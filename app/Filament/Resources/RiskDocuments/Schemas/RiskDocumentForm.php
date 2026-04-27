<?php

namespace App\Filament\Resources\RiskDocuments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use \App\Models\Action;
use \App\Models\Control;
use \App\Models\Risk;

class RiskDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Detalles de la Evidencia')
                    ->schema([
                        Select::make('associated_type')
                            ->label('Asociado a')
                            ->options([
                                'risk'    => 'Riesgo (General)',
                                'control' => 'Control',
                                'action'  => 'Acción',
                            ])
                            ->live()
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record?->control_id) $component->state('control');
                                elseif ($record?->action_id) $component->state('action');
                                elseif (request()->query('classification') === 'action_evidence' || request()->filled('action_id')) $component->state('action');
                                elseif (request()->query('classification') === 'control_evidence' || request()->filled('control_id')) $component->state('control');
                                else $component->state('risk');
                            })
                            ->afterStateUpdated(function (callable $set) {
                                $set('risk_id', null);
                                $set('control_id', null);
                                $set('action_id', null);
                            })
                            ->required(),

                        Select::make('risk_id')
                            ->label('Selecciona el Riesgo')
                            ->relationship('risk', 'name')
                            ->searchable()
                            ->live()
                            ->options(function () {
                                return Risk::all()->pluck('name', 'id');
                            })
                            ->default(function () {
                                // Prioridad: parámetro por URL
                                $riskId = request()->query('risk_id');

                                // Fallback: si tenemos action_id pero no risk_id o es cadena vacía, buscarlo en la acción
                                if (empty($riskId) && $actionId = request()->query('action_id')) {
                                    $riskId = Action::where('id', $actionId)->value('risk_id');
                                }

                                // Fallback: si tenemos control_id pero no risk_id, buscarlo en el control
                                if (empty($riskId) && $controlId = request()->query('control_id')) {
                                    $riskId = Control::where('id', $controlId)->value('risk_id');
                                }

                                return $riskId;
                            })
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$record && empty($state)) {
                                    $riskId = request()->query('risk_id');
                                    if (empty($riskId) && $actionId = request()->query('action_id')) {
                                        $riskId = Action::where('id', $actionId)->value('risk_id');
                                    }
                                    if (empty($riskId) && $controlId = request()->query('control_id')) {
                                        $riskId = Control::where('id', $controlId)->value('risk_id');
                                    }
                                    if ($riskId) $component->state($riskId);
                                }
                            })
                            ->visible(fn(Get $get) => in_array($get('associated_type'), ['risk', 'control', 'action']))
                            ->required(fn(Get $get) => in_array($get('associated_type'), ['risk', 'control', 'action'])),

                        Select::make('control_id')
                            ->label('Selecciona el Control')
                            ->options(function (Get $get) {
                                $riskId = $get('risk_id');
                                if (!$riskId) return [];
                                return Control::where('risk_id', $riskId)->pluck('title', 'id');
                            })
                            ->visible(fn(Get $get) => $get('associated_type') === 'control')
                            ->required(fn(Get $get) => $get('associated_type') === 'control')
                            ->default(fn() => request()->query('control_id')),

                        Select::make('action_id')
                            ->label('Selecciona la Acción')
                            ->options(function (Get $get) {
                                $riskId = $get('risk_id');
                                if (!$riskId) return [];
                                return Action::where('risk_id', $riskId)->pluck('title', 'id');
                            })
                            ->visible(fn(Get $get) => $get('associated_type') === 'action')
                            ->required(fn(Get $get) => $get('associated_type') === 'action')
                            ->default(fn() => request()->query('action_id'))
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$record && empty($state) && $actionId = request()->query('action_id')) {
                                    $component->state($actionId);
                                }
                            }),

                        TextInput::make('title')->label('Título')->required(),

                        Select::make('document_type')->label('Tipo de documento')->required()
                            ->options([
                                'política'      => 'Política',
                                'procedimiento' => 'Procedimiento',
                                'registro'      => 'Registro',
                                'captura'       => 'Captura',
                                'informe'       => 'Informe',
                                'otro'          => 'Otro',
                            ]),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        DatePicker::make('document_date')
                            ->label('Fecha del documento')
                            ->required(),

                        // ── Archivo principal ──────────────────────────────────
                        FileUpload::make('file_path')
                            ->label('Archivo principal')
                            ->required()
                            ->directory('risk-documents')
                            ->downloadable()
                            ->openable(),

                        // ── Archivos adicionales ───────────────────────────────
                        FileUpload::make('file_paths')
                            ->label('Archivos adicionales')
                            ->helperText('Puedes adjuntar uno o varios archivos de soporte.')
                            ->multiple()
                            ->directory('risk-documents')
                            ->downloadable()
                            ->openable()
                            ->columnSpan(2),

                        Hidden::make('uploaded_by')->default(fn() => auth()->id()),
                    ])->columns(2),
            ]);
    }
}
