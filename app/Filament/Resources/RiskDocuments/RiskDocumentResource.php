<?php

namespace App\Filament\Resources\RiskDocuments;

use App\Filament\Resources\RiskDocuments\Pages\CreateRiskDocument;
use App\Filament\Resources\RiskDocuments\Pages\EditRiskDocument;
use App\Filament\Resources\RiskDocuments\Pages\ListRiskDocuments;
use App\Filament\Resources\RiskDocuments\Pages\ViewRiskDocument;
use App\Filament\Resources\RiskDocuments\Schemas\RiskDocumentForm;
use App\Filament\Resources\RiskDocuments\Schemas\RiskDocumentInfolist;
use App\Filament\Resources\RiskDocuments\Tables\RiskDocumentsTable;
use App\Models\RiskDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RiskDocumentResource extends Resource
{
    protected static ?string $model = RiskDocument::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Gestión de Riesgos';

    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $navigationLabel = 'Evidencias';
    protected static ?string $modelLabel = 'Evidencia';
    protected static ?string $pluralModelLabel = 'Evidencias';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return RiskDocumentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RiskDocumentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RiskDocumentsTable::configure($table);
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
            'index' => ListRiskDocuments::route('/'),
            'create' => CreateRiskDocument::route('/create'),
            'view' => ViewRiskDocument::route('/{record}'),
            'edit' => EditRiskDocument::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
