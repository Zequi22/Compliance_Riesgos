<?php

namespace App\Filament\Resources\Risks\Schemas\Tabs;

use Carbon\Carbon;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\HtmlString;

/**
 * Tab 5 — Seguimiento e Historial
 * Dos secciones:
 *  - Indicadores de Seguimiento (KRIs): métricas con objetivo, tolerancia y
 *    estado calculado automáticamente. Guarda un historial de mediciones.
 *  - Historial de Cambios: auditoría de transiciones de estado del riesgo,
 *    generada automáticamente por el modelo Risk al detectar cambios.
 */
class SeguimientoHistorialTab
{
    public static function make(): Tab
    {
        return Tab::make('5. Seguimiento e Historial')
            ->icon('heroicon-m-presentation-chart-line')
            ->schema([
                Section::make('Indicadores de Seguimiento')
                    ->description('Métricas KPI/KRI de rendimiento observables en el tiempo para monitorizar si el riesgo excede lo tolerable.')
                    ->icon('heroicon-m-presentation-chart-line')
                    ->iconColor('warning')
                    ->collapsed()
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('indicators')
                            ->relationship('indicators')
                            ->hiddenLabel()
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'Nuevo indicador')
                            ->grid(3)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Indicador')
                                    ->placeholder('Ej: % de cumplimiento de auditoría o Incidentes mensuales')
                                    ->required()
                                    ->maxLength(100)
                                    ->columnSpan(2),

                                TextInput::make('tolerance_level')
                                    ->label('Tolerancia')
                                    ->placeholder('Ej: 90 (Mínimo) o 5 (Máximo)')
                                    ->maxLength(50)
                                    ->columnSpan(1),

                                TextInput::make('target_value')
                                    ->label('Objetivo')
                                    ->placeholder('Ej: 100 o 0')
                                    ->required()
                                    ->maxLength(50)
                                    ->columnSpan(1),

                                TextInput::make('current_value')
                                    ->label('Actual')
                                    ->placeholder('Ej: 95 o 2')
                                    ->required()
                                    ->maxLength(50)
                                    ->live()
                                    ->afterStateUpdated(function ($state, $get, $record, $set) {
                                        // Se dispara cada vez que el usuario cambia el valor actual del indicador.
                                        // $state = el nuevo valor que acaba de escribir el usuario.
                                        // $record = el modelo RiskIndicator guardado en BD (null si es nuevo).
                                        // Solo guardamos en el historial si el indicador ya existe en BD.
                                        if (!$record) return;

                                        // $get('history') lee el JSON del historial acumulado
                                        // Añadimos una nueva entrada con el valor y la fecha/hora actuales
                                        $history   = $get('history') ?? [];
                                        $history[] = [
                                            'value'       => $state,
                                            'measured_at' => now()->toDateTimeString(),
                                        ];
                                        $set('history', $history);       // actualiza el campo history en el form
                                        $set('last_measured_at', now()); // registra cuándo se midió por última vez
                                    })
                                    ->columnSpan(1),

                                Placeholder::make('status')
                                    ->label('Estado')
                                    ->content(function ($get) {
                                        // $get() aquí lee campos dentro del mismo item del repeater de indicadores
                                        $current   = $get('current_value');  // valor medido ahora
                                        $target    = $get('target_value');   // valor que queremos alcanzar
                                        $tolerance = $get('tolerance_level'); // límite máximo aceptable

                                        if (!$current || !$target) {
                                            return new HtmlString('<span class="text-gray-400 font-bold italic">Sin datos</span>');
                                        }

                                        // filter_var extrae solo la parte numérica del string (ej: "95%" → 95)
                                        // Esto permite comparar valores aunque vengan con unidades o texto
                                        $currentNum   = filter_var($current,   FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                        $targetNum    = filter_var($target,    FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                        $toleranceNum = $tolerance
                                            ? filter_var($tolerance, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)
                                            : null;

                                        if (is_numeric($currentNum) && is_numeric($targetNum)) {
                                            // Comparación numérica: el objetivo se considera "por debajo o igual"
                                            if ($currentNum <= $targetNum) {
                                                return new HtmlString('<x-filament::badge color="success" icon="heroicon-m-check-circle">Dentro de Objetivo</x-filament::badge>');
                                            }

                                            // Si el valor actual supera también la tolerancia máxima → alerta crítica
                                            if ($toleranceNum && $currentNum > $toleranceNum) {
                                                return new HtmlString('<x-filament::badge color="danger" icon="heroicon-m-x-circle">Supera Tolerancia</x-filament::badge>');
                                            }

                                            // Supera el objetivo pero no la tolerancia → vigilar
                                            return new HtmlString('<x-filament::badge color="warning" icon="heroicon-m-exclamation-triangle">Alerta de Seguimiento</x-filament::badge>');
                                        }

                                        // Valores cualitativos (texto): comparación directa de strings
                                        return $current === $target
                                            ? new HtmlString('<x-filament::badge color="success">Objetivo Cumplido</x-filament::badge>')
                                            : new HtmlString('<x-filament::badge color="warning">Pendiente</x-filament::badge>');
                                    })
                                    ->columnSpan(1),

                                Section::make('Histórico')
                                    ->collapsed()
                                    ->compact()
                                    ->schema([
                                        Placeholder::make('history_list')
                                            ->hiddenLabel()
                                            ->content(function ($get) {
                                                // $get('history') = array JSON con todas las mediciones pasadas del indicador
                                                $history = $get('history') ?? [];
                                                if (empty($history)) return 'No hay registros históricos.';

                                                // Ordenamos de más reciente a más antiguo y construimos la tabla
                                                $rows = collect($history)->sortByDesc('measured_at')->map(function ($item) {
                                                    $date = Carbon::parse($item['measured_at'])->format('d/m/Y H:i');
                                                    return "<tr><td class='p-1'>{$date}</td><td class='p-1 font-bold'>{$item['value']}</td></tr>";
                                                })->implode('');

                                                return new HtmlString("<table class='w-full text-xs text-left'><thead><tr><th>Fecha</th><th>Valor</th></tr></thead><tbody>{$rows}</tbody></table>");
                                            }),
                                    ])->columnSpan(2),
                            ])
                            ->addActionLabel('+ Añadir Indicador')
                            ->reorderableWithButtons(),
                    ]),

                Section::make('Historial de Cambios')
                    ->icon('heroicon-m-clock')
                    ->iconColor('gray')
                    ->collapsed()
                    ->visible(fn($record) => $record && $record->statusHistories()->exists())
                    ->columnSpanFull()
                    ->schema([
                        // Solo lectura: generado automáticamente por Risk::booted() al cambiar el status
                        Repeater::make('statusHistories')
                            ->relationship('statusHistories')
                            ->hiddenLabel()->addable(false)->deletable(false)->reorderable(false)->columns(3)
                            ->schema([
                                TextInput::make('old_status')->label('Anterior')->disabled(),
                                TextInput::make('new_status')->label('Nuevo')->disabled(),
                                TextInput::make('created_at')->label('Fecha')->disabled(),
                            ]),
                    ]),
            ]);
    }
}
