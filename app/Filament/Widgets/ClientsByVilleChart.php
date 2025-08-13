<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use App\Models\Ville;
use Filament\Widgets\ChartWidget;

class ClientsByVilleChart extends ChartWidget
{
    protected static ?string $heading = 'Clients par Ville';
    protected static ?string $maxHeight = '300px';
    protected static string $color = 'success';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $villes = Ville::withCount('contacts')->get();

        return [
            'labels' => $villes->pluck('nom')->toArray(),
            'datasets' => [
                [
                    'label' => 'Nombre de Clients',
                    'data' => $villes->pluck('contacts_count')->toArray(),
                    'backgroundColor' => '#06B6D4',
                    'borderColor' => '#67E8F9',
                ],
            ],
        ];
    }
}
