<?php

namespace App\Filament\Resources\OrganizationalUnits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\ColumnManagerLayout;

class OrganizationalUnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns([
                'md' => 2,
                'lg' => 4,
            ])
            ->filtersTriggerAction(
                fn ($action) => $action
                    ->button()
                    ->color('white')
                    ->label('Filtros')
                    ->slideOver(),
            )
            ->columnManagerLayout(ColumnManagerLayout::Modal)
            ->toggleColumnsTriggerAction(
                fn ($action) => $action
                    ->button()
                    ->color('white')
                    ->label('Columnas')
                    ->slideOver(),
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Área' => 'info',
                        'Proceso', 'Proceso Estratégico', 'Proceso Operativo', 'Proceso de Apoyo' => 'warning',
                        'Departamento' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(60)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('risks_count')
                    ->label('Riesgos')
                    ->counts('risks')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('users_count')
                    ->label('Usuarios')
                    ->counts('users')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('controls_count')
                    ->label('Controles')
                    ->counts('controls')
                    ->sortable()
                    ->badge()
                    ->color('warning'),

                TextColumn::make('actions_count')
                    ->label('Acciones')
                    ->counts('actions')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Última modificación')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'Área' => 'Área',
                        'Proceso' => 'Proceso',
                        'Proceso Estratégico' => 'Proceso Estratégico',
                        'Proceso Operativo' => 'Proceso Operativo',
                        'Proceso de Apoyo' => 'Proceso de Apoyo',
                        'Departamento' => 'Departamento',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todas')
                    ->trueLabel('Activas')
                    ->falseLabel('Inactivas'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
