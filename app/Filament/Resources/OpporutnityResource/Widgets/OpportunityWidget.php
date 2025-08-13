<?php

namespace App\Filament\Resources\OpporutnityResource\Widgets;

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

        return [
            Stat::make('Total des opportunités', Opportunity::whereBetween('created_at', [$startDate, $endDate])->count())
                ->description('Total des opportunités')
                ->color('success')
                ->descriptionIcon('heroicon-o-light-bulb')
        ];
    }
}
