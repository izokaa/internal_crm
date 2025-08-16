<?php

namespace App\Filament\Resources\ContactResource\Widgets;

use App\Models\Contact;
// Removed: use App\Models\Fournisseur;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class ContactWidget extends BaseWidget
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
        $endDate   = Carbon::parse($this->endDate);

        return [
            Stat::make('Total de contacts', Contact::whereBetween('created_at', [$startDate, $endDate])->count())
                ->description('Total des contacts')
                ->descriptionIcon('heroicon-o-users')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary'),

            Stat::make('Total des clients', Contact::whereBetween('created_at', [$startDate, $endDate])
                ->where('type', 'client')->count())
                ->description('Total des clients')
                ->descriptionIcon('heroicon-o-check')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('Total des prospects', Contact::whereBetween('created_at', [$startDate, $endDate])
                ->where('type', 'prospect')->count())
                ->description('Total des prospects')
                ->descriptionIcon('heroicon-o-users')
                ->chart([7, 2, 10, 3, 15, 9, 20])
                ->color('info'),

            Stat::make('Total des fournisseurs', Contact::whereBetween('created_at', [$startDate, $endDate])
                ->where('type', 'fournisseur')->count())
                ->description('Total des fournisseurs')
                ->descriptionIcon('heroicon-o-truck')
                ->chart([3, 4, 5, 2, 9, 1, 7])
                ->color('secondary'),
        ];
    }
}
