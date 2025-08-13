<?php

namespace App\Filament\Resources\ContactResource\Widgets;

use App\Models\Contact;
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
        $endDate = Carbon::parse($this->endDate);

        return [
            Stat::make('Total de contacts', Contact::whereBetween('created_at', [$startDate, $endDate])->count())
                ->description('total des contacts')
                ->descriptionIcon('lucide-contact')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),


            Stat::make('Total des clients ', Contact::whereBetween('created_at', [$startDate, $endDate])->where('type', 'client')->count())
                ->description('total des clients')
                ->descriptionIcon('lucide-contact')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('Total des prospects ', Contact::where('type', 'prospect')->count())
                ->description('total des prospects')
                ->descriptionIcon('lucide-contact')
                ->chart([7, 2, 10, 3, 15, 9, 20])
                ->color('info'),

        ];
    }
}
