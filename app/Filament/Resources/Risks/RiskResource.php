<?php

namespace App\Filament\Resources\Risks;

use App\Filament\Resources\Risks\Pages\CreateRisk;
use App\Filament\Resources\Risks\Pages\EditRisk;
use App\Filament\Resources\Risks\Pages\ListRisks;
use App\Filament\Resources\Risks\Schemas\RiskForm;
use App\Models\OrganizationalUnit;
use App\Models\Risk;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\ColumnManagerLayout;
use Illuminate\Database\Eloquent\Builder;
use Torgodly\Html2Media\Actions\Html2MediaAction;
use Filament\Actions\Exports\Enums\ExportFormat;

class RiskResource extends Resource
{
    protected static ?string $model = Risk::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    protected static string|\UnitEnum|null $navigationGroup = 'Gestión de Riesgos';

    protected static ?string $navigationLabel = 'Registro de Riesgos';

    protected static ?string $modelLabel = 'Riesgo';

    protected static ?string $pluralModelLabel = 'Riesgos';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RiskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns([
                'md' => 2,
                'lg' => 4,
            ])
            ->filtersTriggerAction(
                fn($action) => $action
                    ->button()
                    ->color('white')
                    ->label('Filtros')
                    ->slideOver(),
            )
            ->columnManagerLayout(ColumnManagerLayout::Modal)
            ->toggleColumnsTriggerAction(
                fn($action) => $action
                    ->button()
                    ->color('white')
                    ->label('Columnas')
                    ->slideOver(),
            )
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['assessments', 'organizationalUnit']))
            ->columns([
                TextColumn::make('name')
                    ->label('Riesgo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('responsable.name')
                    ->label('Responsable')
                    // ->description(fn($record) => $record->responsable?->last_name)
                    ->formatStateUsing(function ($record) {
                        if (! $record->responsable) {
                            return '-';
                        }

                        return "{$record->responsable->name} {$record->responsable->last_name}";
                    })
                    ->sortable()
                    ->searchable(['name', 'last_name']),
                TextColumn::make('treatment')
                    ->label('Tratamiento')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->color(fn(?string $state): string => match ($state) {
                        Risk::TREATMENT_ACEPTAR => 'info',
                        Risk::TREATMENT_EVITAR => 'danger',
                        Risk::TREATMENT_REDUCIR => 'warning',
                        Risk::TREATMENT_TRANSFERIR => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('category')
                    ->label('Categoría'),
                TextColumn::make('organizationalUnit.name')
                    ->label('Área / Proceso')
                    ->sortable()
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('criticality')
                    ->label('Criticidad')
                    ->badge()
                    ->state(fn($record) => $record->criticality)
                    ->color(fn($record) => $record->criticalityColor),
                TextColumn::make('responsable.area')
                    ->label('Área')
                    ->toggleable(),
                TextColumn::make('responsable.department')
                    ->label('Departamento')
                    ->toggleable(),
                TextColumn::make('responsable.team')
                    ->label('Equipo')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        Risk::STATUS_IDENTIFICADO => 'gray',
                        Risk::STATUS_EVALUADO => 'info',
                        Risk::STATUS_TRATAMIENTO => 'warning',
                        Risk::STATUS_SEGUIMIENTO => 'primary',
                        Risk::STATUS_CERRADO => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('next_review_at')
                    ->label('Próxima Revisión')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn($record) => $record->isReviewOverdue() ? 'danger' : 'gray')
                    ->weight(fn($record) => $record->isReviewOverdue() ? 'bold' : 'normal')
                    ->icon(fn($record) => $record->isReviewOverdue() ? 'heroicon-m-exclamation-circle' : null)
                    ->iconPosition('after'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Categoría')
                    ->options(fn() => Risk::query()
                        ->whereNotNull('category')
                        ->distinct()
                        ->pluck('category', 'category')
                        ->toArray()),
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        Risk::STATUS_IDENTIFICADO => Risk::STATUS_IDENTIFICADO,
                        Risk::STATUS_EVALUADO => Risk::STATUS_EVALUADO,
                        Risk::STATUS_TRATAMIENTO => Risk::STATUS_TRATAMIENTO,
                        Risk::STATUS_SEGUIMIENTO => Risk::STATUS_SEGUIMIENTO,
                        Risk::STATUS_CERRADO => Risk::STATUS_CERRADO,
                    ]),
                SelectFilter::make('treatment')
                    ->label('Tratamiento')
                    ->options([
                        Risk::TREATMENT_ACEPTAR => 'Aceptar',
                        Risk::TREATMENT_EVITAR => 'Evitar',
                        Risk::TREATMENT_REDUCIR => 'Reducir',
                        Risk::TREATMENT_TRANSFERIR => 'Transferir',
                    ]),
                SelectFilter::make('organizational_unit_id')
                    ->label('Filtrar por Proceso / Área')
                    ->options(
                        fn() => OrganizationalUnit::query()
                            ->orderBy('name')
                            ->get()
                            ->groupBy('type')
                            ->map(fn($units) => $units->pluck('name', 'id'))
                            ->toArray()
                    )
                    ->searchable()
                    ->placeholder('Todos'),
                SelectFilter::make('risk_level')
                    ->label('Nivel de Riesgo')
                    ->options([
                        'Muy Bajo' => 'Muy Bajo',
                        'Bajo' => 'Bajo',
                        'Medio' => 'Medio',
                        'Alto' => 'Alto',
                        'Crítico' => 'Crítico',
                        'No Evaluado' => 'No Evaluado',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        $value = $data['value'];

                        if ($value === 'No Evaluado') {
                            return $query->doesntHave('assessments');
                        }

                        $range = match ($value) {
                            'Muy Bajo' => [1, 2],
                            'Bajo' => [3, 4],
                            'Medio' => [5, 9],
                            'Alto' => [10, 14],
                            'Crítico' => [15, 25],
                        };

                        return $query->where(function ($q) use ($range) {
                            $q->whereHas('assessments', function ($q2) use ($range) {
                                $q2->where('type', 'residual')->whereBetween('score', $range);
                            })->orWhere(function ($q3) use ($range) {
                                $q3->whereDoesntHave('assessments', fn($q4) => $q4->where('type', 'residual'))
                                    ->whereHas('assessments', function ($q5) use ($range) {
                                        $q5->where('type', 'inherent')->whereBetween('score', $range);
                                    });
                            });
                        });
                    }),
                Filter::make('overdue_review')
                    ->label('Revisión Vencida')
                    ->query(fn(Builder $query) => $query->where('next_review_at', '<', now())),
                Filter::make('upcoming_review')
                    ->label('Próxima semana')
                    ->query(fn(Builder $query) => $query->whereBetween('next_review_at', [now(), now()->addWeek()])),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                    \Filament\Actions\ExportBulkAction::make()
                        ->label('Exportar Riesgos')
                        ->icon('heroicon-m-arrow-down-tray')
                        ->exporter(\App\Filament\Exports\RiskExporter::class)
                        ->formats([
                            ExportFormat::Csv,
                            ExportFormat::Xlsx,
                        ])
                        ->slideOver()
                ])->label('Acciones de Exportación')->color('primary')->icon('heroicon-m-chevron-down'),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Ficha de Riesgo')
                    ->icon('heroicon-m-eye')
                    ->color('warning'),
                Html2MediaAction::make('Imprimir Ficha')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->print()
                    ->preview()
                    ->savePdf()
                    ->orientation('portrait')
                    ->format('a4')
                    ->margins(20, 20, 20, 20)
                    ->filename(fn($record) => 'Ficha_Riesgo_' . str_replace(' ', '_', $record->name))
                    ->content(fn($record) => view('pdf.risk-card', ['risk' => $record])),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRisks::route('/'),
            'create' => CreateRisk::route('/create'),
            'edit' => EditRisk::route('/{record}/edit'),
        ];
    }
}
