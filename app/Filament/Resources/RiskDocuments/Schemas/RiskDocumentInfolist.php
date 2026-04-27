<?php

namespace App\Filament\Resources\RiskDocuments\Schemas;

use App\Models\RiskDocument;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use App\Models\User;
use Illuminate\Support\HtmlString;

class RiskDocumentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Principal')
                    ->description('Detalles detallados y descripción del riesgo o acción.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Título')
                            ->size('lg')
                            ->weight('bold')
                            ->columnSpanFull(),
                        TextEntry::make('description')
                            ->placeholder('Sin descripción disponible.')
                            ->label('Descripción')
                            ->prose()
                            ->columnSpanFull(),
                        Grid::make(3)->schema([
                            TextEntry::make('document_type')
                                ->label('Tipo de Documento')
                                ->formatStateUsing(fn($state) => ucfirst($state))
                                ->weight('bold')
                                ->icon('heroicon-o-document-text')
                                ->placeholder('-'),
                            TextEntry::make('document_date')
                                ->date()
                                ->label('Fecha del Documento')
                                ->weight('bold')
                                ->icon('heroicon-o-calendar')
                                ->placeholder('-'),
                            TextEntry::make('status')
                                ->label('Estado de Validación')
                                ->badge()
                                ->formatStateUsing(fn(string $state): string => match ($state) {
                                    'validada' => 'Validada',
                                    'rechazada' => 'Rechazada',
                                    'pendiente' => 'Pendiente',
                                    default => ucfirst($state),
                                })
                                ->color(fn(string $state): string => match ($state) {
                                    'validada' => 'success',
                                    'rechazada' => 'danger',
                                    'pendiente' => 'warning',
                                    default => 'gray',
                                }),
                        ]),
                    ]),

                Section::make('Archivos y Clasificación')
                    ->description('Documentación adjunta y categorización del registro.')
                    ->icon('heroicon-o-paper-clip')
                    ->schema([
                        TextEntry::make('file_path')
                            ->label('Archivo principal')
                            ->formatStateUsing(fn($state) => basename($state))
                            ->url(fn($state) => $state ? asset('storage/' . $state) : null, true)
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color('primary')
                            ->weight('bold')
                            ->placeholder('Sin archivo principal'),

                        TextEntry::make('classification')
                            ->label('Clasificación')
                            ->badge()
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'risk_evidence'    => 'Evidencia de Riesgo',
                                'control_evidence' => 'Evidencia de Control',
                                'action_evidence'  => 'Evidencia de Acción',
                                default            => ucfirst(str_replace('_', ' ', $state)),
                            })
                            ->color(fn(string $state): string => match ($state) {
                                'risk_evidence'    => 'info',
                                'control_evidence' => 'success',
                                'action_evidence'  => 'warning',
                                default            => 'gray',
                            }),

                        TextEntry::make('file_paths')
                            ->label('Archivos adicionales')
                            ->columnSpanFull()
                            ->formatStateUsing(function ($state) {
                                if (empty($state)) return '-';
                                $paths = is_array($state) ? $state : json_decode($state, true) ?? [];
                                if (empty($paths)) return '-';
                                $html = '<ul class="flex flex-col gap-2 mt-1">';
                                foreach ($paths as $path) {
                                    $name = basename($path);
                                    $url  = asset('storage/' . $path);
                                    $html .= "<li><a href=\"{$url}\" target=\"_blank\" class=\"text-primary-600 hover:text-primary-500 underline text-sm flex items-center gap-1\"><svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1\"/></svg> {$name}</a></li>";
                                }
                                $html .= '</ul>';
                                return new HtmlString($html);
                            })
                            ->placeholder('Sin archivos adicionales'),
                    ])->columns(2),

                Section::make('Validación y Relaciones')
                    ->description('Información sobre la validación y entidades vinculadas.')
                    ->icon('heroicon-o-check-circle')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('validated_by')
                                ->label('Validado por')
                                ->weight('medium')
                                ->formatStateUsing(fn($state) => User::find($state)?->name)
                                ->icon('heroicon-o-user')
                                ->placeholder('-'),
                            TextEntry::make('validated_at')
                                ->dateTime()
                                ->label('Fecha de Validación')
                                ->placeholder('-'),
                        ]),
                        TextEntry::make('validation_comment')
                            ->label('Comentario de la Validación')
                            ->visible(fn(RiskDocument $record): bool => $record->status == 'validada')
                            ->placeholder('-')
                            ->columnSpanFull()
                            ->prose(),
                        
                        Grid::make(3)->schema([
                            TextEntry::make('risk.name')
                                ->label('Riesgo Relacionado')
                                ->weight('medium')
                                ->icon('heroicon-o-exclamation-circle')
                                ->visible(fn(RiskDocument $record): bool => $record->risk_id != null)
                                ->placeholder('-'),
                            TextEntry::make('control.title')
                                ->label('Control Aplicado')
                                ->weight('medium')
                                ->icon('heroicon-o-check-circle')
                                ->visible(fn(RiskDocument $record): bool => $record->control_id != null)
                                ->placeholder('-'),
                            TextEntry::make('action.title')
                                ->label('Acción Relacionada')
                                ->weight('medium')
                                ->icon('heroicon-o-cog-8-tooth')
                                ->visible(fn(RiskDocument $record): bool => $record->action_id != null)
                                ->placeholder('-'),
                        ]),
                    ]),

                Section::make('Historial del Sistema')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Grid::make(4)->schema([
                            TextEntry::make('uploaded_by')
                                ->label('Subido por')
                                ->weight('medium')
                                ->formatStateUsing(fn($state) => User::find($state)?->name)
                                ->placeholder('-'),
                            TextEntry::make('uploaded_at')
                                ->label('Fecha de Subida')
                                ->dateTime()
                                ->placeholder('-'),
                            TextEntry::make('created_at')
                                ->label('Fecha de Creación')
                                ->dateTime()
                                ->placeholder('-'),
                            TextEntry::make('updated_at')
                                ->label('Última Actualización')
                                ->dateTime()
                                ->placeholder('-'),
                            TextEntry::make('deleted_at')
                                ->label('Fecha de Eliminación')
                                ->dateTime()
                                ->visible(fn(RiskDocument $record): bool => $record->trashed()),
                        ]),
                    ]),
            ]);
    }
}
