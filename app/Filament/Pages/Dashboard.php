<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ContactResource\Widgets\ContactWidget;
use App\Filament\Resources\ContratResource\Widgets\ContratWidget;
use App\Filament\Resources\OpporutnityResource\Widgets\OpportunityWidget;
use App\Traits\HasActiveIcon;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class Dashboard extends Page implements HasForms
{
    use HasActiveIcon;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'clarity-dashboard-line';
    protected static ?string $navigationActiveIcon = 'clarity-dashboard-solid';
    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $title = 'Tableau de board';

    public ?string $startDate = null;
    public ?string $endDate = null;

    public function mount(): void
    {
        $this->form->fill([
            'startDate' => now()->startOfMonth()->toDateString(),
            'endDate' => now()->endOfMonth()->toDateString(),
        ]);

        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();
    }

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('startDate')
                ->label('Date de début')
                ->default(now()->startOfMonth())
                ->live()
                ->afterStateUpdated(function ($state) {
                    $this->startDate = $state;
                    $this->dispatch('dateRangeUpdated', [
                        'startDate' => $this->startDate,
                        'endDate' => $this->endDate
                    ]);
                }),
            DatePicker::make('endDate')
                ->label('Date de fin')
                ->default(now()->endOfMonth())
                ->live()
                ->afterStateUpdated(function ($state) {
                    $this->endDate = $state;
                    $this->dispatch('dateRangeUpdated', [
                        'startDate' => $this->startDate,
                        'endDate' => $this->endDate
                    ]);
                }),
        ];
    }

    public function updateWidgets(): void
    {
        // Force refresh all widgets
        $this->dispatch('$refresh');
    }

    protected function getWidgets(): array
    {
        return [
            ContactWidget::class,
            ContratWidget::class,
            OpportunityWidget::class,
            \App\Filament\Widgets\ClientsBySpecialiteChart::class,
            \App\Filament\Widgets\ClientsByVilleChart::class,
            \App\Filament\Widgets\WonRevenueOverview::class,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    // Method to get current date range for widgets
    public function getDateRange(): array
    {
        return [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];
    }
}
