<?php

namespace App\Filament\Resources\ExpenseResource\Widgets;

use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class ExpenseWidget extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total des expenses', Expense::count())
                ->color('primary')
                ->description('Total epxneses'),
        ];
    }
}
