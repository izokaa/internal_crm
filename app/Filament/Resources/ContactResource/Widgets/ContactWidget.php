<?php

namespace App\Filament\Resources\ContactResource\Widgets;

use App\Models\Contact;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContactWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total de contacts', Contact::count())
                ->description('total des contacts')
                ->descriptionIcon('lucide-contact')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),


            Stat::make('Total des clients ', Contact::where('type', 'client')->count())
                ->description('total des clients')
                ->descriptionIcon('lucide-contact')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('Total des prospects ', Contact::where('type', 'prospect')->count())
                ->description('total des prospects')
                ->descriptionIcon('lucide-contact')
                ->chart([7, 2, 10, 3, 15, 9, 20])
                ->color('info'),

        ];
    }
}
