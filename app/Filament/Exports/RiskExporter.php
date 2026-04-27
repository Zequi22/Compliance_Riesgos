<?php

namespace App\Filament\Exports;

use App\Models\Risk;
use App\Models\Action;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class RiskExporter extends Exporter
{
    protected static ?string $model = Risk::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('organizationalUnit.name')
                ->label('Unidad / Proceso'),
            ExportColumn::make('responsable_fullname')
                ->label('Responsable')
                ->state(fn ($record) => ($record->responsable?->name ?? '') . ' ' . ($record->responsable?->last_name ?? '')),
            ExportColumn::make('name')
                ->label('Riesgo'),
            ExportColumn::make('treatment')
                ->label('Tratamiento')
                ->formatStateUsing(fn (?string $state): string => $state ? ucfirst($state) : '-'),
            ExportColumn::make('status')
                ->label('Estado'),
            ExportColumn::make('criticality')
                ->label('Criticidad')
                ->state(fn ($record) => $record->criticality),
            
            // --- Evaluaciones ---
            ExportColumn::make('prob_impact_val')
                ->label('Prob / Impacto')
                ->state(function ($record) {
                    $assessment = $record->assessments()->latest('assessed_at')->first();
                    if (!$assessment) return '-';
                    return ($assessment->probability ?? '-') . ' / ' . ($assessment->impact ?? '-');
                }),
            ExportColumn::make('residual_score_val')
                ->label('Score Residual')
                ->state(function ($record) {
                    return $record->assessments()
                        ->where('type', 'residual')
                        ->latest('assessed_at')
                        ->first()?->score ?? '-';
                }),

            // --- Controles ---
            ExportColumn::make('controls_total')
                ->label('Controles')
                ->state(fn ($record) => $record->controls()->count()),
            ExportColumn::make('insufficient_controls_total')
                ->label('# Ctrl. Insuficientes')
                ->state(fn ($record) => $record->controls()->where('effectiveness', 'Insuficiente')->count()),

            // --- Acciones ---
            ExportColumn::make('open_actions_total')
                ->label('Acciones Abiertas')
                ->state(fn ($record) => $record->actions()->where('status', '!=', Action::STATUS_CERRADA)->count()),
            ExportColumn::make('overdue_actions_total')
                ->label('# Acciones Vencidas')
                ->state(fn ($record) => $record->actions()
                    ->where('status', '!=', Action::STATUS_CERRADA)
                    ->get()
                    ->filter(fn($action) => $action->isOverdue())
                    ->count()
                ),

            // --- Evidencias ---
            ExportColumn::make('evidences_total')
                ->label('Evidencias')
                ->state(fn ($record) => $record->documents()->count()),
            ExportColumn::make('validated_evidences_percent')
                ->label('% Validadas')
                ->state(function ($record) {
                    $total = $record->documents()->count();
                    if ($total === 0) return '0%';
                    $valid = $record->documents()->where('status', 'validada')->count();
                    return (int)(($valid / $total) * 100) . '%';
                }),

            ExportColumn::make('next_review_at')
                ->label('Próxima Revisión')
                ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y') : '-'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Su exportación global de riesgos se ha completado y ' . number_format($export->successful_rows) . ' ' . str('fila')->plural($export->successful_rows) . ' exportadas correctamente.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' fallaron.';
        }

        return $body;
    }
}
