<?php

namespace App\Filament\Resources\OpporutnityResource\Widgets;

use App\Enums\OpportunityStatut;
use App\Models\Opportunity;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class OpportunityWidget extends BaseWidget
{
    public ?string $startDate = null;
    public ?string $endDate = null;




    // Listen for date range updates
    protected $listeners = ['dateRangeUpdated' => 'updateDateRange'];

    public function mount(?string $startDate = null, ?string $endDate = null): void
    {
        $this->startDate = $startDate ?? now()->startOfMonth()->toDateString();
        $this->endDate = $endDate ?? now()->endOfMonth()->toDateString();
    }

    public function updateDateRange(array $data): void
    {
        $this->startDate = $data['startDate'] ?? $this->startDate;
        $this->endDate = $data['endDate'] ?? $this->endDate;

        // Force refresh the widget
        $this->dispatch('$refresh');
    }



    protected function getStats(): array
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        $stats = [
            Stat::make('Total des opportunités', Opportunity::whereBetween('created_at', [$startDate, $endDate])->count())
                ->description('Total des opportunités')
                ->color('success')
                ->descriptionIcon('heroicon-o-light-bulb')
        ];

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

        foreach ($revenueByCurrency as $currency => $amount) {
            $stats[] = Stat::make('Chiffre d\'Affaire Gagné (' . $currency . ')', number_format($amount, 2) . ' ' . $currency)
                ->description('Total des opportunités gagnées en ' . $currency)
                ->color('success');
        }


        return $stats;
    }
}
