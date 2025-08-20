<?php

namespace App\Enums;
use Filament\Support\Contracts\HasLabel;


enum ExpenseStatus: string implements HasLabel
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT => 'Brouillon',
            self::SUBMITTED => 'Soumise',
            self::APPROVED => 'Approuvée',
            self::REJECTED => 'Rejetée',
            self::PAID => 'Payée',
            self::CANCELLED => 'Annulée',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::DRAFT => 'info',
            self::SUBMITTED => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::PAID => 'success',
            self::CANCELLED => 'dark',
        };
    }


}
