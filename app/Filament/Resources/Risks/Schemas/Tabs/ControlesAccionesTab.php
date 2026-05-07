<?php

namespace App\Filament\Resources\Risks\Schemas\Tabs;

use App\Models\Action;
use App\Models\Risk;
use App\Models\RiskDocument;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action as FilamentAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\HtmlString;

/**
 * Tab 4 — Controles y Acciones
 * Dos secciones:
 *  - Controles Existentes: medidas de mitigación activas (máx. 5 por riesgo),
 *    con semáforo de vencimiento por fecha límite.
 *  - Acciones de Mejora: plan de tratamiento futuro. Las acciones se crean desde
 *    su propio módulo; aquí se consultan y actualizan en modo embebido (addable: false).
 */
class ControlesAccionesTab
{
    public static function make(): Tab
    {
        return Tab::make('4. Controles y Acciones')
            ->icon('heroicon-m-shield-check')
            ->schema([
                Section::make('Controles Existentes')
                    ->description('Controles ya implementados. Registre únicamente las medidas que están vigentes y operativas para mitigar este riesgo.')
                    ->icon('heroicon-m-check-circle')
                    ->iconColor('success')
                    ->collapsed()
                    ->columnSpanFull()
                    ->schema([
                        Placeholder::make('controls_summary_header')
                            ->hiddenLabel()
                            ->content(function (Get $get) {
                                // $get('controls') devuelve cuántos items hay actualmente en el repeater
                                $count = count($get('controls') ?? []);
                                $limit = 5;

                                $bgClass = $count >= $limit
                                    ? 'bg-danger-50 text-danger-700 border-danger-200 dark:bg-danger-400/10 dark:text-danger-400 dark:border-danger-400/20'
                                    : 'bg-info-50 text-info-700 border-info-200 dark:bg-info-400/10 dark:text-info-400 dark:border-info-400/20';

                                $badge = "<span class='inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold border {$bgClass}'>
                                            {$count} / {$limit} Controles
                                          </span>";

                                $message = $count >= $limit
                                    ? "<span class='ml-3 text-xs text-danger-600 font-bold'>⚠️ Límite alcanzado. No se permiten más controles por riesgo.</span>"
                                    : "<span class='ml-3 text-xs text-gray-500 italic'>Se sugiere no exceder los {$limit} controles para mantener la claridad.</span>";

                                return new HtmlString("<div class='flex items-center mb-3 p-2 bg-gray-50/50 dark:bg-white/5 rounded-lg border border-gray-100 dark:border-white/10'>{$badge} {$message}</div>");
                            })
                            ->columnSpanFull(),

                        Repeater::make('controls')
                            ->relationship('controls', fn($query) => $query->orderBy('due_date', 'asc'))
                            ->live()
                            ->maxItems(5)
                            ->hiddenLabel()
                            ->collapsible()
                            ->itemLabel(function (array $state, $uuid, $component): ?string {
                                // Generamos el título del item con su número de orden y título
                                $items = $component->getState();
                                $keys  = array_keys($items);
                                $index = array_search($uuid, $keys) + 1;
                                return "Control #{$index}: " . ($state['title'] ?? 'Nuevo control');
                            })
                            ->grid(3)
                            ->disabled(fn($get) => $get('status') === Risk::STATUS_CERRADO)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Título')
                                    ->required()
                                    ->placeholder('Nombre de la actividad de control')
                                    ->columnSpan(3),

                                Select::make('type')
                                    ->label('Tipo')
                                    ->options(['Preventivo' => 'Preventivo', 'Reactivo' => 'Reactivo'])
                                    ->columnSpan(2),
                                Select::make('frequency')
                                    ->label('Frecuencia')
                                    ->options(['Diaria' => 'Diaria', 'Semanal' => 'Semanal', 'Mensual' => 'Mensual', 'Anual' => 'Anual'])
                                    ->columnSpan(1),
                                Select::make('effectiveness')
                                    ->label('Efectividad')
                                    ->options(['Insuficiente' => 'Insuficiente', 'Medio' => 'Medio', 'Suficiente' => 'Suficiente'])
                                    ->live()
                                    ->columnSpan(2),

                                DatePicker::make('due_date')
                                    ->label('Fecha Límite')
                                    ->placeholder('DD/MM/AAAA')
                                    ->native(false)
                                    ->live() // live() para que hint/hintColor/hintIcon se recalculen al cambiar la fecha
                                    ->hint(fn(Get $get) => self::dueDateStatus($get('due_date'))['hint'])
                                    ->hintColor(fn(Get $get) => self::dueDateStatus($get('due_date'))['color'])
                                    ->hintIcon(fn(Get $get) => self::dueDateStatus($get('due_date'))['icon'])
                                    ->columnSpan(1),

                                Textarea::make('description')
                                    ->label('Descripción')
                                    ->rows(3)
                                    ->columnSpan(3),

                                // Por defecto hereda el responsable del riesgo padre (../../responsable_id sube dos niveles)
                                Select::make('responsable_id')
                                    ->label('Responsable')
                                    ->searchable()
                                    ->options(fn() => self::getUserOptions())
                                    ->default(fn(Get $get) => $get('../../responsable_id'))
                                    ->columnSpan(3),

                                Select::make('organizational_unit_id')
                                    ->label('Área / Unidad')
                                    ->relationship('organizationalUnit', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(fn(Get $get) => $get('../../organizational_unit_id'))
                                    ->columnSpan(3),

                                Placeholder::make('evidences_list')
                                    ->label('Evidencias Asociadas')
                                    ->content(function ($record) {
                                        // $record aquí es el modelo Control (item del repeater guardado en BD)
                                        if (!$record || !$record->id) return 'Guarde el control para ver las evidencias.';

                                        $docs = RiskDocument::where('control_id', $record->id)->get();
                                        if ($docs->isEmpty()) return 'Sin evidencias vinculadas.';

                                        $html = '<div class="flex flex-col gap-1 mt-1">';
                                        foreach ($docs as $doc) {
                                            $url         = "/app/risk-documents/{$doc->id}/edit";
                                            $statusColor = match ($doc->status) {
                                                'validada'  => 'text-green-600',
                                                'rechazada' => 'text-red-600',
                                                default     => 'text-amber-600',
                                            };
                                            $html .= "
                                                <a href='{$url}' target='_blank' class='flex items-center gap-2 text-xs hover:underline bg-gray-50 dark:bg-gray-800 p-1.5 rounded border border-gray-100 dark:border-gray-700'>
                                                    <svg class='w-3 h-3 text-gray-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' /></svg>
                                                    <span class='font-medium truncate flex-1'>{$doc->title}</span>
                                                    <span class='{$statusColor} font-bold text-[9px] uppercase'>" . ucfirst($doc->status) . "</span>
                                                </a>";
                                        }
                                        $html .= '</div>';
                                        return new HtmlString($html);
                                    })
                                    ->columnSpan(3),
                            ])
                            ->afterStateHydrated(function ($record, $set, $get) {
                                // Al cargar el formulario, si el control no tiene responsable heredamos el del riesgo padre
                                if ($record && ! $record->responsable_id) {
                                    $set('responsable_id', $get('../../responsable_id'));
                                }
                            })
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $get) {
                                // Al crear un control sin responsable explícito, usamos el del riesgo
                                if (! isset($data['responsable_id'])) {
                                    $data['responsable_id'] = $get('../../responsable_id');
                                }
                                return $data;
                            })
                            ->addActionLabel('+ Añadir Control')
                            ->reorderableWithButtons(),
                    ]),

                Section::make('Acciones de Mejora')
                    ->description('Medidas correctivas y mejoras futuras. Acciones a ejecutar para reforzar controles o implementar nuevas medidas.')
                    ->icon('heroicon-m-arrow-path')
                    ->iconColor('info')
                    ->collapsed()
                    ->columnSpanFull()
                    ->schema([
                        // Botón que redirige al módulo de acciones para crear una nueva (abre en pestaña nueva)
                        \Filament\Schemas\Components\Actions::make([
                            FilamentAction::make('go_to_create_action')
                                ->label('Nueva Acción')
                                ->icon('heroicon-m-plus-circle')
                                ->color('primary')
                                ->url(
                                    fn(?Risk $record) => $record
                                        ? route('filament.admin.resources.actions.create', ['risk_id' => $record->id])
                                        : route('filament.admin.resources.actions.create')
                                )
                                ->openUrlInNewTab(),
                        ])->columnSpanFull(),

                        Repeater::make('actions')
                            ->relationship('actions')
                            ->live()
                            ->hiddenLabel()
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['title'] ?? 'Nueva acción')
                            ->grid(3)
                            ->disabled(fn($get) => $get('status') === Risk::STATUS_CERRADO)
                            ->addable(false) // Las acciones se crean desde su módulo propio; aquí solo se editan
                            ->schema([
                                TextInput::make('title')
                                    ->label('Acción')
                                    ->required()
                                    ->columnSpan(3),
                                DatePicker::make('due_date')
                                    ->label('Fecha límite')
                                    ->required()
                                    ->columnSpan(2),

                                Select::make('status')
                                    ->label('Estado')
                                    ->options(Action::getStatusOptions())
                                    ->required()
                                    ->rule(function ($get, $record) {
                                        // Regla personalizada: impide cerrar una acción sin evidencias adjuntas.
                                        // $value = estado elegido; $fail('msg') detiene el guardado mostrando el error.
                                        return function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                            if ($value === Action::STATUS_CERRADA) {
                                                $actionId = $get('id'); // ID de la acción dentro del repeater
                                                if ($actionId) {
                                                    $action = Action::find($actionId);
                                                    if ($action && !$action->hasEvidence()) {
                                                        $fail('No puedes cerrar una acción sin al menos una evidencia vinculada.');
                                                    }
                                                } else {
                                                    $fail('Primero debe guardar la acción y adjuntar una evidencia para poder cerrarla.');
                                                }
                                            }
                                        };
                                    })
                                    ->columnSpan(1),

                                Select::make('responsable_id')
                                    ->label('Responsable')
                                    ->searchable()
                                    ->options(fn() => self::getUserOptions())
                                    ->default(fn(Get $get) => $get('../../responsable_id'))
                                    ->columnSpan(3),

                                Select::make('organizational_unit_id')
                                    ->label('Área / Unidad')
                                    ->relationship('organizationalUnit', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(fn(Get $get) => $get('../../organizational_unit_id'))
                                    ->columnSpan(3),

                                Textarea::make('notes')
                                    ->label('Notas')
                                    ->columnSpan(3),

                                Placeholder::make('documents_list')
                                    ->label('Documentos y Evidencias')
                                    ->content(function (Get $get, $record) {
                                        // $get('id') = ID de la acción en el repeater; null si aún no se ha guardado
                                        $actionId = $get('id');
                                        if (!$actionId) {
                                            return new HtmlString('<div class="text-xs text-gray-500 italic">Guarde la acción para poder vincular evidencias.</div>');
                                        }

                                        $evidences = RiskDocument::where('action_id', $actionId)->get();

                                        $html = '<div class="flex flex-col gap-2 mt-2">';

                                        if ($evidences->isEmpty()) {
                                            $html .= '<div class="text-xs text-gray-400 italic mb-2">No hay evidencias asociadas.</div>';
                                        } else {
                                            foreach ($evidences as $doc) {
                                                $editUrl     = "/app/risk-documents/{$doc->id}/edit";
                                                $title       = htmlspecialchars($doc->title, ENT_QUOTES);
                                                $statusLabel = match ($doc->status) {
                                                    'validada'  => '<span class="text-[9px] font-bold text-green-600 uppercase">Validada</span>',
                                                    'rechazada' => '<span class="text-[9px] font-bold text-red-600 uppercase">Rechazada</span>',
                                                    default     => '<span class="text-[9px] font-bold text-amber-600 uppercase">Pendiente</span>',
                                                };

                                                $html .= "
                                                    <div class='flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-100 dark:border-gray-700'>
                                                        <div class='flex items-center gap-2 overflow-hidden'>
                                                            <svg class='w-3.5 h-3.5 text-gray-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' /></svg>
                                                            <span class='text-xs font-medium truncate' title='{$title}'>{$title}</span>
                                                        </div>
                                                        <div class='flex items-center gap-3'>
                                                            {$statusLabel}
                                                            <a href='{$editUrl}' target='_blank' class='text-[10px] font-bold text-primary-600 hover:text-primary-500 underline'>VER</a>
                                                        </div>
                                                    </div>";
                                            }
                                        }

                                        // $record es el modelo Action; su risk_id construye la URL de nueva evidencia
                                        $riskId  = $record?->risk_id;
                                        $addUrl  = "/app/risk-documents/create?action_id={$actionId}&risk_id={$riskId}&classification=action_evidence";
                                        $goToUrl = "/app/actions/{$actionId}/edit";

                                        $html .= "
                                            <div class='flex flex-wrap gap-2 mt-2'>
                                                <a href='{$addUrl}' target='_blank' class='inline-flex items-center justify-center gap-1 px-2.5 py-1.5 text-[10px] font-bold text-white bg-primary-600 rounded-lg hover:bg-primary-500 shadow-sm transition-all'>
                                                    <svg class='w-3 h-3' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 4v16m8-8H4' /></svg>
                                                    AÑADIR EVIDENCIA
                                                </a>
                                                <a href='{$goToUrl}' target='_blank' class='inline-flex items-center justify-center gap-1 px-2.5 py-1.5 text-[10px] font-bold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 shadow-sm transition-all dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600'>
                                                    <svg class='w-3 h-3' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14' /></svg>
                                                    IR A LA ACCIÓN (DETALLE)
                                                </a>
                                            </div>
                                        </div>";

                                        return new HtmlString($html);
                                    })
                                    ->columnSpan(3),
                            ])
                            ->afterStateHydrated(function ($record, $set, $get) {
                                if ($record && ! $record->responsable_id) {
                                    $set('responsable_id', $get('../../responsable_id'));
                                }
                            })
                            ->reorderableWithButtons(),
                    ]),
            ]);
    }

    /**
     * Calcula el estado de una fecha límite y devuelve hint, color e icono en un solo paso.
     * Evita llamar Carbon::parse() tres veces separadas (una por hint, hintColor e hintIcon).
     *
     * @return array{hint: string|null, color: string|null, icon: string|null}
     */
    private static function dueDateStatus(?string $date): array
    {
        if (! $date) return ['hint' => null, 'color' => null, 'icon' => null];

        $carbon = Carbon::parse($date);

        if ($carbon->isPast()) {
            return ['hint' => 'Vencido', 'color' => 'danger', 'icon' => 'heroicon-m-exclamation-circle'];
        }

        $diff = (int) Carbon::today()->diffInDays($carbon);
        if ($diff <= 15) {
            return ['hint' => "Próximo ({$diff} días)", 'color' => 'warning', 'icon' => 'heroicon-m-clock'];
        }

        return ['hint' => 'En plazo', 'color' => 'success', 'icon' => 'heroicon-m-check-circle'];
    }

    /**
     * Genera las opciones del selector de responsable.
     * Centraliza User::all()->mapWithKeys() para no duplicarlo en controles y en acciones.
     */
    private static function getUserOptions(): array
    {
        return User::all()->mapWithKeys(fn($user) => [
            $user->id => "{$user->name} {$user->last_name} ({$user->job_title})",
        ])->all();
    }
}
