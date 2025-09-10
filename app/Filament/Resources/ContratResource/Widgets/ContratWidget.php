<?php

namespace App\Filament\Resources\ContratResource\Widgets;

use App\Models\Contrat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use App\Enums\ContratStatus;

class ContratWidget extends BaseWidget
{
    use HasWidgetShield;

    public ?string $startDate = null;
    public ?string $endDate = null;
    protected ?string $heading = "Stats des Contrats";

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
                ->descriptionIcon('heroicon-o-document-text')
                ->color('info'),

            Stat::make('Total des contrats actifs', Contrat::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', ContratStatus::ACTIVE->value)->count())
                ->description('Total des contrats actifs')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Total des contrats expirés', Contrat::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', ContratStatus::EXPIRED->value)->count())
                ->description('Total des contrats expirés')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger'),
            Stat::make('Total des contrats renouvelé', Contrat::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', ContratStatus::RENEWED->value)->count())
                ->description('Total des contrats renouvelés')
                ->descriptionIcon('clarity-recycle-line')
                ->color('success'),
        ];
    }
}
