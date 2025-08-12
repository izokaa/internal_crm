<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ContactResource\Widgets\ContactWidget;
use App\Filament\Resources\ContratResource\Widgets\ContratWidget;
use App\Filament\Resources\OpporutnityResource\Widgets\OpportunityWidget;
use App\Traits\HasActiveIcon;
use Filament\Pages\Page;

class Dashboard extends Page
{
    use HasActiveIcon;

    protected static ?string $navigationIcon = 'clarity-dashboard-line';
    protected static ?string $navigationActiveIcon = 'clarity-dashboard-solid';

    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $title = 'Tableau de board';


    protected function getHeaderWidgets(): array
    {
        return [
            ContactWidget::class,
            ContratWidget::class,
            OpportunityWidget::class
        ];
    }
}
