<?php

namespace App\Filament\Resources\Actions\Tables;

use App\Models\Action;
use App\Models\Risk;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\Select;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\ColumnManagerLayout;

use Illuminate\Database\Eloquent\Builder;

class ActionsTable
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
            ->defaultSort(fn(Builder $query) => $query->orderByRaw("CASE WHEN priority = 'alta' THEN 1 WHEN priority = 'media' THEN 2 WHEN priority = 'baja' THEN 3 ELSE 4 END ASC")->orderBy('commitment_date', 'asc'))
            ->columns([
                TextColumn::make('title')
                    ->label('Acción')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),
                TextColumn::make('notes')
                    ->label('Notas')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('risk.name')
                    ->label('Riesgo Asociado')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->toggleable()
                    ->wrap(),
                TextColumn::make('responsable.name')
                    ->label('Responsable')
                    ->formatStateUsing(function ($record) {
                        if (! $record->responsable) {
                            return '-';
                        }
                        return "{$record->responsable->name} {$record->responsable->last_name}";
                    })
                    ->searchable(['name', 'last_name'])
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('due_date')
                    ->label('Fecha Límite')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Fecha Inicio')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('commitment_date')
                    ->label('Fecha Compromiso')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable()
                    ->color(fn(Action $record) => $record->isOverdue() ? 'danger' : null)
                    ->icon(fn(Action $record) => $record->isOverdue() ? 'heroicon-m-exclamation-triangle' : null),
                TextColumn::make('actual_closure_date')
                    ->label('Fecha Cierre Real')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        Action::PRIORITY_ALTA => 'Alta',
                        Action::PRIORITY_MEDIA => 'Media',
                        Action::PRIORITY_BAJA => 'Baja',
                        default => ucfirst($state),
                    })
                    ->color(fn(string $state): string => Action::getPriorityColor($state))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        Action::STATUS_PENDIENTE => 'Pendiente',
                        Action::STATUS_EN_CURSO => 'En curso',
                        Action::STATUS_BLOQUEADA => 'Bloqueada',
                        Action::STATUS_EN_REVISION => 'En revisión',
                        Action::STATUS_CERRADA => 'Cerrada',
                        Action::STATUS_CANCELADA => 'Cancelada',
                        default => ucfirst($state),
                    })
                    ->color(fn(string $state): string => Action::getStatusColor($state))
                    ->toggleable(),
                TextColumn::make('progress')
                    ->label('Progreso')
                    ->formatStateUsing(fn($state) => $state . '%')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('documents_count')
                    ->counts('documents')
                    ->label('Evidencias')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('latestUpdate.comment')
                    ->label('Última Actualización / Motivo')
                    ->description(fn($record) => $record->latestUpdate && $record->latestUpdate->user ? "Por: {$record->latestUpdate->user->name}" : '')
                    ->wrap()
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('dependsOn.title')
                    ->label('Depende de')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('organizationalUnit.name')
                    ->label('Área / Unidad')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('vencimiento')
                    ->form([
                        Select::make('rango')
                            ->label('Vencimiento')
                            ->options([
                                'vencidas' => 'Vencidas',
                                '7_dias'   => 'Próximas a vencer (7 días)',
                                '14_dias'  => 'Próximas a vencer (14 días)',
                                '30_dias'  => 'Próximas a vencer (30 días)',
                            ])
                            ->placeholder('Todas')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['rango'] ?? null,
                            function (Builder $query, $rango) {
                                if ($rango === 'vencidas') {
                                    return $query->where('due_date', '<', now()->startOfDay())
                                        ->whereNotIn('status', [Action::STATUS_CERRADA, Action::STATUS_CANCELADA]);
                                }

                                $dias = (int) str_replace('_dias', '', $rango);
                                return $query->whereBetween('due_date', [now()->startOfDay(), now()->addDays($dias)->endOfDay()])
                                    ->whereNotIn('status', [Action::STATUS_CERRADA, Action::STATUS_CANCELADA]);
                            }
                        );
                    }),
                TernaryFilter::make('asignacion')
                    ->label('Asignación de Responsable')
                    ->placeholder('Todas')
                    ->trueLabel('Sin Asignar')
                    ->falseLabel('Asignadas')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNull('responsable_id'),
                        false: fn(Builder $query) => $query->whereNotNull('responsable_id'),
                    ),
                SelectFilter::make('organizational_unit_id')
                    ->label('Área / Unidad')
                    ->relationship('organizationalUnit', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(Action::getStatusOptions()),
                SelectFilter::make('risk_id')
                    ->label('Riesgo')
                    ->options(fn() => Risk::query()->orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable(),
                SelectFilter::make('responsable_id')
                    ->label('Responsable')
                    ->options(fn() => User::query()
                        ->orderBy('name')
                        ->get(['id', 'name', 'last_name'])
                        ->mapWithKeys(fn($user) => [$user->id => "{$user->name} {$user->last_name}"])
                        ->toArray())
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

            ]);
    }
}
