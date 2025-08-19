<?php

namespace App\Filament\Resources\ActivityResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Activity;

class DemoWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Nombre des démos', Activity::where('label_id', 13)->count()),
        ];
    }
}
