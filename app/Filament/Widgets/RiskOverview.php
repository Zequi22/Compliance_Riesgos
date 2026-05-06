<?php

namespace App\Filament\Widgets;

use App\Models\Action;
use App\Models\Risk;
use App\Models\RiskDocument;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RiskOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalRiesgos = Risk::count();
        $enTratamiento = Risk::where('status', Risk::STATUS_TRATAMIENTO)->count();
        $pendientes = Action::whereNotIn('status', [Action::STATUS_CERRADA, Action::STATUS_CANCELADA])->count();
        // Cálculo para los riesgos críticos o altos utilizando consulta optimizada
        $range = [10, 25]; // Alto y Crítico
        $totalAltos = Risk::where(function ($q) use ($range) {
            $q->whereHas('assessments', function ($q2) use ($range) {
                $q2->where('type', 'residual')->whereBetween('score', $range);
            })->orWhere(function ($q3) use ($range) {
                $q3->whereDoesntHave('assessments', fn($q4) => $q4->where('type', 'residual'))
                    ->whereHas('assessments', function ($q5) use ($range) {
                        $q5->where('type', 'inherent')->whereBetween('score', $range);
                    });
            });
        })->count();

        return [
            Stat::make('Total de Riesgos', $totalRiesgos)
                ->icon('heroicon-o-clipboard-document-list')
                ->color('gray')
                ->extraAttributes(['class' => 'stat-gray']),

            Stat::make('Nivel Alto', $totalAltos)
                ->icon('heroicon-o-exclamation-triangle')
                ->description($totalAltos > 0 ? 'Requiere atención inmediata' : null)
                ->descriptionIcon($totalAltos > 0 ? 'heroicon-m-fire' : null)
                ->color('danger')
                ->extraAttributes(['class' => 'stat-danger']),

            Stat::make('Riesgos en Tratamiento', $enTratamiento)
                ->icon('heroicon-o-shield-exclamation')
                ->description($enTratamiento > 0 ? 'En proceso de tratamiento' : null)
                ->descriptionIcon($enTratamiento > 0 ? 'heroicon-m-arrow-trending-up' : null)
                ->color('warning')
                ->extraAttributes(['class' => 'stat-warning']),

            Stat::make('Acciones Pendientes', $pendientes)
                ->icon('heroicon-o-check-circle')
                ->description($pendientes > 0 ? 'Tareas activas del Plan de Acción' : null)
                ->descriptionIcon($pendientes > 0 ? 'heroicon-m-check-badge' : null)
                ->color('success')
                ->extraAttributes(['class' => 'stat-success']),

            Stat::make('Revisiones Vencidas', Risk::where('next_review_at', '<', now())->count())
                ->icon('heroicon-o-clock')
                ->description('Riesgos que requieren revisión')
                ->color(fn() => Risk::where('next_review_at', '<', now())->count() > 0 ? 'danger' : 'gray'),

            Stat::make('Evidencias', RiskDocument::count())
                ->icon('heroicon-o-document-text')
                ->color('gray')
                ->extraAttributes(['class' => 'stat-gray']),
        ];
    }
}
