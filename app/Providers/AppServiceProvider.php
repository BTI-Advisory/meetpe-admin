<?php

namespace App\Providers;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        FilamentColor::register([
            'orange' => Color::hex('#f97316'),
            'violet' => Color::hex('#8b5cf6'),
            'slate'  => Color::hex('#64748b'),
            'dark'   => Color::hex('#374151'),
        ]);
    }
}
