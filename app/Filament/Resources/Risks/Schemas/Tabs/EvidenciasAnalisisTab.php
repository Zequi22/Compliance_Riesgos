<?php

namespace App\Filament\Resources\Risks\Schemas\Tabs;

use App\Models\Risk;
use App\Models\RiskDocument;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\HtmlString;

/**
 * Tab 3 — Evidencias y Análisis
 * Dos secciones:
 *  - Documentos adjuntos: lista de evidencias vinculadas al riesgo, sus controles y acciones.
 *  - Análisis de riesgo: repeater de evaluaciones (inherente / residual) con cálculo del score en tiempo real.
 */
class EvidenciasAnalisisTab
{
    public static function make(): Tab
    {
        return Tab::make('3. Evidencias y Análisis')
            ->icon('heroicon-m-document-text')
            ->schema([
                Section::make('Documentos Adjuntos')
                    ->description('Evidencias y documentación soporte del riesgo.')
                    ->icon('heroicon-m-document-arrow-up')
                    ->iconColor('info')
                    ->schema([
                        Placeholder::make('documents_list')
                            ->label('Listado de Evidencias')
                            ->content(function ($record) {
                                // $record = el Risk de BD; si aún no existe pedimos que se guarde primero
                                if (!$record) return 'Guarde el riesgo para vincular evidencias.';

                                /** @var \App\Models\Risk $record */
                                $docs = $record->documents()->with(['control', 'action'])->get();

                                if ($docs->isEmpty()) {
                                    return new HtmlString('
                                        <div class="text-sm text-gray-500 italic">No hay evidencias cargadas para este riesgo.</div>
                                    ');
                                }

                                $html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2">';
                                foreach ($docs as $doc) {
                                    $url = "/app/risk-documents/{$doc->id}/edit";

                                    // Indicamos si la evidencia pertenece a un control, una acción o es general del riesgo
                                    $origin = 'Riesgo (General)';
                                    if ($doc->control_id) {
                                        $origin = "Control: " . (optional($doc->control)->title ?? 'ID: ' . $doc->control_id);
                                    } elseif ($doc->action_id) {
                                        $origin = "Acción: " . (optional($doc->action)->title ?? 'ID: ' . $doc->action_id);
                                    }

                                    $statusLabel = match ($doc->status) {
                                        'validada'  => '<span class="px-2 py-0.5 text-[10px] font-bold bg-green-100 text-green-700 rounded-full uppercase">Validada</span>',
                                        'rechazada' => '<span class="px-2 py-0.5 text-[10px] font-bold bg-red-100 text-red-700 rounded-full uppercase">Rechazada</span>',
                                        default     => '<span class="px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-700 rounded-full uppercase">Pendiente</span>',
                                    };

                                    $html .= "
                                        <a href='{$url}' target='_blank' class='flex items-center justify-between p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:border-primary-500 transition-all shadow-sm group'>
                                            <div class='flex items-center gap-3 overflow-hidden'>
                                                <div class='p-2 bg-gray-50 dark:bg-gray-700 rounded-lg text-gray-400 group-hover:text-primary-500'>
                                                    <svg class='w-5 h-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' /></svg>
                                                </div>
                                                <div class='flex flex-col overflow-hidden'>
                                                    <span class='text-sm font-semibold truncate'>{$doc->title}</span>
                                                    <span class='text-[11px] text-primary-600 font-medium uppercase'>{$origin}</span>
                                                    <div class='flex items-center gap-1 text-[10px] text-gray-400 uppercase italic'>
                                                        <span>{$doc->document_type}</span>
                                                        <span>•</span>
                                                        <span>Subido por: " . ($doc->uploadedBy ? "{$doc->uploadedBy->name} {$doc->uploadedBy->last_name}" : 'Sistema') . "</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class='flex items-center gap-2'>
                                                {$statusLabel}
                                                <svg class='w-4 h-4 text-gray-300 group-hover:text-primary-500 transition-colors' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5l7 7-7 7' /></svg>
                                            </div>
                                        </a>";
                                }
                                $html .= '</div>';

                                return new HtmlString($html);
                            }),
                    ]),

                Section::make('Análisis de riesgo')
                    ->description('Evaluaciones numéricas inherentes y residuales de probabilidad e impacto.')
                    ->icon('heroicon-m-chart-bar')
                    ->iconColor('warning')
                    ->collapsed()
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('assessments')
                            ->relationship('assessments')
                            ->live()
                            ->hiddenLabel()
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => ucfirst($state['type'] ?? 'evaluación') . ' - ' . ($state['assessed_at'] ?? 'Sin fecha'))
                            ->grid(3)
                            ->disabled(fn($get) => $get('status') === Risk::STATUS_CERRADO)
                            ->schema(function () {
                                // ─────────────────────────────────────────────────────────
                                // $updateCalculations: se ejecuta cada vez que el usuario
                                // cambia probabilidad, algún impacto, tipo de evaluación
                                // o el nivel de gestión estimado.
                                // Su objetivo es recalcular en tiempo real los campos
                                // 'impact' y 'score' sin necesidad de guardar el formulario.
                                // ─────────────────────────────────────────────────────────
                                $updateCalculations = function ($get, $set, \Livewire\Component $livewire) {
                                    // $get('campo') lee el valor actual de un campo dentro de este repeater
                                    // Usamos ?: 1 para evitar multiplicar por 0 cuando el campo aún no se ha tocado
                                    $eco  = (int) $get('economic_impact')     ?: 1;
                                    $ope  = (int) $get('operational_impact')  ?: 1;
                                    $rep  = (int) $get('reputational_impact') ?: 1;
                                    $prob = (int) $get('probability')         ?: 1;
                                    $mgnt = (int) $get('management_level')    ?: 0; // gestión estimada manualmente (%)
                                    $type = $get('type'); // 'inherent' o 'residual'

                                    // El impacto final es el peor de los tres tipos (el máximo)
                                    $impactoTotal = max($eco, $ope, $rep);
                                    $set('impact', $impactoTotal); // actualiza el campo "Impacto Máximo" en el form

                                    // Puntuación base = probabilidad × impacto (riesgo inherente puro)
                                    $baseScore = $prob * $impactoTotal;

                                    if ($type === 'residual') {
                                        // Para el score residual necesitamos la efectividad real de los controles.
                                        // $livewire es el componente Livewire de la página; con getRecord()
                                        // obtenemos el modelo Risk que se está editando (null si es nuevo).
                                        $effectiveness = 0;
                                        $controlsCount = 0;

                                        $record = method_exists($livewire, 'getRecord') ? $livewire->getRecord() : null;
                                        // $record = el objeto Risk guardado en BD (o null si aún no se ha creado)
                                        if ($record instanceof Risk) {
                                            $effectiveness = $record->getAverageControlEffectiveness(); // 0-100 %
                                            // getAverageControlEffectiveness() ya carga $record->controls; usamos ->controls (propiedad) para no lanzar un COUNT extra
                                            $controlsCount = $record->controls->count();
                                        }

                                        // Si hay controles definidos usamos su efectividad real;
                                        // si no, usamos el porcentaje estimado manualmente (management_level)
                                        $reductionRatio = ($controlsCount > 0)
                                            ? ($effectiveness / 100)
                                            : ($mgnt / 100);

                                        // El ratio debe estar entre 0 y 1 para que la fórmula tenga sentido
                                        $reductionRatio = max(0, min(1, $reductionRatio));

                                        // Score residual = base × (1 − reducción), redondeado hacia arriba
                                        $set('score', (int) ceil($baseScore * (1 - $reductionRatio)));
                                    } else {
                                        // Riesgo inherente: sin reducción, el score es solo probabilidad × impacto
                                        $set('score', $baseScore);
                                    }
                                };

                                $levelOptions = [
                                    1 => '1 - Muy Bajo / Mínimo',
                                    2 => '2 - Bajo',
                                    3 => '3 - Medio / Moderado',
                                    4 => '4 - Alto',
                                    5 => '5 - Muy Alto / Máximo',
                                ];

                                return [
                                    Grid::make(2)
                                        ->schema([
                                            Group::make()->schema([
                                                Select::make('type')
                                                    ->label('Tipo de Evaluación')
                                                    ->options(['inherent' => 'Inherente', 'residual' => 'Residual'])
                                                    ->required()->live()->afterStateUpdated($updateCalculations),

                                                Select::make('probability')
                                                    ->label('Probabilidad')
                                                    ->options($levelOptions)
                                                    ->required()->default(1)->live()->afterStateUpdated($updateCalculations),
                                            ])->columns(2)->columnSpanFull(),

                                            Group::make()->schema([
                                                TextInput::make('management_level')
                                                    ->label('Gestión Estimada (%)')
                                                    ->numeric()->minValue(0)->maxValue(100)->suffix('%')
                                                    ->visible(fn($get) => $get('type') === 'residual')
                                                    ->required(fn($get) => $get('type') === 'residual')
                                                    ->live(onBlur: true)->afterStateUpdated($updateCalculations)
                                                    ->columnSpan(1),

                                                TextInput::make('real_effectiveness')
                                                    ->label('Gestión Real (%)')
                                                    ->disabled()
                                                    ->dehydrated(false)
                                                    ->suffix('%')
                                                    ->visible(fn($get) => $get('type') === 'residual')
                                                    ->formatStateUsing(function (\Livewire\Component $livewire) {
                                                        // Muestra la efectividad media real calculada desde los controles del riesgo
                                                        $record = method_exists($livewire, 'getRecord') ? $livewire->getRecord() : null;
                                                        return $record instanceof Risk ? $record->getAverageControlEffectiveness() : 0;
                                                    })
                                                    ->columnSpan(1),
                                            ])->columns(2)->columnSpanFull(),
                                        ]),

                                    Section::make('Impactos Específicos')
                                        ->compact()->columns(3)
                                        ->schema([
                                            Select::make('economic_impact')->label('Económico')->options($levelOptions)->required()->default(1)->live()->afterStateUpdated($updateCalculations),
                                            Select::make('operational_impact')->label('Operacional')->options($levelOptions)->required()->default(1)->live()->afterStateUpdated($updateCalculations),
                                            Select::make('reputational_impact')->label('Reputacional')->options($levelOptions)->required()->default(1)->live()->afterStateUpdated($updateCalculations),
                                        ]),

                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('impact')->label('Impacto Máximo')->disabled()->dehydrated()->extraInputAttributes(['class' => 'bg-gray-50 font-bold']),
                                            TextInput::make('score')->label('Score Total')->disabled()->dehydrated()->extraInputAttributes(['class' => 'bg-orange-50 font-bold text-orange-700']),
                                        ]),

                                    DatePicker::make('assessed_at')->label('Fecha de Evaluación')->default(now())->required()->columnSpanFull(),
                                ];
                            })
                            ->addActionLabel('Añadir nueva evaluación'),
                    ]),
            ]);
    }
}
