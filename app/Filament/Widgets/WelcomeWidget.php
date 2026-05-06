<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WelcomeWidget extends Widget
{
    protected static ?int $sort = -1;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.welcome-widget';

    public function getUser()
    {
        return Auth::user();
    }

    public function getGreeting(): string
    {
        $hour = now()->hour;

        if ($hour < 12) {
            return '¡Buenos días!';
        }

        if ($hour < 20) {
            return '¡Buenas tardes!';
        }

        return '¡Buenas noches!';
    }
}
