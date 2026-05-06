<?php

namespace App\Filament\Resources\Actions\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;

class UpdatesRelationManager extends RelationManager
{
    protected static string $relationship = 'updates';

    protected static ?string $title = 'Log de Avances';
    protected static ?string $modelLabel = 'Actualización';
    protected static ?string $pluralModelLabel = 'Actualizaciones';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Textarea::make('comment')
                    ->label('Comentario / Avance')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('comment')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->formatStateUsing(fn ($record) => $record->user ? "{$record->user->name} {$record->user->last_name}" : 'Sistema')
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Comentario')
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->toolbarActions([
                Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        return $data;
                    }),
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
