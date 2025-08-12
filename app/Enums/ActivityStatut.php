<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ActivityStatut: string implements HasLabel
{
    case OVERDUE = 'Overdue';
    case TODO = 'To Do';
    case UPCOMING = 'Upcoming';
    case COMPLETED = 'Completed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::OVERDUE => 'En retard',
            self::TODO => 'À faire',
            self::UPCOMING => 'À venir',
            self::COMPLETED => 'Terminé',
        };
    }


    public function getBadge(): ?string
    {
        return match ($this) {
            self::OVERDUE => '#EAB308',
            self::TODO => '#3B82F6',
            self::UPCOMING => '#3B02A6',
            self::COMPLETED => '#22C55E',
        };
    }


}
