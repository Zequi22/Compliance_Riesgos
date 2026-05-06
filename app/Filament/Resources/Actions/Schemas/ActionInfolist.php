<?php

namespace App\Filament\Resources\Actions\Schemas;

use App\Models\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ActionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->description('Detalle descriptivo y contexto del riesgo asociado.')
                    ->icon('heroicon-m-information-circle')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('title')
                            ->label('Acción')
                            ->weight('bold')
                            ->size('lg')
                            ->columnSpanFull(),

                        Group::make([
                            TextEntry::make('risk.name')
                                ->label('Riesgo Relacionado')
                                ->url(fn(Action $record) => $record->risk ? route('filament.admin.resources.risks.edit', ['record' => $record->risk_id]) : null)
                                ->color('primary')
                                ->icon('heroicon-m-link'),

                            TextEntry::make('status')
                                ->label('Estado Actual')
                                ->badge()
                                ->formatStateUsing(fn(string $state): string => Action::getStatusOptions()[$state] ?? ucfirst($state))
                                ->color(fn(string $state): string => Action::getStatusColor($state)),

                            TextEntry::make('priority')
                                ->label('Prioridad')
                                ->badge()
                                ->formatStateUsing(fn(string $state): string => Action::getPriorityOptions()[$state] ?? ucfirst($state))
                                ->color(fn(string $state): string => Action::getPriorityColor($state)),

                            TextEntry::make('progress')
                                ->label('Progreso')
                                ->formatStateUsing(fn ($state) => ($state ?? 0) . '%')
                                ->color('primary'),
                        ])->columns(4)->columnSpanFull(),
                    ]),

                Section::make('Tiempos y Planificación')
                    ->description('Seguimiento de plazos de ejecución y compromiso.')
                    ->icon('heroicon-m-clock')
                    ->columnSpanFull()
                    ->columns(4)
                    ->schema([
                        TextEntry::make('due_date')
                            ->label('Fecha Límite')
                            ->date('d/m/Y')
                            ->icon('heroicon-m-calendar')
                            ->color(fn(Action $record) => $record->isOverdue() ? 'danger' : null),
                        TextEntry::make('start_date')
                            ->label('Fecha Inicio')
                            ->placeholder('No definida')
                            ->date('d/m/Y')
                            ->icon('heroicon-m-play-circle'),
                        TextEntry::make('commitment_date')
                            ->label('Fecha Compromiso')
                            ->placeholder('No definida')
                            ->date('d/m/Y')
                            ->icon('heroicon-m-calendar-days')
                            ->weight('bold')
                            ->color(fn(Action $record) => $record->isOverdue() ? 'danger' : 'primary'),
                        TextEntry::make('actual_closure_date')
                            ->label('Cierre Real')
                            ->placeholder('Pendiente')
                            ->date('d/m/Y')
                            ->icon('heroicon-m-check-badge')
                            ->color('success'),
                    ]),

                Section::make('Responsables y Notas')
                    ->icon('heroicon-m-user-group')
                    ->columnSpanFull()
                    ->schema([
                        Group::make([
                            TextEntry::make('responsable.full_name')
                                ->label('Persona a Cargo')
                                ->placeholder('-')
                                ->icon('heroicon-m-user-circle')
                                ->color('gray'),
                            TextEntry::make('organizationalUnit.name')
                                ->label('Unidad de Gestión')
                                ->icon('heroicon-m-building-office')
                                ->placeholder('-')
                                ->color('gray'),
                        ])->columns(2)->columnSpanFull(),

                        TextEntry::make('notes')
                            ->label('Comentarios Adicionales')
                            ->placeholder('No se han registrado notas adicionales.')
                            ->size('sm')
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'p-3 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 italic']),
                    ]),
            ]);
    }
}
