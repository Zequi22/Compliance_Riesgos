<?php

namespace App\Filament\Resources\Risks\Schemas;

use App\Models\OrganizationalUnit;
use App\Models\Action;
use App\Models\Risk;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
//use Filament\Forms\Components\FileUpload;
//use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use App\Models\RiskDocument;
use Carbon\Carbon;
use Filament\Actions\Action as FilamentAction;


class RiskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Tabs::make('Navegación del Riesgo')
                    ->columnSpanFull()
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('1. Identificación')
                            ->icon('heroicon-m-identification')
                            ->schema([
                                Section::make('Datos Generales')
                                    ->description('Información principal para la identificación y clasificación del riesgo.')
                                    ->icon('heroicon-m-information-circle')
                                    ->iconColor('warning')
                                    ->schema([
                                        Grid::make(5)
                                            ->schema([
                                                Fieldset::make('Identificación Básica')
                                                    ->schema([
                                                        TextInput::make('name')
                                                            ->label('Nombre del Riesgo')
                                                            ->prefixIcon('heroicon-m-tag')
                                                            ->required()
                                                            ->placeholder('Ej: Falla en el sistema de respaldo')
                                                            ->columnSpan(2),

                                                        Textarea::make('description')
                                                            ->label('Descripción Completa')
                                                            ->default(null)
                                                            ->rows(2)
                                                            ->columnSpan(2),
                                                    ])
                                                    ->columns(2)
                                                    ->columnSpan(3),

                                                Fieldset::make('Estado y Clasificación')
                                                    ->schema([
                                                        Select::make('status')
                                                            ->label('Estado Actual')
                                                            ->prefixIcon('heroicon-m-flag')
                                                            ->options([
                                                                'Identificado' => Risk::STATUS_IDENTIFICADO,
                                                                'Evaluado' => Risk::STATUS_EVALUADO,
                                                                'En tratamiento' => Risk::STATUS_TRATAMIENTO,
                                                                'En seguimiento' => Risk::STATUS_SEGUIMIENTO,
                                                                'Cerrado / Revisado' => Risk::STATUS_CERRADO,
                                                            ])
                                                            ->default(Risk::STATUS_IDENTIFICADO)
                                                            ->required()
                                                            ->columnSpan(1),

                                                        Select::make('treatment')
                                                            ->label('Tratamiento del Riesgo')
                                                            ->prefixIcon('heroicon-m-shield-check')
                                                            ->options([
                                                                Risk::TREATMENT_ACEPTAR => 'Aceptar',
                                                                Risk::TREATMENT_EVITAR => 'Evitar',
                                                                Risk::TREATMENT_REDUCIR => 'Reducir',
                                                                Risk::TREATMENT_TRANSFERIR => 'Transferir',
                                                            ])
                                                            ->in([
                                                                Risk::TREATMENT_ACEPTAR,
                                                                Risk::TREATMENT_EVITAR,
                                                                Risk::TREATMENT_REDUCIR,
                                                                Risk::TREATMENT_TRANSFERIR,
                                                            ])
                                                            ->placeholder('Seleccione tratamiento')
                                                            ->columnSpan(1),

                                                        TextInput::make('category')
                                                            ->label('Categoría / Familia')
                                                            ->prefixIcon('heroicon-m-folder')
                                                            ->placeholder('Ej: Financiero, Operativo')
                                                            ->default(null)
                                                            ->columnSpan(1),

                                                        Select::make('organizational_unit_id')
                                                            ->label('Área / Proceso / Departamento')
                                                            ->prefixIcon('heroicon-m-building-office')
                                                            ->relationship('organizationalUnit', 'name')
                                                            ->createOptionForm([
                                                                TextInput::make('name')->label('Nombre')->required(),
                                                                Select::make('type')
                                                                    ->label('Tipo')
                                                                    ->options([
                                                                        'Área' => 'Área',
                                                                        'Proceso' => 'Proceso',
                                                                        'Proceso Estratégico' => 'Proceso Estratégico',
                                                                        'Proceso Operativo' => 'Proceso Operativo',
                                                                        'Proceso de Apoyo' => 'Proceso de Apoyo',
                                                                        'Departamento' => 'Departamento',
                                                                    ])
                                                                    ->required(),
                                                            ])
                                                            ->searchable()
                                                            ->preload()
                                                            ->nullable()
                                                            ->placeholder('Seleccione del catálogo')
                                                            ->columnSpan(2),

                                                        Select::make('type_crime')
                                                            ->label('Marco Penal')
                                                            ->prefixIcon('heroicon-m-scale')
                                                            ->placeholder('Seleccione el delito asociado')
                                                            ->options([
                                                                'Corrupción y Mercado' => [
                                                                    'Corrupción en los negocios' => 'Corrupción en los negocios',
                                                                    'Cohecho' => 'Cohecho (Soborno)',
                                                                    'Tráfico de influencias' => 'Tráfico de influencias',
                                                                    'Uso de información privilegiada' => 'Uso de información privilegiada',
                                                                ],
                                                                'Económicos y Fraude' => [
                                                                    'Estafa' => 'Estafa / Fraude',
                                                                    'Blanqueo de capitales' => 'Blanqueo de capitales',
                                                                    'Insolvencias punibles' => 'Insolvencias punibles (Alzamiento)',
                                                                    'Delitos contra Hacienda y Seg. Social' => 'Delitos Fiscales y Seguridad Social',
                                                                ],
                                                                'Tecnológicos y Propiedad' => [
                                                                    'Descubrimiento y revelación de secretos' => 'Hacking / Revelación de secretos',
                                                                    'Daños informáticos' => 'Daños informáticos',
                                                                    'Propiedad Intelectual e Industrial' => 'Propiedad Intelectual e Industrial',
                                                                ],
                                                                'Otros' => [
                                                                    'Delitos contra el medio ambiente' => 'Delitos contra el medio ambiente',
                                                                    'Delitos contra la salud pública' => 'Delitos contra la salud pública',
                                                                    'Financiación del terrorismo' => 'Financiación del terrorismo',
                                                                ],
                                                            ])
                                                            ->searchable()
                                                            ->required()
                                                            ->columnSpan(2),

                                                        DatePicker::make('last_review_at')
                                                            ->label('Última Revisión')
                                                            ->prefixIcon('heroicon-m-calendar')
                                                            ->placeholder('DD/MM/AAAA')
                                                            ->columnSpan(1),

                                                        DatePicker::make('next_review_at')
                                                            ->label('Próxima Revisión')
                                                            ->prefixIcon('heroicon-m-calendar-days')
                                                            ->placeholder('DD/MM/AAAA')
                                                            ->required()
                                                            ->columnSpan(1),

                                                        Placeholder::make('criticality')
                                                            ->label('Nivel de Criticidad Base')
                                                            ->content(function (?Risk $record) {
                                                                $criticality = $record ? ($record->criticality ?? 'No Evaluado') : 'No Evaluado';
                                                                $color = $record ? ($record->criticalityColor ?? 'gray') : 'gray';

                                                                return new HtmlString(Blade::render("<x-filament::badge color=\"{$color}\">{$criticality}</x-filament::badge>"));
                                                            })
                                                            ->columnSpan(2),
                                                    ])
                                                    ->columns(2)
                                                    ->columnSpan(2),
                                            ]),
                                    ]),

                                Section::make('Gestión del Responsable')
                                    ->description('Asignación de la persona titular o encargada de este riesgo en particular.')
                                    ->icon('heroicon-o-user-circle')
                                    ->iconColor('warning')
                                    ->columns(3)
                                    ->compact()
                                    ->schema([
                                        Select::make('responsable_id')
                                            ->label('Seleccionar Usuario Titular')
                                            ->prefixIcon('heroicon-m-magnifying-glass')
                                            ->options(fn() => User::all()->mapWithKeys(fn($user) => [
                                                $user->id => "{$user->name} {$user->last_name} ({$user->job_title})",
                                            ]))
                                            ->searchable()
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                $user = User::find($state);
                                                if ($user) {
                                                    $set('responsable_nombre', $user->name);
                                                    $set('responsable_apellidos', $user->last_name);
                                                    $set('responsable_area', $user->area);
                                                    $set('responsable_equipo', $user->team);
                                                    $set('responsable_departamento', $user->department);
                                                    $set('responsable_cargo', $user->job_title);
                                                }

                                                $actions = $get('actions') ?? [];
                                                foreach (array_keys($actions) as $uuid) {
                                                    $set("actions.{$uuid}.responsable_id", $state);
                                                }

                                                $controls = $get('controls') ?? [];
                                                foreach (array_keys($controls) as $uuid) {
                                                    $set("controls.{$uuid}.owner_name", $user->name ?? '');
                                                    $set("controls.{$uuid}.owner_area", $user->area ?? '');
                                                }
                                            })
                                            ->columnSpanFull(),

                                        TextInput::make('responsable_nombre')->label('Nombre')->prefixIcon('heroicon-m-user')->disabled()->dehydrated(false)->formatStateUsing(fn($get) => User::find($get('responsable_id'))?->name)->live(onBlur: true),
                                        TextInput::make('responsable_apellidos')->label('Apellidos')->prefixIcon('heroicon-m-user')->disabled()->dehydrated(false)->formatStateUsing(fn($get) => User::find($get('responsable_id'))?->last_name)->live(onBlur: true),
                                        TextInput::make('responsable_area')->label('Área Interna')->prefixIcon('heroicon-m-building-office-2')->disabled()->dehydrated(false)->formatStateUsing(fn($get) => User::find($get('responsable_id'))?->area)->live(onBlur: true),
                                        TextInput::make('responsable_departamento')->label('Departamento')->prefixIcon('heroicon-m-building-office')->disabled()->dehydrated(false)->formatStateUsing(fn($get) => User::find($get('responsable_id'))?->department)->live(onBlur: true),
                                        TextInput::make('responsable_equipo')->label('Equipo / Team')->prefixIcon('heroicon-m-users')->disabled()->dehydrated(false)->formatStateUsing(fn($get) => User::find($get('responsable_id'))?->team)->live(onBlur: true),
                                        TextInput::make('responsable_cargo')->label('Cargo / Puesto')->prefixIcon('heroicon-m-briefcase')->disabled()->dehydrated(false)->formatStateUsing(fn($get) => User::find($get('responsable_id'))?->job_title)->live(onBlur: true),
                                    ]),

                            ]), // Fin Identificación

                        Tab::make('2. Plan y Valoración')
                            ->icon('heroicon-m-clipboard-document-check')
                            ->schema([
                                Section::make('Plan de Gestión del Riesgo')
                                    ->description('Monitorización del estado global: criticidad, tiempos de respuesta y planes de acción registrados.')
                                    ->icon('heroicon-m-clipboard-document-check')
                                    ->iconColor('success')
                                    ->visible(fn($record) => $record !== null)
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                Fieldset::make('Estado y Criticidad')
                                                    ->schema([
                                                        Placeholder::make('criticality_status')
                                                            ->label('Nivel de Criticidad')
                                                            ->content(function (Get $get) {
                                                                $assessments = $get('assessments') ?? [];
                                                                if (empty($assessments)) {
                                                                    return new HtmlString(Blade::render('<x-filament::badge color="gray">No Evaluado</x-filament::badge>'));
                                                                }

                                                                $sorted = collect($assessments)->sortByDesc('assessed_at');
                                                                $residual = $sorted->firstWhere('type', 'residual');
                                                                $assessment = $residual ?: $sorted->firstWhere('type', 'inherent');

                                                                $score = $assessment['score'] ?? null;
                                                                if (!$score) {
                                                                    return new HtmlString(Blade::render('<x-filament::badge color="gray">No Evaluado</x-filament::badge>'));
                                                                }

                                                                $label = match (true) {
                                                                    $score <= 2 => 'Muy Bajo',
                                                                    $score <= 4 => 'Bajo',
                                                                    $score <= 9 => 'Medio',
                                                                    $score <= 14 => 'Alto',
                                                                    default => 'Crítico',
                                                                };

                                                                $color = match ($label) {
                                                                    'Muy Bajo' => 'success',
                                                                    'Bajo' => 'info',
                                                                    'Medio' => 'warning',
                                                                    'Alto' => 'danger',
                                                                    'Crítico' => 'danger',
                                                                    default => 'gray',
                                                                };

                                                                $icon = match ($color) {
                                                                    'danger' => 'heroicon-m-fire',
                                                                    'warning' => 'heroicon-m-exclamation-triangle',
                                                                    'info' => 'heroicon-m-information-circle',
                                                                    'success' => 'heroicon-m-shield-check',
                                                                    default => 'heroicon-m-minus'
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
                                                                if (!$record || !$record->treatment) {
                                                                    return new HtmlString('<x-filament::badge color="gray">Sin definir</x-filament::badge>');
                                                                }

                                                                $label = ucfirst($record->treatment);
                                                                $color = match ($record->treatment) {
                                                                    Risk::TREATMENT_ACEPTAR => 'info',
                                                                    Risk::TREATMENT_EVITAR => 'danger',
                                                                    Risk::TREATMENT_REDUCIR => 'warning',
                                                                    Risk::TREATMENT_TRANSFERIR => 'success',
                                                                    default => 'gray',
                                                                };

                                                                return new HtmlString(Blade::render("<x-filament::badge color='{$color}'>{$label}</x-filament::badge>"));
                                                            })
                                                            ->columnSpan(1),

                                                        Placeholder::make('treatment_consistency')
                                                            ->label('Estado / Coherencia')
                                                            ->content(function (Get $get, ?Risk $record) {
                                                                $treatment = $get('treatment') ?? ($record ? $record->treatment : null);
                                                                $actions = collect($get('actions') ?? []);
                                                                $openActions = $actions->whereNotIn('status', [Action::STATUS_CERRADA, Action::STATUS_CANCELADA])->count();

                                                                if (!$treatment) return '-';

                                                                if (in_array($treatment, [Risk::TREATMENT_REDUCIR, Risk::TREATMENT_TRANSFERIR])) {
                                                                    if ($openActions === 0) {
                                                                        return new HtmlString(Blade::render('<x-filament::badge color="danger" icon="heroicon-m-exclamation-triangle">Requiere medidas activas</x-filament::badge>'));
                                                                    }
                                                                    return new HtmlString(Blade::render('<x-filament::badge color="success" icon="heroicon-m-check-circle">Medidas en ejecución</x-filament::badge>'));
                                                                }

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
                                                                if (! $record?->next_review_at) {
                                                                    return 'No definida';
                                                                }
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
                                                                $controls = collect($get('controls') ?? []);
                                                                $total = $controls->count();
                                                                $insuficientes = $controls->where('effectiveness', 'Insuficiente')->count();

                                                                $color = $insuficientes > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400';

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
                                                                $actions = collect($get('actions') ?? []);
                                                                $total = $actions->count();
                                                                $pendientes = $actions->whereNotIn('status', [Action::STATUS_CERRADA, Action::STATUS_CANCELADA])->count();

                                                                $color = $pendientes > 0 ? 'text-warning-600 dark:text-warning-400' : 'text-success-600 dark:text-success-400';

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
                                                                            $title = htmlspecialchars($control->title, ENT_QUOTES);
                                                                            $safeJsTitle = addslashes($control->title);

                                                                            $rawJs = "
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
                                                                                        container.style.backgroundColor = 'rgba(239, 68, 68, 0.15)'; 
                                                                                        setTimeout(() => container.style.backgroundColor = oldBg, 2000);
                                                                                    }
                                                                                }, 300);
                                                                            ";
                                                                            $onclick = htmlspecialchars($rawJs, ENT_QUOTES);

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
                                                                            $title = htmlspecialchars($action->title, ENT_QUOTES);
                                                                            $safeJsTitle = addslashes($action->title);

                                                                            $rawJs = "
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
                                                                                        container.style.backgroundColor = 'rgba(245, 158, 11, 0.15)'; 
                                                                                        setTimeout(() => container.style.backgroundColor = oldBg, 2000);
                                                                                    }
                                                                                }, 300);
                                                                            ";
                                                                            $onclick = htmlspecialchars($rawJs, ENT_QUOTES);

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
                                                                $state = $get('controls');
                                                                $controls = ($state && count($state) > 0) ? collect($state) : ($record ? $record->controls : collect());

                                                                if ($controls->isEmpty()) return 0;

                                                                $totalScore = 0;
                                                                $validCount = 0;

                                                                foreach ($controls as $control) {
                                                                    $eff = is_array($control) ? ($control['effectiveness'] ?? null) : $control->effectiveness;
                                                                    if (!$eff) continue;

                                                                    $totalScore += match ($eff) {
                                                                        'Suficiente' => 100,
                                                                        'Medio' => 50,
                                                                        'Insuficiente' => 0,
                                                                        default => 0,
                                                                    };
                                                                    $validCount++;
                                                                }

                                                                return $validCount > 0 ? (int) round($totalScore / $validCount) : 0;
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
                                                                if (! $record) {
                                                                    return 0;
                                                                }

                                                                return $record->assessments()->where('type', 'residual')->orderByDesc('assessed_at')->value('management_level') ?? 0;
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
                                                                // Contar documentos vinculados a controles
                                                                $docsState = $get('documents');
                                                                if ($docsState && count($docsState) > 0) {
                                                                    return collect($docsState)->whereNotNull('control_id')->count();
                                                                }

                                                                if ($record) {
                                                                    return RiskDocument::where('risk_id', $record->id)
                                                                        ->whereNotNull('control_id')
                                                                        ->count();
                                                                }

                                                                return 0;
                                                            }),

                                                        TextInput::make('v_plazos')
                                                            ->label('Vencimiento')
                                                            ->disabled()
                                                            ->live()
                                                            ->formatStateUsing(function (Get $get, ?Risk $record) {
                                                                $controls = collect($get('controls') ?: ($record?->controls ?? []));

                                                                $vencidos = $controls->filter(
                                                                    fn($c) => ($d = is_array($c) ? ($c['due_date'] ?? null) : $c->due_date) &&
                                                                        Carbon::parse($d)->lt(Carbon::today())
                                                                )->count();

                                                                return $vencidos > 0 ? "{$vencidos} Vencidos" : 'Sin vencimientos';
                                                            })
                                                            ->prefixIcon(
                                                                fn(Get $get, ?Risk $record) =>
                                                                collect($get('controls') ?: ($record?->controls ?? []))
                                                                    ->filter(fn($c) => ($d = is_array($c) ? ($c['due_date'] ?? null) : $c->due_date) && Carbon::parse($d)->lt(Carbon::today()))
                                                                    ->isNotEmpty() ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-circle'
                                                            )
                                                            ->prefixIconColor(
                                                                fn(Get $get, ?Risk $record) =>
                                                                collect($get('controls') ?: ($record?->controls ?? []))
                                                                    ->filter(fn($c) => ($d = is_array($c) ? ($c['due_date'] ?? null) : $c->due_date) && Carbon::parse($d)->lt(Carbon::today()))
                                                                    ->isNotEmpty() ? 'danger' : 'success'
                                                            ),

                                                        TextInput::make('v_suficiente')->label('Suficientes')->disabled()->prefixIcon('heroicon-m-shield-check')->prefixIconColor('success')
                                                            ->formatStateUsing(function (Get $get, ?Risk $record) {
                                                                $state = $get('controls');
                                                                $controls = ($state && count($state) > 0) ? collect($state) : ($record ? $record->controls : collect());
                                                                return $controls->filter(fn($c) => (is_array($c) ? ($c['effectiveness'] ?? '') : $c->effectiveness) === 'Suficiente')->count();
                                                            }),

                                                        TextInput::make('v_medio')->label('Medios')->disabled()->prefixIcon('heroicon-m-shield-exclamation')->prefixIconColor('warning')
                                                            ->formatStateUsing(function (Get $get, ?Risk $record) {
                                                                $state = $get('controls');
                                                                $controls = ($state && count($state) > 0) ? collect($state) : ($record ? $record->controls : collect());
                                                                return $controls->filter(fn($c) => (is_array($c) ? ($c['effectiveness'] ?? '') : $c->effectiveness) === 'Medio')->count();
                                                            }),

                                                        TextInput::make('v_insuficiente')->label('Insuficientes')->disabled()->prefixIcon('heroicon-m-shield-exclamation')->prefixIconColor('danger')
                                                            ->formatStateUsing(function (Get $get, ?Risk $record) {
                                                                $state = $get('controls');
                                                                $controls = ($state && count($state) > 0) ? collect($state) : ($record ? $record->controls : collect());
                                                                return $controls->filter(fn($c) => (is_array($c) ? ($c['effectiveness'] ?? '') : $c->effectiveness) === 'Insuficiente')->count();
                                                            }),
                                                    ])
                                                    ->columns(2)
                                                    ->columnSpan(3),
                                            ]),
                                    ]),

                            ]), // Fin Plan y Valoración

                        Tab::make('3. Evidencias y Análisis')
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
                                                    $url = "/admin/risk-documents/{$doc->id}/edit";

                                                    // Determinar el origen
                                                    $origin = 'Riesgo (General)';
                                                    if ($doc->control_id) {
                                                        $origin = "Control: " . (optional($doc->control)->title ?? 'ID: ' . $doc->control_id);
                                                    } elseif ($doc->action_id) {
                                                        $origin = "Acción: " . (optional($doc->action)->title ?? 'ID: ' . $doc->action_id);
                                                    }

                                                    $statusLabel = match ($doc->status) {
                                                        'validada' => '<span class="px-2 py-0.5 text-[10px] font-bold bg-green-100 text-green-700 rounded-full uppercase">Validada</span>',
                                                        'rechazada' => '<span class="px-2 py-0.5 text-[10px] font-bold bg-red-100 text-red-700 rounded-full uppercase">Rechazada</span>',
                                                        default => '<span class="px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-700 rounded-full uppercase">Pendiente</span>',
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
                                                $updateCalculations = function ($get, $set, \Livewire\Component $livewire) {
                                                    $eco = (int) $get('economic_impact') ?: 1;
                                                    $ope = (int) $get('operational_impact') ?: 1;
                                                    $rep = (int) $get('reputational_impact') ?: 1;
                                                    $prob = (int) $get('probability') ?: 1;
                                                    $mgnt = (int) $get('management_level') ?: 0;
                                                    $type = $get('type');

                                                    $impactoTotal = max($eco, $ope, $rep);
                                                    $set('impact', $impactoTotal);

                                                    $baseScore = $prob * $impactoTotal;

                                                    if ($type === 'residual') {
                                                        $effectiveness = 0;
                                                        $controlsCount = 0;

                                                        $record = method_exists($livewire, 'getRecord') ? $livewire->getRecord() : null;
                                                        if ($record instanceof Risk) {
                                                            $effectiveness = $record->getAverageControlEffectiveness();
                                                            $controlsCount = $record->controls()->count();
                                                        }

                                                        $reductionRatio = ($controlsCount > 0) ? ($effectiveness / 100) : ($mgnt / 100);
                                                        $reductionRatio = max(0, min(1, $reductionRatio));

                                                        $set('score', (int) ceil($baseScore * (1 - $reductionRatio)));
                                                    } else {
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

                            ]), // Fin Evidencias y Análisis

                        Tab::make('4. Controles y Acciones')
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
                                                $count = count($get('controls') ?? []);
                                                $limit = 5;

                                                // Definición de clases para el "badge"
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
                                                $items = $component->getState();
                                                $keys = array_keys($items);
                                                $index = array_search($uuid, $keys) + 1;
                                                $title = $state['title'] ?? 'Nuevo control';

                                                return "Control #{$index}: {$title}";
                                            })
                                            ->grid(3)
                                            ->disabled(fn($get) => $get('status') === Risk::STATUS_CERRADO)
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Título')
                                                    ->required()
                                                    ->placeholder('Nombre de la actividad de control')
                                                    ->columnSpan(3),

                                                // Fila clasifícación
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
                                                    ->live()
                                                    ->hint(function (Get $get) {
                                                        $date = $get('due_date');
                                                        if (! $date) {
                                                            return null;
                                                        }
                                                        $carbon = Carbon::parse($date); // esta funcion de parse ayuda a trabajar con las fecha , transformandola en objetos
                                                        if ($carbon->isPast()) {
                                                            return 'Vencido';
                                                        }
                                                        $diff = (int) Carbon::today()->diffInDays($carbon);
                                                        if ($diff <= 15) {
                                                            return "Próximo ({$diff} días)";
                                                        }

                                                        return 'En plazo';
                                                    })
                                                    ->hintColor(function (Get $get) {
                                                        $date = $get('due_date');
                                                        if (! $date) {
                                                            return null;
                                                        }
                                                        $carbon = Carbon::parse($date);
                                                        if ($carbon->isPast()) {
                                                            return 'danger';
                                                        }
                                                        if ((int) Carbon::today()->diffInDays($carbon) <= 15) {
                                                            return 'warning';
                                                        }

                                                        return 'success';
                                                    })
                                                    ->hintIcon(function (Get $get) {
                                                        $date = $get('due_date');
                                                        if (! $date) {
                                                            return null;
                                                        }
                                                        $carbon = Carbon::parse($date);
                                                        if ($carbon->isPast()) {
                                                            return 'heroicon-m-exclamation-circle';
                                                        }
                                                        if ((int) Carbon::today()->diffInDays($carbon) <= 15) {
                                                            return 'heroicon-m-clock';
                                                        }

                                                        return 'heroicon-m-check-circle';
                                                    })
                                                    ->columnSpan(1),

                                                Textarea::make('description')
                                                    ->label('Descripción')
                                                    ->rows(3)
                                                    ->columnSpan(3),
                                                Select::make('responsable_id')
                                                    ->label('Responsable')
                                                    ->searchable()
                                                    ->options(fn() => User::all()->mapWithKeys(fn($user) => [
                                                        $user->id => "{$user->name} {$user->last_name} ({$user->job_title})",
                                                    ]))
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
                                                        if (!$record || !$record->id) return 'Guarde el control para ver las evidencias.';

                                                        $docs = RiskDocument::where('control_id', $record->id)->get();
                                                        if ($docs->isEmpty()) return 'Sin evidencias vinculadas.';

                                                        $html = '<div class="flex flex-col gap-1 mt-1">';
                                                        foreach ($docs as $doc) {
                                                            $url = "/admin/risk-documents/{$doc->id}/edit";
                                                            $statusColor = match ($doc->status) {
                                                                'validada' => 'text-green-600',
                                                                'rechazada' => 'text-red-600',
                                                                default => 'text-amber-600',
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
                                                if ($record && ! $record->responsable_id) {
                                                    $set('responsable_id', $get('../../responsable_id'));
                                                }
                                            })
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $get) {
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
                                            ->addable(false)
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
                                                        return function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                                            if ($value === Action::STATUS_CERRADA) {
                                                                $actionId = $get('id');
                                                                if ($actionId) {
                                                                    $action = Action::find($actionId);
                                                                    if ($action && !$action->hasEvidence()) {
                                                                        $fail('No puedes cerrar una acción sin al menos una evidencia vinculada.');
                                                                    }
                                                                } else {
                                                                    // Si es una acción nueva en el repeater sin ID aún
                                                                    $fail('Primero debe guardar la acción y adjuntar una evidencia para poder cerrarla.');
                                                                }
                                                            }
                                                        };
                                                    })
                                                    ->columnSpan(1),
                                                Select::make('responsable_id')
                                                    ->label('Responsable')
                                                    ->searchable()
                                                    ->options(fn() => User::all()->mapWithKeys(fn($user) => [
                                                        $user->id => "{$user->name} {$user->last_name} ({$user->job_title})",
                                                    ]))
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
                                                                $editUrl = "/admin/risk-documents/{$doc->id}/edit";
                                                                $title = htmlspecialchars($doc->title, ENT_QUOTES);
                                                                $statusLabel = match ($doc->status) {
                                                                    'validada' => '<span class="text-[9px] font-bold text-green-600 uppercase">Validada</span>',
                                                                    'rechazada' => '<span class="text-[9px] font-bold text-red-600 uppercase">Rechazada</span>',
                                                                    default => '<span class="text-[9px] font-bold text-amber-600 uppercase">Pendiente</span>',
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

                                                        $riskId = $record?->risk_id;
                                                        $addUrl = "/admin/risk-documents/create?action_id={$actionId}&risk_id={$riskId}&classification=action_evidence";
                                                        $goToUrl = "/admin/actions/{$actionId}/edit";

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
                                    ])

                            ]), // Fin Controles y Acciones

                        Tab::make('5. Seguimiento e Historial')
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
                                                        // Lógica para guardar histórico si cambia el valor
                                                        if (!$record) return;

                                                        $history = $get('history') ?? [];
                                                        $history[] = [
                                                            'value' => $state,
                                                            'measured_at' => now()->toDateTimeString(),
                                                        ];
                                                        $set('history', $history);
                                                        $set('last_measured_at', now());
                                                    })
                                                    ->columnSpan(1),
                                                Placeholder::make('status')
                                                    ->label('Estado')
                                                    ->content(function ($get) {
                                                        $current = $get('current_value');
                                                        $target = $get('target_value');
                                                        $tolerance = $get('tolerance_level');

                                                        if (!$current || !$target) return new HtmlString('<span class="text-gray-400 font-bold italic">Sin datos</span>');

                                                        // Básicamente si es numérico comparamos, si no, es cualitativo.
                                                        $currentNum = filter_var($current, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                                        $targetNum = filter_var($target, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                                                        $toleranceNum = $tolerance ? filter_var($tolerance, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null;

                                                        if (is_numeric($currentNum) && is_numeric($targetNum)) {
                                                            $diff = abs($currentNum - $targetNum);
                                                            if ($currentNum <= $targetNum) {
                                                                return new HtmlString('<x-filament::badge color="success" icon="heroicon-m-check-circle">Dentro de Objetivo</x-filament::badge>');
                                                            }

                                                            if ($toleranceNum && $currentNum > $toleranceNum) {
                                                                return new HtmlString('<x-filament::badge color="danger" icon="heroicon-m-x-circle">Supera Tolerancia</x-filament::badge>');
                                                            }

                                                            return new HtmlString('<x-filament::badge color="warning" icon="heroicon-m-exclamation-triangle">Alerta de Seguimiento</x-filament::badge>');
                                                        }

                                                        // Cualitativo: Simple coincidencia
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
                                                                $history = $get('history') ?? [];
                                                                if (empty($history)) return 'No hay registros históricos.';

                                                                $rows = collect($history)->sortByDesc('measured_at')->map(function ($item) {
                                                                    $date = Carbon::parse($item['measured_at'])->format('d/m/Y H:i');
                                                                    return "<tr><td class='p-1'>{$date}</td><td class='p-1 font-bold'>{$item['value']}</td></tr>";
                                                                })->implode('');

                                                                return new HtmlString("<table class='w-full text-xs text-left'><thead><tr><th>Fecha</th><th>Valor</th></tr></thead><tbody>{$rows}</tbody></table>");
                                                            })
                                                    ])->columnSpan(2)
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
                                        Repeater::make('statusHistories')
                                            ->relationship('statusHistories')
                                            ->hiddenLabel()->addable(false)->deletable(false)->reorderable(false)->columns(3)
                                            ->schema([
                                                TextInput::make('old_status')->label('Anterior')->disabled(),
                                                TextInput::make('new_status')->label('Nuevo')->disabled(),
                                                TextInput::make('created_at')->label('Fecha')->disabled(),
                                            ]),
                                    ]),
                            ]), // Cierre Tab 5
                    ]), // Cierre tabs array
            ]);
    }
}
