<?php

namespace App\Filament\Resources\RiskDocuments\Pages;

use App\Filament\Resources\RiskDocuments\RiskDocumentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use \Filament\Forms\Components\Select;
use \App\Models\RiskDocument;

class ViewRiskDocument extends ViewRecord
{
    protected static string $resource = RiskDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('validar')
                ->label('Validar')
                ->icon('heroicon-m-check-badge')
                ->color('success')
                ->visible(function () {
                    /** @var \App\Models\User $user */
                    $user = auth()->user();
                    return $user && $user->hasRole('super_admin');
                })
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
                    \Filament\Forms\Components\Textarea::make('validation_comment')
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
                    \Filament\Notifications\Notification::make()
                        ->title('Evidencia actualizada correctamente')
                        ->success()
                        ->send();
                }),
            EditAction::make(),
        ];
    }
}
