<?php

namespace App\Filament\Pages;

use App\Models\Action;
use App\Models\Control;
use App\Models\OrganizationalUnit;
use App\Models\Risk;
use App\Models\RiskDocument;
use BackedEnum;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Torgodly\Html2Media\Actions\Html2MediaAction;

class ExecutiveReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Informe Ejecutivo';
    protected static ?string $title = 'Informe Ejecutivo por Periodo';
    protected static ?int $navigationSort = 10;
    protected string $view = 'filament.pages.executive-report';

    // Livewire properties — reactive filter
    public ?string $date_from = null;
    public ?string $date_to   = null;

    public function mount(): void
    {
        $this->date_from = now()->startOfMonth()->format('Y-m-d');
        $this->date_to   = now()->endOfMonth()->format('Y-m-d');
    }

    protected function getHeaderActions(): array
    {
        return [
            Html2MediaAction::make('descargar_pdf')
                ->label('Descargar Informe PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->savePdf()
                ->print()
                ->preview()
                ->orientation('portrait')
                ->format('a4')
                ->margins(8, 8, 8, 8)
                ->filename(fn () => 'Informe_Ejecutivo_' . ($this->date_from ?? 'completo') . '_a_' . ($this->date_to ?? 'hoy'))
                ->content(fn () => new \Illuminate\Support\HtmlString(
                    view(
                        'pdf.executive-report-pdf',
                        $this->getReportData($this->date_from, $this->date_to)
                    )->render()
                )),
        ];
    }

    protected function getViewData(): array
    {
        return $this->getReportData($this->date_from, $this->date_to);
    }

    public function getReportData(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $from = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : null;
        $to   = $dateTo   ? Carbon::parse($dateTo)->endOfDay()     : null;

        // ── 1. TOP RIESGOS POR CRITICIDAD ────────────────────────────────────
        $topRisks = Risk::with(['assessments', 'organizationalUnit', 'responsable'])
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to,   fn ($q) => $q->where('created_at', '<=', $to))
            ->get()
            ->filter(fn ($r) => $r->criticality !== 'No Evaluado')
            ->sortByDesc(fn ($r) => $r->assessments->where('type', 'residual')->sortByDesc('assessed_at')->first()?->score
                ?? $r->assessments->where('type', 'inherent')->sortByDesc('assessed_at')->first()?->score ?? 0)
            ->take(15)->values();

        // ── 2. RIESGOS SIN REVISIÓN (VENCIDOS) ───────────────────────────────
        $overdueRisks = Risk::with(['organizationalUnit', 'responsable'])
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '<', now())
            ->whereNotIn('status', [Risk::STATUS_CERRADO])
            ->when($from && $to, fn ($q) => $q->whereBetween('next_review_at', [$from, $to]))
            ->orderBy('next_review_at')
            ->get();

        // ── 3. ACCIONES VENCIDAS Y BLOQUEADAS ────────────────────────────────
        $overdueActions = Action::with(['risk', 'responsable'])
            ->where(fn ($q) => $q
                ->where('status', Action::STATUS_BLOQUEADA)
                ->orWhere(fn ($q2) => $q2
                    ->whereNotNull('commitment_date')
                    ->where('commitment_date', '<', now())
                    ->whereNotIn('status', [Action::STATUS_CERRADA, Action::STATUS_CANCELADA])
                )
            )
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to,   fn ($q) => $q->where('created_at', '<=', $to))
            ->orderByRaw("status = 'bloqueada' DESC")
            ->orderBy('commitment_date')
            ->get();

        // ── 4. CONTROLES INSUFICIENTES EN RIESGOS CRÍTICOS ───────────────────
        $criticalRiskIds = Risk::with('assessments')->get()
            ->filter(fn ($r) => ($r->assessments->where('type', 'residual')->sortByDesc('assessed_at')->first()?->score
                ?? $r->assessments->where('type', 'inherent')->sortByDesc('assessed_at')->first()?->score ?? 0) >= 10)
            ->pluck('id');

        $insufficientControls = Control::with(['risk.organizationalUnit', 'responsable'])
            ->where('effectiveness', 'Insuficiente')
            ->whereIn('risk_id', $criticalRiskIds)
            ->get();

        // ── 5. EVIDENCIAS PENDIENTES DE VALIDAR ──────────────────────────────
        $pendingEvidences = RiskDocument::with(['risk', 'uploadedBy'])
            ->whereNull('validated_at')
            ->where(fn ($q) => $q->where('status', 'pendiente')->orWhereNull('status'))
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to,   fn ($q) => $q->where('created_at', '<=', $to))
            ->orderBy('created_at')
            ->get();

        // ── 6. RESUMEN POR UNIDAD ORGANIZATIVA ───────────────────────────────
        $units = OrganizationalUnit::with(['risks.assessments', 'risks.actions', 'risks.controls'])
            ->get()
            ->map(function ($unit) {
                $risks = $unit->risks;
                if ($risks->isEmpty()) return null;
                $allActions  = $risks->flatMap(fn ($r) => $r->actions);
                $allControls = $risks->flatMap(fn ($r) => $r->controls);
                return [
                    'name'                  => $unit->name,
                    'type'                  => $unit->type ?? '—',
                    'total_risks'           => $risks->count(),
                    'critical_risks'        => $risks->filter(fn ($r) => in_array($r->criticality, ['Crítico', 'Alto']))->count(),
                    'overdue_reviews'       => $risks->filter(fn ($r) => $r->isReviewOverdue())->count(),
                    'overdue_actions'       => $allActions->filter(fn ($a) => $a->isOverdue())->count(),
                    'blocked_actions'       => $allActions->filter(fn ($a) => $a->status === Action::STATUS_BLOQUEADA)->count(),
                    'insufficient_controls' => $allControls->filter(fn ($c) => $c->effectiveness === 'Insuficiente')->count(),
                ];
            })
            ->filter()->sortByDesc('critical_risks')->values();

        return [
            'date_from'            => $dateFrom,
            'date_to'              => $dateTo,
            'kpis'                 => [
                'total_risks'             => Risk::count(),
                'critical_risks'          => $criticalRiskIds->count(),
                'overdue_reviews'         => $overdueRisks->count(),
                'overdue_blocked_actions' => $overdueActions->count(),
                'insufficient_controls'   => $insufficientControls->count(),
                'pending_evidences'       => $pendingEvidences->count(),
            ],
            'topRisks'             => $topRisks,
            'overdueRisks'         => $overdueRisks,
            'overdueActions'       => $overdueActions,
            'insufficientControls' => $insufficientControls,
            'pendingEvidences'     => $pendingEvidences,
            'units'                => $units,
            'generatedAt'          => now(),
        ];
    }
}
