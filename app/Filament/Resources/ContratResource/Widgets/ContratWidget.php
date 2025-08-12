<?php

namespace App\Filament\Resources\ContratResource\Widgets;

use App\Models\Contrat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContratWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total des contrats', Contrat::count())
                ->description('Total des contrats')
                ->descriptionIcon('clarity-contract-line')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('sucess'),

            Stat::make('Total des contrats actifs', Contrat::where('date_fin', '>=', now()->toDateString())->count())
                ->description('Total des contrats actifs')
                ->descriptionIcon('clarity-contract-line')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('sucess'),

            Stat::make('Total des contrats epxirés', Contrat::where('date_fin', '<', now()->toDateString())->count())
                ->description('Total des contrats expirés')
                ->descriptionIcon('clarity-contract-line')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('danger')
        ];
    }
}
