<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use App\Models\Specialite;
use Filament\Widgets\ChartWidget;

class ClientsBySpecialiteChart extends ChartWidget
{
    protected static ?string $heading = 'Clients par Spécialité';
    protected static ?string $maxHeight = '300px';
    protected static string $color = 'info';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $specialites = Specialite::withCount('contacts')->get();

        return [
            'labels' => $specialites->pluck('nom')->toArray(),
            'datasets' => [
                [
                    'label' => 'Nombre de Clients',
                    'data' => $specialites->pluck('contacts_count')->toArray(),
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
        ];
    }
}
