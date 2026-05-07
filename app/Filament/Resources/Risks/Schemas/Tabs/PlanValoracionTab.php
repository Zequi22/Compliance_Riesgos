<?php

namespace App\Filament\Resources\Risks\Schemas\Tabs;

use App\Models\Action;
use App\Models\Risk;
use App\Models\RiskDocument;
use Carbon\Carbon;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

/**
 * Tab 2 — Plan y Valoración
 * Panel de resumen ejecutivo del riesgo: muestra en tiempo real la criticidad,
 * el tratamiento elegido, su coherencia con las acciones abiertas, y un desglose
 * cuantitativo de controles y acciones. Solo visible cuando el riesgo ya está guardado.
 */
class PlanValoracionTab
{
    public static function make(): Tab
    {
        return Tab::make('2. Plan y Valoración')
            ->icon('heroicon-m-clipboard-document-check')
            ->schema([
                Section::make('Plan de Gestión del Riesgo')
                    ->description('Monitorización del estado global: criticidad, tiempos de respuesta y planes de acción registrados.')
                    ->icon('heroicon-m-clipboard-document-check')
                    ->iconColor('success')
                    ->visible(fn($record) => $record !== null) // Solo se muestra si el riesgo ya existe en BD
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Fieldset::make('Estado y Criticidad')
                                    ->schema([
                                        Placeholder::make('criticality_status')
                                            ->label('Nivel de Criticidad')
                                            ->content(function (Get $get) {
                                                // $get('assessments') devuelve el array de evaluaciones
                                                // tal como están en el repeater del formulario (no desde BD)
                                                $assessments = $get('assessments') ?? [];
                                                if (empty($assessments)) {
                                                    return new HtmlString(Blade::render('<x-filament::badge color="gray">No Evaluado</x-filament::badge>'));
                                                }

                                                // Priorizamos la evaluación residual más reciente; si no hay, usamos la inherente
                                                $sorted     = collect($assessments)->sortByDesc('assessed_at');
                                                $assessment = $sorted->firstWhere('type', 'residual') ?: $sorted->firstWhere('type', 'inherent');

                                                // $assessment es un array porque viene del estado del repeater, no un objeto
                                                $score = $assessment['score'] ?? null;
                                                if (!$score) {
                                                    return new HtmlString(Blade::render('<x-filament::badge color="gray">No Evaluado</x-filament::badge>'));
                                                }

                                                // Escala: 1-2 Muy Bajo | 3-4 Bajo | 5-9 Medio | 10-14 Alto | 15+ Crítico
                                                $label = match (true) {
                                                    $score <= 2  => 'Muy Bajo',
                                                    $score <= 4  => 'Bajo',
                                                    $score <= 9  => 'Medio',
                                                    $score <= 14 => 'Alto',
                                                    default      => 'Crítico',
                                                };

                                                $color = match ($label) {
                                                    'Muy Bajo' => 'success',
                                                    'Bajo'     => 'info',
                                                    'Medio'    => 'warning',
                                                    default    => 'danger', // Alto y Crítico
                                                };

                                                $icon = match ($color) {
                                                    'danger'  => 'heroicon-m-fire',
                                                    'warning' => 'heroicon-m-exclamation-triangle',
                                                    'info'    => 'heroicon-m-information-circle',
                                                    'success' => 'heroicon-m-shield-check',
                                                    default   => 'heroicon-m-minus',
                                                };

                                                return new HtmlString("
                                                    <div class='flex items-center gap-2'>
                                                        " . Blade::render("<x-filament::badge color='{$color}' icon='{$icon}'>{$label}</x-filament::badge>") . "
                                                    </div>
                                                ");
                                            })
                                            ->columnSpan(1),

                                        Placeholder::make('treatment_summary')
                                            ->label('Tratamiento Elegido')
                                            ->content(function (?Risk $record) {
                                                // $record = el Risk de BD; null en riesgos nuevos (pero la sección está hidden si es null)
                                                if (!$record || !$record->treatment) {
                                                    return new HtmlString('<x-filament::badge color="gray">Sin definir</x-filament::badge>');
                                                }

                                                $label = ucfirst($record->treatment);
                                                $color = match ($record->treatment) {
                                                    Risk::TREATMENT_ACEPTAR    => 'info',
                                                    Risk::TREATMENT_EVITAR     => 'danger',
                                                    Risk::TREATMENT_REDUCIR    => 'warning',
                                                    Risk::TREATMENT_TRANSFERIR => 'success',
                                                    default                    => 'gray',
                                                };

                                                return new HtmlString(Blade::render("<x-filament::badge color='{$color}'>{$label}</x-filament::badge>"));
                                            })
                                            ->columnSpan(1),

                                        Placeholder::make('treatment_consistency')
                                            ->label('Estado / Coherencia')
                                            ->content(function (Get $get, ?Risk $record) {
                                                // $get('treatment') lee el tratamiento del form; si no se cambió aún, usamos el del $record
                                                $treatment   = $get('treatment') ?? ($record ? $record->treatment : null);
                                                $openActions = collect($get('actions') ?? [])
                                                    ->whereNotIn('status', [Action::STATUS_CERRADA, Action::STATUS_CANCELADA])
                                                    ->count(); // acciones que siguen abiertas o en curso

                                                if (!$treatment) return '-';

                                                // Reducir/Transferir requieren acciones activas; si no hay ninguna → inconsistente
                                                if (in_array($treatment, [Risk::TREATMENT_REDUCIR, Risk::TREATMENT_TRANSFERIR])) {
                                                    if ($openActions === 0) {
                                                        return new HtmlString(Blade::render('<x-filament::badge color="danger" icon="heroicon-m-exclamation-triangle">Requiere medidas activas</x-filament::badge>'));
                                                    }
                                                    return new HtmlString(Blade::render('<x-filament::badge color="success" icon="heroicon-m-check-circle">Medidas en ejecución</x-filament::badge>'));
                                                }

                                                // Aceptar = la organización asume el riesgo conscientemente
                                                if ($treatment === Risk::TREATMENT_ACEPTAR) {
                                                    return new HtmlString(Blade::render('<x-filament::badge color="info" icon="heroicon-m-information-circle">Riesgo asumido</x-filament::badge>'));
                                                }

                                                return new HtmlString(Blade::render('<x-filament::badge color="gray" icon="heroicon-m-check-circle">Coherente</x-filament::badge>'));
                                            })
                                            ->columnSpan(1),

                                        TextInput::make('next_review')
                                            ->label('Próxima Revisión')
                                            ->disabled()
                                            ->prefixIcon(fn($record) => $record && $record->isReviewOverdue() ? 'heroicon-m-bell-alert' : 'heroicon-m-calendar-days')
                                            ->prefixIconColor(fn($record) => $record && $record->isReviewOverdue() ? 'danger' : 'success')
                                            ->formatStateUsing(function (?Risk $record) {
                                                if (! $record?->next_review_at) return 'No definida';

                                                $date = $record->next_review_at->format('d/m/Y');
                                                return $record->isReviewOverdue() ? "{$date} ⚠️ VENCIDA" : $date;
                                            })
                                            ->extraInputAttributes(fn($record) => [
                                                'class' => $record && $record->isReviewOverdue() ? 'font-bold text-danger-600' : '',
                                            ])
                                            ->columnSpan(1),
                                    ])
                                    ->columns(4)
                                    ->columnSpan(4),

                                Fieldset::make('Resumen de Planes de Tratamiento')
                                    ->schema([
                                        Placeholder::make('controls_summary_combined')
                                            ->label('Controles (Totales / Insuficientes)')
                                            ->content(function (Get $get) {
                                                $controls      = collect($get('controls') ?? []);
                                                $total         = $controls->count();
                                                $insuficientes = $controls->where('effectiveness', 'Insuficiente')->count();

                                                $color = $insuficientes > 0
                                                    ? 'text-danger-600 dark:text-danger-400'
                                                    : 'text-success-600 dark:text-success-400';

                                                return new HtmlString("
                                                    <div class='flex items-center gap-2 font-bold'>
                                                        <span class='text-gray-600'>{$total} Totales</span>
                                                        <span class='text-gray-300'>/</span>
                                                        <span class='{$color}'>{$insuficientes} Insuficientes</span>
                                                    </div>
                                                ");
                                            }),

                                        Placeholder::make('actions_summary_combined')
                                            ->label('Acciones (Totales / Pendientes)')
                                            ->content(function (Get $get) {
                                                $actions    = collect($get('actions') ?? []);
                                                $total      = $actions->count();
                                                $pendientes = $actions->whereNotIn('status', [Action::STATUS_CERRADA, Action::STATUS_CANCELADA])->count();

                                                $color = $pendientes > 0
                                                    ? 'text-warning-600 dark:text-warning-400'
                                                    : 'text-success-600 dark:text-success-400';

                                                return new HtmlString("
                                                    <div class='flex items-center gap-2 font-bold'>
                                                        <span class='text-gray-600'>{$total} Totales</span>
                                                        <span class='text-gray-300'>/</span>
                                                        <span class='{$color}'>{$pendientes} Pendientes</span>
                                                    </div>
                                                ");
                                            }),
                                    ])
                                    ->columns(2)
                                    ->columnSpan(4),

                                Fieldset::make('Detalle de Medidas Críticas')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Placeholder::make('list_insufficient_controls')
                                                    ->label('Controles Insuficientes')
                                                    ->content(function (?Risk $record) {
                                                        if (!$record) return 'Ninguno';

                                                        $controls = $record->controls()->where('effectiveness', 'Insuficiente')->get();
                                                        if ($controls->isEmpty()) return 'Ninguno';

                                                        $html = '<ul class="flex flex-col gap-3 mt-2">';
                                                        foreach ($controls as $control) {
                                                            $title   = htmlspecialchars($control->title, ENT_QUOTES);
                                                            // buildNavigationJs() genera el JS que navega a la pestaña y resalta el item en rojo
                                                            $onclick = htmlspecialchars(self::buildNavigationJs(addslashes($control->title), '239, 68, 68'), ENT_QUOTES);

                                                            $html .= "
                                                                <li class=\"flex items-center justify-between p-3 bg-danger-50 dark:bg-danger-500/10 rounded-xl border border-danger-200 dark:border-danger-500/20 shadow-sm\">
                                                                    <div class=\"flex items-center gap-2 overflow-hidden\">
                                                                        <svg class=\"w-5 h-5 text-danger-500 flex-shrink-0\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">
                                                                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z\" />
                                                                        </svg>
                                                                        <span class=\"text-danger-700 dark:text-danger-400 font-semibold text-sm truncate\" title=\"{$title}\">{$title}</span>
                                                                    </div>
                                                                    <button type=\"button\" onclick=\"{$onclick}\" class=\"flex items-center gap-1 text-xs font-bold px-3 py-1.5 bg-white dark:bg-gray-800 border border-danger-300 dark:border-danger-600 text-danger-600 dark:text-danger-400 rounded-lg hover:bg-danger-100 dark:hover:bg-danger-500/30 transition flex-shrink-0\">
                                                                        Ver control
                                                                        <svg class=\"w-4 h-4\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 5l7 7-7 7\" /></svg>
                                                                    </button>
                                                                </li>";
                                                        }
                                                        $html .= '</ul>';
                                                        return new HtmlString($html);
                                                    })
                                                    ->columnSpan(1),

                                                Placeholder::make('list_pending_actions')
                                                    ->label('Acciones Pendientes')
                                                    ->content(function (?Risk $record) {
                                                        if (!$record) return 'Ninguna';

                                                        $actions = $record->actions()->whereNotIn('status', [Action::STATUS_CERRADA, Action::STATUS_CANCELADA])->get();
                                                        if ($actions->isEmpty()) return 'Ninguna';

                                                        $html = '<ul class="flex flex-col gap-3 mt-2">';
                                                        foreach ($actions as $action) {
                                                            $title   = htmlspecialchars($action->title, ENT_QUOTES);
                                                            // Mismo mecanismo que los controles, pero el resaltado es naranja (warning)
                                                            $onclick = htmlspecialchars(self::buildNavigationJs(addslashes($action->title), '245, 158, 11'), ENT_QUOTES);

                                                            $html .= "
                                                                <li class=\"flex items-center justify-between p-3 bg-warning-50 dark:bg-warning-500/10 rounded-xl border border-warning-200 dark:border-warning-500/20 shadow-sm\">
                                                                    <div class=\"flex items-center gap-2 overflow-hidden\">
                                                                        <svg class=\"w-5 h-5 text-warning-500 flex-shrink-0\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">
                                                                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\" />
                                                                        </svg>
                                                                        <span class=\"text-warning-700 dark:text-warning-400 font-semibold text-sm truncate\" title=\"{$title}\">{$title}</span>
                                                                    </div>
                                                                    <button type=\"button\" onclick=\"{$onclick}\" class=\"flex items-center gap-1 text-xs font-bold px-3 py-1.5 bg-white dark:bg-gray-800 border border-warning-300 dark:border-warning-600 text-warning-600 dark:text-warning-400 rounded-lg hover:bg-warning-100 dark:hover:bg-warning-500/30 transition flex-shrink-0\">
                                                                        Ejecutar
                                                                        <svg class=\"w-4 h-4\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 5l7 7-7 7\" /></svg>
                                                                    </button>
                                                                </li>";
                                                        }
                                                        $html .= '</ul>';
                                                        return new HtmlString($html);
                                                    })
                                                    ->columnSpan(1),
                                            ])
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpan(4),
                            ]),
                    ]),

                Section::make('Valoración de Controles')
                    ->description('Resumen de supervisión y efectividad operativa.')
                    ->icon('heroicon-m-magnifying-glass-circle')
                    ->iconColor('warning')
                    ->visible(fn($record) => $record !== null)
                    ->columnSpanFull()
                    ->schema([
                        Placeholder::make('alerta')
                            ->hiddenLabel()
                            ->visible(fn($record) => $record?->controls()->where('effectiveness', 'Insuficiente')->exists())
                            ->content('⚠️ ATENCIÓN: Existen controles insuficientes registrados.'),

                        Grid::make(5)
                            ->schema([
                                Fieldset::make('Comparativa de Gestión')
                                    ->schema([
                                        TextInput::make('real_effectiveness')
                                            ->label('Efectividad Promedio Real')
                                            ->disabled()
                                            ->suffix('%')
                                            ->prefixIcon('heroicon-m-chart-pie')
                                            ->formatStateUsing(function (Get $get, ?Risk $record) {
                                                // resolveControls() elige entre el repeater del form (array) o los de BD ($record->controls)
                                                $controls = self::resolveControls($get, $record);
                                                if ($controls->isEmpty()) return 0;

                                                // avg() calcula el promedio directamente; filtramos nulos para no distorsionar el resultado
                                                $avg = $controls
                                                    ->filter(fn($c) => (is_array($c) ? ($c['effectiveness'] ?? null) : $c->effectiveness) !== null)
                                                    ->avg(fn($c) => match (is_array($c) ? ($c['effectiveness'] ?? '') : $c->effectiveness) {
                                                        'Suficiente' => 100,
                                                        'Medio'      => 50,
                                                        default      => 0,
                                                    });

                                                return (int) round($avg ?? 0);
                                            })
                                            ->extraInputAttributes(['class' => 'font-bold text-lg'])
                                            ->columnSpan(1),

                                        TextInput::make('estimated_management')
                                            ->label('Gestión Estimada (Última Eval.)')
                                            ->disabled()
                                            ->suffix('%')
                                            ->prefixIcon('heroicon-m-user-circle')
                                            ->prefixIconColor('gray')
                                            ->formatStateUsing(function (?Risk $record) {
                                                if (! $record) return 0;

                                                // management_level de la evaluación residual más reciente guardada en BD
                                                return $record->assessments()
                                                    ->where('type', 'residual')
                                                    ->orderByDesc('assessed_at')
                                                    ->value('management_level') ?? 0;
                                            })
                                            ->extraInputAttributes(['class' => 'font-bold text-lg'])
                                            ->columnSpan(1),
                                    ])
                                    ->columns(2)
                                    ->columnSpan(2),

                                Fieldset::make('Desglose de Controles')
                                    ->schema([
                                        TextInput::make('v_total')->label('Total')->disabled()->prefixIcon('heroicon-m-hashtag')
                                            ->formatStateUsing(function (Get $get, ?Risk $record) {
                                                $state = $get('controls');
                                                return ($state && count($state) > 0) ? count($state) : ($record ? $record->controls()->count() : 0);
                                            }),

                                        TextInput::make('v_docs')->label('Evidencias')->disabled()->prefixIcon('heroicon-m-document-text')
                                            ->formatStateUsing(function (Get $get, ?Risk $record) {
                                                // Cuenta documentos vinculados a controles (control_id no nulo)
                                                $docsState = $get('documents');
                                                if ($docsState && count($docsState) > 0) {
                                                    return collect($docsState)->whereNotNull('control_id')->count();
                                                }

                                                return $record
                                                    ? RiskDocument::where('risk_id', $record->id)->whereNotNull('control_id')->count()
                                                    : 0;
                                            }),

                                        TextInput::make('v_plazos')
                                            ->label('Vencimiento')
                                            ->disabled()
                                            ->live()
                                            ->formatStateUsing(function (Get $get, ?Risk $record) {
                                                // overdueControls() filtra controles con due_date anterior a hoy
                                                $vencidos = self::overdueControls($get, $record)->count();
                                                return $vencidos > 0 ? "{$vencidos} Vencidos" : 'Sin vencimientos';
                                            })
                                            ->prefixIcon(fn(Get $get, ?Risk $record) => self::overdueControls($get, $record)->isNotEmpty()
                                                ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-circle')
                                            ->prefixIconColor(fn(Get $get, ?Risk $record) => self::overdueControls($get, $record)->isNotEmpty()
                                                ? 'danger' : 'success'),

                                        // countEffective() filtra la colección de controles por nivel de efectividad
                                        TextInput::make('v_suficiente')->label('Suficientes')->disabled()->prefixIcon('heroicon-m-shield-check')->prefixIconColor('success')
                                            ->formatStateUsing(fn(Get $get, ?Risk $record) => self::countEffective('Suficiente', $get, $record)),

                                        TextInput::make('v_medio')->label('Medios')->disabled()->prefixIcon('heroicon-m-shield-exclamation')->prefixIconColor('warning')
                                            ->formatStateUsing(fn(Get $get, ?Risk $record) => self::countEffective('Medio', $get, $record)),

                                        TextInput::make('v_insuficiente')->label('Insuficientes')->disabled()->prefixIcon('heroicon-m-shield-exclamation')->prefixIconColor('danger')
                                            ->formatStateUsing(fn(Get $get, ?Risk $record) => self::countEffective('Insuficiente', $get, $record)),
                                    ])
                                    ->columns(2)
                                    ->columnSpan(3),
                            ]),
                    ]),
            ]);
    }

    /**
     * Devuelve los controles del repeater del formulario si existen; si no, los carga de BD.
     * Evita llamadas redundantes a la BD cuando el repeater ya tiene los datos en memoria.
     *
     * $get('controls') = array del estado del repeater (items son arrays)
     * $record->controls = colección Eloquent ya cargada por la relación del modelo Risk
     */
    private static function resolveControls(Get $get, ?Risk $record): Collection
    {
        $state = $get('controls');
        return ($state && count($state) > 0)
            ? collect($state)
            : ($record ? $record->controls : collect());
    }

    /**
     * Cuenta cuántos controles tienen un nivel de efectividad específico.
     * Soporta tanto arrays (del repeater) como objetos Eloquent (de BD).
     */
    private static function countEffective(string $level, Get $get, ?Risk $record): int
    {
        return self::resolveControls($get, $record)
            ->filter(fn($c) => (is_array($c) ? ($c['effectiveness'] ?? '') : $c->effectiveness) === $level)
            ->count();
    }

    /**
     * Filtra los controles que tienen due_date vencida (anterior a hoy).
     * Usado por v_plazos para calcular el texto, el icono y el color del campo.
     */
    private static function overdueControls(Get $get, ?Risk $record): Collection
    {
        return collect($get('controls') ?: ($record?->controls ?? []))
            ->filter(fn($c) => ($d = is_array($c) ? ($c['due_date'] ?? null) : $c->due_date) &&
                Carbon::parse($d)->lt(Carbon::today()));
    }

    /**
     * Genera el fragmento JavaScript inline que navega a la pestaña "Controles y Acciones"
     * y resalta el item cuyo input coincide con $safeJsTitle usando el color $rgbColor.
     *
     * @param  string  $safeJsTitle  Título ya escapado para uso en JS (addslashes aplicado)
     * @param  string  $rgbColor     Color en formato "R, G, B" (ej: '239, 68, 68' = rojo danger)
     */
    private static function buildNavigationJs(string $safeJsTitle, string $rgbColor): string
    {
        return "
            let tab = Array.from(document.querySelectorAll('button[role=\"tab\"]')).find(el => el.innerText.includes('Controles y Acciones'));
            if(tab) tab.click();
            setTimeout(() => {
                let inputs = Array.from(document.querySelectorAll('input'));
                let target = inputs.find(el => el.value === '{$safeJsTitle}');
                if(target) {
                    target.scrollIntoView({behavior: 'smooth', block: 'center'});
                    let container = target.closest('[x-bind=\"item\"]') || target.closest('.fi-repeater-item') || target.closest('.fi-fo-repeater-item') || target.parentElement;
                    container.style.transition = 'all 0.5s';
                    let oldBg = container.style.backgroundColor;
                    container.style.backgroundColor = 'rgba({$rgbColor}, 0.15)';
                    setTimeout(() => container.style.backgroundColor = oldBg, 2000);
                }
            }, 300);
        ";
    }
}
