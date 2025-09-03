<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;


enum ContratStatus: string implements HasLabel
{
        // les status du contrats : actif, expiré, renouvelé => tous ça en anglais 
    case ACTIVE = "Active";
    case EXPIRED = "Expired";
    case RENEWED = "Renewed";
    case CANCELED = "Canceled";


    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Actif',
            self::EXPIRED => 'Expiré',
            self::RENEWED => 'Renouvelé',
            self::CANCELED => 'Annulé',
        };
    }

    public function getBadge(): ?string
    {
        return match ($this) {
            self::ACTIVE => '#22C55E',
            self::EXPIRED => '#D97706',
            self::RENEWED => '#3B82F6',
            self::CANCELED => '#EF4444',
        };
    }

    public function getFilamentBadge(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::EXPIRED => 'warning',
            self::RENEWED => 'primary',
            self::CANCELED => 'danger',
        };
    }
}
