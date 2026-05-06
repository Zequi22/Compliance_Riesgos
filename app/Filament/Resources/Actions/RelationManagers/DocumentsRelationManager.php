<?php

namespace App\Filament\Resources\Actions\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Evidencias';
    protected static ?string $modelLabel = 'Evidencia';
    protected static ?string $pluralModelLabel = 'Evidencias';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\TextInput::make('title')
                    ->label('Título')
                    ->required(),
                Components\Select::make('document_type')
                    ->label('Tipo de documento')
                    ->required()
                    ->options([
                        'política'      => 'Política',
                        'procedimiento' => 'Procedimiento',
                        'registro'      => 'Registro',
                        'captura'       => 'Captura',
                        'informe'       => 'Informe',
                        'otro'          => 'Otro',
                    ]),
                Components\Textarea::make('description')
                    ->label('Descripción')
                    ->required()
                    ->maxLength(255),
                Components\DatePicker::make('document_date')
                    ->label('Fecha del documento')
                    ->required(),
                Components\FileUpload::make('file_path')
                    ->label('Archivo principal')
                    ->required()
                    ->directory('risk-documents')
                    ->downloadable()
                    ->openable(),
                Components\FileUpload::make('file_paths')
                    ->label('Archivos adicionales')
                    ->multiple()
                    ->directory('risk-documents')
                    ->downloadable()
                    ->openable(),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => route('filament.admin.resources.risk-documents.view', ['record' => $record->id]))
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('document_type')
                    ->label('Tipo')
                    ->badge()
                    ->sortable(),
                TextColumn::make('document_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Validación')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'validada' => 'Validada',
                        'rechazada' => 'Rechazada',
                        default => 'Pendiente',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'validada' => 'success',
                        'rechazada' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('uploadedBy.name')
                    ->label('Subido por')
                    ->formatStateUsing(fn ($record) => $record->uploadedBy ? "{$record->uploadedBy->name} {$record->uploadedBy->last_name}" : 'Sistema'),
            ])
            ->filters([
                //
            ])
            ->toolbarActions([
                Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['associated_type'] = 'action';
                        $data['risk_id'] = $this->getOwnerRecord()->risk_id;
                        $data['uploaded_by'] = Auth::id();
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
