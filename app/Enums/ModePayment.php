<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ModePayment: string implements HasLabel
{
    // modes de paiement: virement, chèque, carte bancaire, prélèvement

    case BANK_TRANSFER = "Bank Transfer";
    case CHECK = "Check";
    case CREDIT_CARD = "Credit Card";
    case DIRECT_DEBIT = "Direct Debit";
    case CASH = "Cash";

    public function getLabel(): string
    {
        return match($this) {
            self::BANK_TRANSFER => 'Virement',
            self::CHECK => 'Chèque',
            self::CREDIT_CARD => 'Carte Bancaire',
            self::DIRECT_DEBIT => 'Prélèvement',
            self::CASH => 'Espèces',
        };
    }

    public function getBadge(): ?string
    {
        return match($this) {
            self::BANK_TRANSFER => '#3B82F6',
            self::CHECK => '#6B7280',
            self::CREDIT_CARD => '#22C55E',
            self::DIRECT_DEBIT => '#F97316',
            self::CASH => '#D97706',
        };
    }


    public function getFilamentBadge(): ?string
    {
        return match($this) {
            self::BANK_TRANSFER => 'primary',
            self::CHECK => 'info',
            self::CREDIT_CARD => 'success',
            self::DIRECT_DEBIT => 'warning',
            self::CASH => 'danger',
        };
    }

}
