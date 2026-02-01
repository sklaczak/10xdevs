<?php


namespace App\Enum;

enum UninvoicedPurchaseStatus: string
{
    case PENDING = 'pending';
    case PARTIALLY_SETTLED = 'partially_settled';
    case SETTLED = 'settled';
}
