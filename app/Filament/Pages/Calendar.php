<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ActivityResource\Widgets\CalendarWidget;
use Filament\Pages\Page;

class Calendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static string $view = 'filament.pages.calendar';
    protected static ?string $navigationLabel = 'Calendrier';
    protected static ?int $navigationSort = 3;

    protected function getHeaderWidgets(): array
    {
        return [
            CalendarWidget::class,
        ];
    }

}
