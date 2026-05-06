<?php

namespace App\Filament\Resources\Actions\Schemas;

use App\Models\Action;
use App\Models\Risk;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class ActionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Detalle descriptivo y contexto del riesgo asociado.')
                    ->icon('heroicon-m-information-circle')
                    ->iconColor('info')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->label('Título o Descripción Breve')
                            ->required()
                            ->columnSpanFull(),
                        Grid::make(4)->schema([
                            Select::make('risk_id')
                                ->label('Riesgo Asociado')
                                ->relationship('risk', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->default(fn() => request('risk_id') ? (int) request('risk_id') : null)
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    if (!$state) return;
                                    $risk = Risk::find($state);
                                    if ($risk) {
                                        $priority = match ($risk->criticality) {
                                            'Crítico', 'Alto' => Action::PRIORITY_ALTA,
                                            'Medio' => Action::PRIORITY_MEDIA,
                                            'Bajo', 'Muy Bajo', 'No Evaluado' => Action::PRIORITY_BAJA,
                                            default => Action::PRIORITY_MEDIA,
                                        };
                                        $set('priority', $priority);
                                    }
                                }),
                            Select::make('status')
                                ->label('Estado')
                                ->options(Action::getStatusOptions())
                                ->default(Action::STATUS_PENDIENTE)
                                ->required()
                                ->live()
                                ->rule(function (Get $get, ?\Illuminate\Database\Eloquent\Model $record) {
                                    return function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                        if ($value === Action::STATUS_CERRADA) {
                                            if (! $record || ! $record->hasEvidence()) {
                                                $fail('No se puede cerrar una acción sin subir al menos una evidencia.');
                                            }
                                        }
                                    };
                                }),
                            Select::make('priority')
                                ->label('Prioridad')
                                ->options(Action::getPriorityOptions())
                                ->default(Action::PRIORITY_MEDIA)
                                ->required(),
                            TextInput::make('progress')
                                ->label('% Progreso')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->step(1)
                                ->default(0)
                                ->suffix('%')
                                ->required(),
                            Select::make('depends_on_action_id')
                                ->label('Depende de (Opcional)')
                                ->relationship('dependsOn', 'title')
                                ->searchable()
                                ->preload()
                                ->placeholder('Selecciona una acción')
                                ->columnSpanFull(),
                        ]),
                    ]),

                Section::make('Tiempos y Planificación')
                    ->description('Seguimiento de plazos de ejecución y compromiso.')
                    ->icon('heroicon-m-clock')
                    ->iconColor('warning')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        DatePicker::make('due_date')
                            ->label('Fecha Límite')
                            ->required(),
                        DatePicker::make('start_date')
                            ->label('Fecha Inicio'),
                        DatePicker::make('commitment_date')
                            ->label('Fecha Compromiso'),
                        DatePicker::make('actual_closure_date')
                            ->label('Fecha Cierre Real'),
                    ]),

                Section::make('Responsables y Notas')
                    ->icon('heroicon-m-user-group')
                    ->iconColor('success')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('responsable_id')
                                ->label('Responsable')
                                ->relationship('responsable', 'name')
                                ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name)
                                ->searchable(['name', 'last_name'])
                                ->preload()
                                ->default(null),
                            Select::make('organizational_unit_id')
                                ->label('Área / Unidad')
                                ->relationship('organizationalUnit', 'name')
                                ->searchable()
                                ->preload()
                                ->default(null),
                        ]),

                        Textarea::make('notes')
                            ->label('Notas Adicionales')
                            ->rows(3)
                            ->default(null)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
