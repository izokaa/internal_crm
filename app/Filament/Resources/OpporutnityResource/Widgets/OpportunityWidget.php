<?php

namespace App\Filament\Resources\OpporutnityResource\Widgets;

use App\Models\Opportunity;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OpportunityWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total des opportunités', Opportunity::count())
                ->description('Total des opportunités')
                ->color('success')
                ->descriptionIcon('heroicon-o-light-bulb')
        ];
    }
}
