<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;


enum FactureStatus: string implements HasLabel
{

    // les status d'une facture

    case PENDING = 'pending';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';

    public function getLabel(): string 
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::PAID => 'Payée',
            self::OVERDUE => 'En reatard',
            self::CANCELLED => 'Annulée',
        };
    }

    public function getBadge(): string 
    {
        return match ($this) {
            self::PENDING => '#F59E0B',
            self::PAID => '#22C55E',
            self::OVERDUE => '#EF4444',
            self::CANCELLED => '#9CA3AF',
        };
    }

    public function getFilamentBadge(): string 
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PAID => 'success',
            self::OVERDUE => 'danger',
            self::CANCELLED => 'secondary',
        };
    }


}

