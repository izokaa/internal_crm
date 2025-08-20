<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ExpenseResource\Widgets\ExpenseWidget;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;


    public static function getWidgets(): array
    {
        return [
            ExpenseWidget::class
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ExpenseWidget::class
        ];
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
