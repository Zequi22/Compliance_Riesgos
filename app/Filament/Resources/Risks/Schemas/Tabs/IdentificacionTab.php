<?php

namespace App\Filament\Resources\Risks\Schemas\Tabs;

use App\Models\Risk;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

/**
 * Tab 1 — Identificación
 * Recoge los datos básicos del riesgo: nombre, descripción, clasificación,
 * marco penal, fechas de revisión y asignación del responsable titular.
 */
class IdentificacionTab
{
    public static function make(): Tab
    {
        return Tab::make('1. Identificación')
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
                                                'Identificado'       => Risk::STATUS_IDENTIFICADO,
                                                'Evaluado'           => Risk::STATUS_EVALUADO,
                                                'En tratamiento'     => Risk::STATUS_TRATAMIENTO,
                                                'En seguimiento'     => Risk::STATUS_SEGUIMIENTO,
                                                'Cerrado / Revisado' => Risk::STATUS_CERRADO,
                                            ])
                                            ->default(Risk::STATUS_IDENTIFICADO)
                                            ->required()
                                            ->columnSpan(1),

                                        Select::make('treatment')
                                            ->label('Tratamiento del Riesgo')
                                            ->prefixIcon('heroicon-m-shield-check')
                                            ->options([
                                                Risk::TREATMENT_ACEPTAR    => 'Aceptar',
                                                Risk::TREATMENT_EVITAR     => 'Evitar',
                                                Risk::TREATMENT_REDUCIR    => 'Reducir',
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

                                        // Permite crear una nueva unidad organizativa desde el propio selector
                                        Select::make('organizational_unit_id')
                                            ->label('Área / Proceso / Departamento')
                                            ->prefixIcon('heroicon-m-building-office')
                                            ->relationship('organizationalUnit', 'name')
                                            ->createOptionForm([
                                                TextInput::make('name')->label('Nombre')->required(),
                                                Select::make('type')
                                                    ->label('Tipo')
                                                    ->options([
                                                        'Área'                 => 'Área',
                                                        'Proceso'              => 'Proceso',
                                                        'Proceso Estratégico'  => 'Proceso Estratégico',
                                                        'Proceso Operativo'    => 'Proceso Operativo',
                                                        'Proceso de Apoyo'     => 'Proceso de Apoyo',
                                                        'Departamento'         => 'Departamento',
                                                    ])
                                                    ->required(),
                                            ])
                                            ->searchable()
                                            ->preload()
                                            ->nullable()
                                            ->placeholder('Seleccione del catálogo')
                                            ->columnSpan(2),

                                        // Lista de delitos del Código Penal vinculables al riesgo (compliance penal)
                                        Select::make('type_crime')
                                            ->label('Marco Penal')
                                            ->prefixIcon('heroicon-m-scale')
                                            ->placeholder('Seleccione el delito asociado')
                                            ->options([
                                                'Corrupción y Mercado' => [
                                                    'Corrupción en los negocios'       => 'Corrupción en los negocios',
                                                    'Cohecho'                          => 'Cohecho (Soborno)',
                                                    'Tráfico de influencias'           => 'Tráfico de influencias',
                                                    'Uso de información privilegiada'  => 'Uso de información privilegiada',
                                                ],
                                                'Económicos y Fraude' => [
                                                    'Estafa'                                => 'Estafa / Fraude',
                                                    'Blanqueo de capitales'                 => 'Blanqueo de capitales',
                                                    'Insolvencias punibles'                 => 'Insolvencias punibles (Alzamiento)',
                                                    'Delitos contra Hacienda y Seg. Social' => 'Delitos Fiscales y Seguridad Social',
                                                ],
                                                'Tecnológicos y Propiedad' => [
                                                    'Descubrimiento y revelación de secretos' => 'Hacking / Revelación de secretos',
                                                    'Daños informáticos'                      => 'Daños informáticos',
                                                    'Propiedad Intelectual e Industrial'      => 'Propiedad Intelectual e Industrial',
                                                ],
                                                'Otros' => [
                                                    'Delitos contra el medio ambiente' => 'Delitos contra el medio ambiente',
                                                    'Delitos contra la salud pública'  => 'Delitos contra la salud pública',
                                                    'Financiación del terrorismo'      => 'Financiación del terrorismo',
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

                                        // Muestra el nivel de criticidad calculado a partir de las evaluaciones guardadas
                                        Placeholder::make('criticality')
                                            ->label('Nivel de Criticidad Base')
                                            ->content(function (?Risk $record) {
                                                // $record = el Risk cargado de BD; null si es un riesgo nuevo
                                                $criticality = $record ? ($record->criticality ?? 'No Evaluado') : 'No Evaluado';
                                                $color       = $record ? ($record->criticalityColor ?? 'gray') : 'gray';

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
                                // $state = ID del usuario seleccionado; $set escribe en el formulario en tiempo real
                                $user = User::find($state);

                                if ($user) {
                                    // Mapa campo → propiedad del modelo User para rellenar los campos de solo lectura
                                    foreach ([
                                        'responsable_nombre'       => 'name',
                                        'responsable_apellidos'    => 'last_name',
                                        'responsable_area'         => 'area',
                                        'responsable_equipo'       => 'team',
                                        'responsable_departamento' => 'department',
                                        'responsable_cargo'        => 'job_title',
                                    ] as $field => $property) {
                                        $set($field, $user->{$property});
                                    }
                                }

                                // Propagamos el responsable a todas las acciones del repeater.
                                // array_keys($actions) devuelve los UUIDs de cada item para apuntar al campo correcto.
                                foreach (array_keys($get('actions') ?? []) as $uuid) {
                                    $set("actions.{$uuid}.responsable_id", $state);
                                }

                                // Igual para controles: actualizamos nombre y área del propietario
                                foreach (array_keys($get('controls') ?? []) as $uuid) {
                                    $set("controls.{$uuid}.owner_name", $user->name ?? '');
                                    $set("controls.{$uuid}.owner_area", $user->area ?? '');
                                }
                            })
                            ->columnSpanFull(),

                        // Campos informativos de solo lectura; se rellenan al seleccionar el responsable (no se guardan en BD)
                        self::readonlyUserField('responsable_nombre',       'Nombre',         'heroicon-m-user',              'name'),
                        self::readonlyUserField('responsable_apellidos',    'Apellidos',      'heroicon-m-user',              'last_name'),
                        self::readonlyUserField('responsable_area',         'Área Interna',   'heroicon-m-building-office-2', 'area'),
                        self::readonlyUserField('responsable_departamento', 'Departamento',   'heroicon-m-building-office',   'department'),
                        self::readonlyUserField('responsable_equipo',       'Equipo / Team',  'heroicon-m-users',             'team'),
                        self::readonlyUserField('responsable_cargo',        'Cargo / Puesto', 'heroicon-m-briefcase',         'job_title'),
                    ]),
            ]);
    }

    /**
     * Campo de solo lectura que muestra una propiedad del usuario seleccionado como responsable.
     * No persiste en BD (dehydrated: false); se actualiza reactivamente al cambiar responsable_id.
     *
     * @param  string  $name      Nombre del campo en el formulario (ej: 'responsable_nombre')
     * @param  string  $label     Etiqueta visible
     * @param  string  $icon      Icono heroicon para el prefijo
     * @param  string  $property  Propiedad del modelo User a mostrar (ej: 'name', 'area')
     */
    private static function readonlyUserField(string $name, string $label, string $icon, string $property): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->prefixIcon($icon)
            ->disabled()
            ->dehydrated(false)
            ->formatStateUsing(fn($get) => User::find($get('responsable_id'))?->{$property})
            ->live(onBlur: true);
    }
}
