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
}
