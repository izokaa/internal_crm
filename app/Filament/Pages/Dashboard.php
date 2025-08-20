<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ActivityResource\Widgets\DemoWidget;
use App\Filament\Resources\ContactResource\Widgets\ContactWidget;
use App\Filament\Resources\ContratResource\Widgets\ContratWidget;
use App\Filament\Resources\DevisResource\Widgets\DevisWidget;
use App\Filament\Resources\OpportunityResource\Widgets\OpportunityWidget;
use App\Traits\HasActiveIcon;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Grid;

class Dashboard extends Page implements HasForms
{
    use HasActiveIcon;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'clarity-dashboard-line';
    protected static ?string $navigationActiveIcon = 'clarity-dashboard-solid';
    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $title = 'Tableau de board';

    public function getTitle(): string
    {
        return " ";
    }

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
            Grid::make()
            ->schema([
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
            })->columns(2),
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
          ])
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
            DemoWidget::class,
            DevisWidget::class,
            ContratWidget::class
        ];
    }

    protected function getFinanceWidgets(): array
    {
        return [
            OpportunityWidget::class
        ];
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
