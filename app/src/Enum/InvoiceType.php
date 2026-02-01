<?php

namespace App\Enum;

enum InvoiceType: string
{
    case SALE = 'sale';
    case COST = 'cost';

    public function label(): string
    {
        return match($this) {
            self::SALE => 'Sprzedażowa',
            self::COST => 'Kosztowa',
        };
    }
}
