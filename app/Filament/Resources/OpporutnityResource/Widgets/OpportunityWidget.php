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
                ->descriptionIcon('heroicon-o-light-bulb')
                ->color('primary'),
        ];

        // Stat for won opportunities revenue by currency (existing)
        $query = Opportunity::where('status', OpportunityStatut::WON->value);
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
            $stats[] = Stat::make("Chiffre d'Affaire Gagné ({$currency})", number_format($amount, 2) . ' ' . $currency)
                ->description("Total des opportunités gagnées en {$currency}")
                ->color('success');
        }

        // New stat: Total revenue gained (aggregated over all won opportunities)
        $totalRevenue = Opportunity::where('status', OpportunityStatut::WON->value)
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->sum('montant_reel');
        $stats[] = Stat::make('Total Chiffre d’Affaire Gagné', number_format($totalRevenue, 2) . ' €')
            ->description('Revenu total des opportunités gagnées')
            ->descriptionIcon('heroicon-o-currency-dollar')
            ->color('success');

        // New stat: Opportunities lost
        $lostQuery = Opportunity::where('status', OpportunityStatut::LOST->value);
        if ($this->startDate) {
            $lostQuery->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $lostQuery->whereDate('created_at', '<=', $this->endDate);
        }
        $stats[] = Stat::make('Opportunités perdues', $lostQuery->count())
                ->description('Total des opportunités perdues')
                ->color('danger');

        return $stats;
    }
}
