<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OpportunityStatut: string implements HasLabel
{
    case OPEN = 'Open';
    case WON = 'Won';
    case LOST = 'Lost';
    case LATE = 'Late';       // En retard
    case CANCELED = 'Canceled'; // Annulée
    case CLOSED = 'Closed';   // Fermée

    public function getLabel(): ?string
    {
        return match ($this) {
            self::OPEN => 'Ouverte',
            self::WON => 'Gagnée',
            self::LOST => 'Perdue',
            self::LATE => 'En retard',
            self::CANCELED => 'Annulée',
            self::CLOSED => 'Fermée',
        };
    }

    public function getBadge(): ?string
    {
        return match ($this) {
            self::OPEN => '#DBEAFE',       // bleu clair
            self::WON => '#D1FAE5',        // vert clair
            self::LOST => '#FEE2E2',       // rouge clair
            self::LATE => '#FEF3C7',       // jaune clair
            self::CANCELED => '#E5E7EB',   // gris clair
            self::CLOSED => '#EDE9FE',     // violet clair
        };
    }

    public function getTailwindBadge(): ?string
    {
        return match ($this) {
            self::OPEN => 'info',
            self::WON => 'success',
            self::LOST => 'danger',
            self::LATE => 'warning',
            self::CANCELED => 'gray',
            self::CLOSED => 'primary',
        };
    }

    public function getTextStatusColor(): ?string
    {
        return match ($this) {
            self::OPEN => '#1E40AF',       // bleu
            self::WON => '#065F46',        // vert
            self::LOST => '#991B1B',       // rouge
            self::LATE => '#92400E',       // brun/jaune
            self::CANCELED => '#4B5563',   // gris
            self::CLOSED => '#5B21B6',     // violet
        };
    }
}
