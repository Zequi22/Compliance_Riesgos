<?php

namespace App\Filament\Resources\RiskDocuments\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;

class RiskDocumentsTable
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
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->description(fn($record) => $record->description ? \Illuminate\Support\Str::limit($record->description, 60) : null),
                TextColumn::make('document_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn($state) => ucfirst($state ?? '-'))
                    ->color(fn(?string $state): string => match($state) {
                        'política'      => 'info',
                        'procedimiento' => 'warning',
                        'registro'      => 'success',
                        'captura'       => 'primary',
                        'informe'       => 'gray',
                        default         => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('risk.name')
                    ->label('Riesgo Asociado')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('control.title')
                    ->label('Control')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('action.title')
                    ->label('Acción')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pendiente' => 'Pendiente',
                        'validada'  => 'Validada',
                        'rechazada' => 'Rechazada',
                        default     => ucfirst($state),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'validada'  => 'success',
                        'rechazada' => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('uploadedBy.name')
                    ->label('Subido Por')
                    ->formatStateUsing(function ($record) {
                        if (!$record->uploadedBy) return '-';
                        return "{$record->uploadedBy->name} {$record->uploadedBy->last_name}";
                    }),
                TextColumn::make('validatedBy.name')
                    ->label('Validado Por')
                    ->formatStateUsing(function ($record) {
                        if (!$record->validatedBy) return '-';
                        return "{$record->validatedBy->name} {$record->validatedBy->last_name}";
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Fecha Subida')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // ── Estado ──────────────────────────────────────────────────
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendientes',
                        'validada'  => 'Validadas',
                        'rechazada' => 'Rechazadas',
                    ]),

                // ── Tipo de documento ─────────────────────────────────────
                SelectFilter::make('document_type')
                    ->label('Tipo de documento')
                    ->options([
                        'política'      => 'Política',
                        'procedimiento' => 'Procedimiento',
                        'registro'      => 'Registro',
                        'captura'       => 'Captura',
                        'informe'       => 'Informe',
                        'otro'          => 'Otro',
                    ]),

                // ── Asociado a ────────────────────────────────────────────
                SelectFilter::make('associated_to')
                    ->label('Asociado a')
                    ->options([
                        'risk'    => 'Riesgo (General)',
                        'control' => 'Control',
                        'action'  => 'Acción',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;
                        if (!$value) return $query;

                        return match ($value) {
                            'control' => $query->whereNotNull('control_id'),
                            'action'  => $query->whereNotNull('action_id'),
                            'risk'    => $query->whereNull('control_id')->whereNull('action_id'),
                            default   => $query,
                        };
                    }),

                TrashedFilter::make()->label('Borrados Lógicos'),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
            ]);
    }
}
