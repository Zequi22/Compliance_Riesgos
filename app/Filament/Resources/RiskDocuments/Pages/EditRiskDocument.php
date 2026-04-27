<?php

namespace App\Filament\Resources\RiskDocuments\Pages;

use App\Models\RiskDocument;
use App\Models\User;
use App\Filament\Resources\RiskDocuments\RiskDocumentResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditRiskDocument extends EditRecord
{
    protected static string $resource = RiskDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('validar')
                ->label('Validar')
                ->icon('heroicon-m-check-badge')
                ->color('success')
                ->visible(fn () => auth()->user()?->hasRole('super_admin'))
                ->form([
                    Select::make('status')
                        ->label('Estado de Validación')
                        ->options([
                            'validada' => 'Validada',
                            'rechazada' => 'Rechazada',
                            'pendiente' => 'Pendiente',
                        ])
                        ->default(fn (RiskDocument $record) => $record->status)
                        ->required(),
                    Textarea::make('validation_comment')
                        ->label('Comentario de Validación')
                        ->default(fn (RiskDocument $record) => $record->validation_comment),
                ])
                ->action(function (RiskDocument $record, array $data): void {
                    $record->update([
                        'status' => $data['status'],
                        'validation_comment' => $data['validation_comment'],
                        'validated_by' => auth()->id(),
                        'validated_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Evidencia actualizada correctamente')
                        ->success()
                        ->send();
                }),
            ViewAction::make(),
            DeleteAction::make()
                ->visible(fn () => auth()->user()?->hasRole('super_admin')),
            ForceDeleteAction::make()
                ->visible(fn () => auth()->user()?->hasRole('super_admin')),
            RestoreAction::make()
                ->visible(fn () => auth()->user()?->hasRole('super_admin')),
        ];
    }
}
