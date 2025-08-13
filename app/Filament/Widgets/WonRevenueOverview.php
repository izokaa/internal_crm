<?php

namespace App\Filament\Widgets;

use App\Models\Opportunity;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WonRevenueOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $wonOpportunities = Opportunity::where('status', 'Gagnée')->get();

        $revenueByCurrency = $wonOpportunities->groupBy('devise')->map(function ($opportunities) {
            return $opportunities->sum('montant_reel');
        });

        $stats = [];
        foreach ($revenueByCurrency as $currency => $amount) {
            $stats[] = Stat::make('Chiffre d\'Affaire Gagné (' . $currency . ')', number_format($amount, 2) . ' ' . $currency)
                ->description('Total des opportunités gagnées en ' . $currency)
                ->color('success');
        }

        return $stats;
    }
}