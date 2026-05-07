<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use App\Models\Assessment;
use Livewire\Attributes\Url; //PARA LAS URL?
use BackedEnum;

class Heatmap extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'HeatMap';
    protected static ?string $title = 'HeatMap';
    protected static ?int $navigationSort = 3;
    protected string $view = 'filament.pages.heatmap';

    public ?int $score = null;

    protected function getViewData(): array
    {
        // Solo pasamos $matrix; getRiskByScore() se llama directamente en la vista (reactivo a $this->score)
        return [
            'matrix' => $this->getMatrixData(),
        ];
    }

    public function getMatrixData()
    {
        // Agrupa evaluaciones inherentes por probabilidad e impacto para construir la matriz 5×5
        return DB::table('assessments')
            ->select('probability', 'impact', DB::raw('count(*) as total'))
            ->where('type', 'inherent')
            ->groupBy('probability', 'impact')
            ->get()
            ->groupBy('probability')               // filas = niveles de probabilidad
            ->map(fn ($row) => $row->keyBy('impact')); // columnas = niveles de impacto
    }

    public function getRiskByScore()
    {
        // Devuelve vacío si no hay score seleccionado (clic en celda del heatmap)
        if (! $this->score) {
            return collect();
        }

        return Assessment::query()
            ->with('risk')
            ->where('score', $this->score)
            ->where('type', 'inherent')
            ->get();
    }
    public function resetScore()
    {
        $this->reset('score');
    }
}
