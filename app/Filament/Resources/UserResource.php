<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\ColumnManagerLayout;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Usuarios';

    protected static ?string $modelLabel = 'Usuario';

    protected static ?string $pluralModelLabel = 'Usuarios';

    protected static string|\UnitEnum|null $navigationGroup = 'Seguridad';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nombre')->required(),
            TextInput::make('last_name')->label('Apellidos')->required(),
            Select::make('organizational_unit_id')
                ->label('Área / Proceso / Dpto.')
                ->relationship('organizationalUnit', 'name')
                ->searchable()
                ->preload()
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
                ->columnSpanFull(),
            TextInput::make('job_title')->label('Cargo'),
            TextInput::make('email')->label('Email')->email()->required(),
            Toggle::make('is_active')->label('Activo'),
            Select::make('roles')
                ->label('Rol')
                ->relationship('roles', 'name')
                ->options(fn() => Role::query()->pluck('name', 'id'))
                ->preload()
                ->searchable(),
        ]);
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
                    ->description(fn(User $record): string => $record->last_name ?? ''),
                TextColumn::make('roles.name')
                    ->label('Rol')
                    ->separator(',')
                    ->searchable(),
                TextColumn::make('job_title')
                    ->label('Cargo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('organizationalUnit.name')
                    ->label('Unidad Organizativa')
                    ->searchable()
                    ->sortable()
                    ->description(fn(User $record): string => $record->organizationalUnit?->type ?? ''),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
                SelectFilter::make('organizational_unit_id')
                    ->label('Unidad Organizativa')
                    ->relationship('organizationalUnit', 'name'),
            ])
            ->recordActions([
                EditAction::make()->label('Editar')->color('warning'),
                DeleteAction::make()->label('Eliminar'),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\User\Pages\ListUsers::route('/'),
            'create' => \App\Filament\Resources\User\Pages\CreateUser::route('/create'),
            'edit' => \App\Filament\Resources\User\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
