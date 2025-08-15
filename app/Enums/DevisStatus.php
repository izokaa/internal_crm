<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DevisStatus: string implements HasLabel
{
    case DRAFT = "Draft";
    case SENT = "Sent";
    case ACCEPTED = "Accepted";
    case REJECTED = "Rejected";
    case NEGOTIATION = 'Negotiation';
    case EXPIRED = "Expired";


    public function getLabel(): string
    {
        return match($this) {
            self::DRAFT => 'Brouillon',
            self::SENT => 'Enovyé',
            self::ACCEPTED => 'Accepté',
            self::REJECTED => 'Rejeté',
            self::NEGOTIATION => 'Négociation',
            self::EXPIRED => 'Expiré',

        };
    }

    public function getBadge(): ?string
    {
        return match($this) {
            self::DRAFT => '#6B7280',
            self::SENT => '#3B82F6',
            self::ACCEPTED => '#22C55E',
            self::REJECTED => '#EF4444',
            self::NEGOTIATION => '#F97316',
            self::EXPIRED => '#D97706',
        };
    }

    public function getFilamentBadge(): ?string
    {
        return match($this) {
            self::DRAFT => 'info',
            self::SENT => 'primary',
            self::ACCEPTED => 'success',
            self::REJECTED => 'danger',
            self::NEGOTIATION => 'warning',
            self::EXPIRED => 'warning',
        };
    }



}
