<?php

namespace App\Filament\Resources\RiskDocuments\Pages;

use App\Filament\Resources\RiskDocuments\RiskDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRiskDocuments extends ListRecords
{
    protected static string $resource = RiskDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->icon('heroicon-m-plus')
                ->color('primary'),
        ];
    }
}
