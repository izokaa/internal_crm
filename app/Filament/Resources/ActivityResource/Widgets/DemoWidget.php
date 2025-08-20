<?php

namespace App\Filament\Resources\ActivityResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Activity;
use Carbon\Carbon;

class DemoWidget extends BaseWidget
{
    public $startDate;
    public $endDate;

    protected $listeners = ['dateRangeUpdated', 'updateDateRange'];


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
            Stat::make('Nombre des démos', Activity::whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('label', function ($query) {
                    $query->where('value', 'like', '%demo%');
                })->count())
                ->description('Total des démos')
                ->descriptionIcon('carbon-demo')
                ->color('info'),
            Stat::make('Nombre des appels téléphonique', Activity::whereBetween('created_at', [$startDate, $endDate])->where('type', 'call')->count())
                ->description('Total des appels téléphonique')
                ->descriptionIcon('ionicon-call')
                ->color('seconadary'),

        ];
    }
}
