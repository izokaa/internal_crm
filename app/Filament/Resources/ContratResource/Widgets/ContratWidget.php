<?php

namespace App\Filament\Resources\ContratResource\Widgets;

use App\Models\Contrat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class ContratWidget extends BaseWidget
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
            Stat::make('Total des contrats', Contrat::whereBetween('created_at', [$startDate, $endDate])->count())
                ->description('Total des contrats')
                ->descriptionIcon('clarity-contract-line')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('sucess'),

            Stat::make('Total des contrats actifs', Contrat::whereBetween('created_at', [$startDate, $endDate])->where('date_fin', '>=', now()->toDateString())->count())
                ->description('Total des contrats actifs')
                ->descriptionIcon('clarity-contract-line')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('sucess'),

            Stat::make('Total des contrats epxirés', Contrat::whereBetween('created_at', [$startDate, $endDate])->where('date_fin', '<', now()->toDateString())->count())
                ->description('Total des contrats expirés')
                ->descriptionIcon('clarity-contract-line')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('danger')
        ];
    }
}
