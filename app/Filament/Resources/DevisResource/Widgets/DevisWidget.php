<?php

namespace App\Filament\Resources\DevisResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use App\Models\Devis;
use App\Enums\DevisStatus;

class DevisWidget extends BaseWidget
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
            Stat::make('Nombre des devis acceptés', Devis::whereBetween('created_at', [$startDate, $endDate])->where('status', DevisStatus::ACCEPTED->value)->count())
                ->description('Total des devis acceptés')
                ->descriptionIcon('heroicon-o-document-currency-dollar')
                ->color('success'),
            Stat::make('Total des devis envoyés', Devis::whereBetween('created_at', [$startDate, $endDate])->where('status', DevisStatus::SENT->value)->count())
                ->description('Total des devis envoyés')
                ->descriptionIcon('heroicon-o-document-currency-dollar')
                ->color('seconadary'),
            Stat::make('Total des devis refusés', Devis::whereBetween('created_at', [$startDate, $endDate])->where('status', DevisStatus::REJECTED->value)->count())
                ->description('Total des devis refusés')
                ->descriptionIcon('heroicon-o-document-currency-dollar')
                ->color('danger'),
            Stat::make('Total des devis en cours', Devis::whereBetween('created_at', [$startDate, $endDate])->where('status', DevisStatus::DRAFT->value)->count())
                ->description('Total des devis en cours')
                ->descriptionIcon('heroicon-o-document-currency-dollar')
                ->color('warning'),
        ];
    }

}
