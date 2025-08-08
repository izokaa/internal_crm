<?php

namespace App\Filament\Pages;

use App\Traits\HasActiveIcon;
use Filament\Pages\Page;

class Dashboard extends Page
{
    use HasActiveIcon;

    protected static ?string $navigationIcon = 'clarity-dashboard-line';
    protected static ?string $navigationActiveIcon = 'clarity-dashboard-solid';

    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $title = 'Tableau de board';
}
