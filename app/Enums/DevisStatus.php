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

}
