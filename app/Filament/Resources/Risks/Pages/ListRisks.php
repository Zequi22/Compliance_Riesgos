<?php

namespace App\Filament\Resources\Risks\Pages;

use Filament\Actions\CreateAction;
use Torgodly\Html2Media\Actions\Html2MediaAction;
use Filament\Resources\Pages\ListRecords;
use App\Models\Risk;
use App\Filament\Resources\Risks\RiskResource;

use App\Filament\Exports\RiskExporter;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;

class ListRisks extends ListRecords
{
    protected static string $resource = RiskResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->label('Exportar Riesgos')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('primary')
                ->exporter(RiskExporter::class)
                ->formats([
                    ExportFormat::Csv,
                    ExportFormat::Xlsx,
                ]),

            CreateAction::make()
                ->label('Identificar Riesgo')
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->extraAttributes(['class' => 'btn-nuevo-riesgo']),
        ];
    }
}
