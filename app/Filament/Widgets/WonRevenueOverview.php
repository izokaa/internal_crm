<?php

namespace App\Filament\Widgets;

use App\Models\Opportunity;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Enums\OpportunityStatut; // Add this import

class WonRevenueOverview extends BaseWidget
{
    public ?string $startDate = null;
    public ?string $endDate = null;

    protected function getStats(): array
    {
        $query = Opportunity::where('status', OpportunityStatut::WON->value); // Use the enum value

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        $wonOpportunities = $query->get();

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