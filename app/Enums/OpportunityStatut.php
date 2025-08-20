<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OpportunityStatut: string implements HasLabel
{
    case OPEN = 'Open';
    case QUALIFICATION = 'Qualification';
    case PROPOSAL = 'Proposal';
    case NEGOTIATION = 'Negotiation';
    case WON = 'Won';
    case LOST = 'Lost';



    public function getLabel(): ?string
    {
        return match ($this) {
            self::OPEN => 'Ouverte',
            self::QUALIFICATION => 'Qualification',
            self::PROPOSAL => 'Proposition',
            self::NEGOTIATION => 'Négociation',
            self::WON => 'Gagnée',
            self::LOST => 'Perdue',
        };
    }

    public function getBadge(): ?string
    {
        return match ($this) {
            self::OPEN => '#DBEAFE',
            self::QUALIFICATION => '#FEF3C7',
            self::PROPOSAL => '#EDE9FE',
            self::NEGOTIATION => '#E5E7EB',
            self::WON => '#D1FAE5',
            self::LOST => '#FEE2E2',
        };
    }


    public function getTailwindBadge(): ?string
    {
        return match ($this) {
            self::OPEN => 'info',
            self::QUALIFICATION => 'warning',
            self::PROPOSAL => 'primary',
            self::NEGOTIATION => 'gray',
            self::WON => 'success',
            self::LOST => 'danger',
        };
    }


    public function getTextStatusColor(): ?string
    {
        return match ($this) {
            self::OPEN => '#1E40AF',
            self::QUALIFICATION => '#92400E',
            self::PROPOSAL => '#5B21B6',
            self::NEGOTIATION => '#4B5563',
            self::WON => '#065F46',
            self::LOST => '#991B1B',
        };
    }

}
