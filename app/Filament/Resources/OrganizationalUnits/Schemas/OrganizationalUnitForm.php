<?php

namespace App\Filament\Resources\OrganizationalUnits\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrganizationalUnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos de la Unidad Organizativa')
                    ->description('Define el área, proceso o departamento al que se podrán asociar los componentes del sistema (riesgos, controles, acciones, usuarios).')
                    ->icon('heroicon-m-building-office')
                    ->iconColor('warning')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->prefixIcon('heroicon-m-tag')
                                    ->required()
                                    ->maxLength(150)
                                    ->placeholder('Ej: Gestión Financiera'),

                                Select::make('type')
                                    ->label('Tipo')
                                    ->prefixIcon('heroicon-m-squares-2x2')
                                    ->options([
                                        'Área' => 'Área',
                                        'Proceso' => 'Proceso',
                                        'Proceso Estratégico' => 'Proceso Estratégico',
                                        'Proceso Operativo' => 'Proceso Operativo',
                                        'Proceso de Apoyo' => 'Proceso de Apoyo',
                                        'Departamento' => 'Departamento',
                                    ])
                                    ->required()
                                    ->default('Área'),

                                Textarea::make('description')
                                    ->label('Descripción')
                                    ->placeholder('Breve descripción del alcance o función de esta unidad')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Toggle::make('is_active')
                                    ->label('Activa')
                                    ->helperText('Las unidades inactivas no aparecerán en los selectores del sistema.')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }
}
