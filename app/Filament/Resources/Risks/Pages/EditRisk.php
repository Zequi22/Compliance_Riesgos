<?php

namespace App\Filament\Resources\Risks\Pages;

use App\Filament\Resources\Risks\RiskResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditRisk extends EditRecord
{
    protected static string $resource = RiskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function addActionItem(): void
    {
        $responsableId = $this->data['responsable_id'] ?? null;
        $uuid = (string) Str::uuid();

        // Completely replace actions to avoid phantom empty items from stale Livewire state
        $this->data['actions'] = [
            $uuid => [
                'title' => null,
                'responsable_id' => $responsableId,
                'due_date' => null,
                'status' => null,
                'notes' => null,
            ],
        ];

        $this->data['show_actions'] = true;
    }

    public function addIndicatorItem(): void
    {
        $uuid = (string) Str::uuid();

        // Completely replace indicators to avoid phantom empty items from stale Livewire state
        $this->data['indicators'] = [
            $uuid => [
                'name' => null,
                'target_value' => null,
                'tolerance_level' => null,
                'current_value' => null,
                'last_measured_at' => null,
            ],
        ];

        $this->data['show_indicators'] = true;
    }
}
