<?php

namespace App\Filament\Resources\Actions;

use App\Filament\Resources\Actions\Pages\CreateAction;
use App\Filament\Resources\Actions\Pages\EditAction;
use App\Filament\Resources\Actions\Pages\ListActions;
use App\Filament\Resources\Actions\Pages\ViewAction;
use App\Filament\Resources\Actions\Schemas\ActionForm;
use App\Filament\Resources\Actions\Schemas\ActionInfolist;
use App\Filament\Resources\Actions\Tables\ActionsTable;
use App\Models\Action;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ActionResource extends Resource
{
    protected static ?string $model = Action::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static string|\UnitEnum|null $navigationGroup = 'Gestión de Riesgos';
    protected static ?string $navigationLabel = 'Acciones';
    protected static ?string $modelLabel = 'Acción';
    protected static ?string $pluralModelLabel = 'Acciones';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ActionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ActionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\Actions\RelationManagers\UpdatesRelationManager::class,
            \App\Filament\Resources\Actions\RelationManagers\DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActions::route('/'),
            'create' => CreateAction::route('/create'),
            'view' => ViewAction::route('/{record}'),
            'edit' => EditAction::route('/{record}/edit'),
        ];
    }
}
