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
        return [
            'matrix' => $this->getMatrixData(),
            'slectRisk' => $this->getRiskByScore(),
        ];
    }
    public function getMatrixData()
    {
        return DB::table('assessments')
            //contamos los registros que hay en cada combinacion
            ->select('probability', 'impact', DB::raw('count(*) as total'))
            //filtramos por el tipo
            ->where('type', 'inherent')
            //cruzamos los datos y lo metemos en un array
            ->groupBy('probability', 'impact')
            ->get()
            //agrupamos por la probabilidad
            ->groupBy('probability')
            //orgnizamos el resultado
            ->map(fn($item) => $item->keyBy('impact'));
    }

    public function getRiskByScore()
    {
        if (! $this->score) return collect();

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
