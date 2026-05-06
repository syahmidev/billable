<?php

declare(strict_types=1);

namespace App\Enums;

enum InvoiceStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';

    public static function values(): array
    {
        return array_map(fn (self $status) => $status->value, self::cases());
    }

    public static function payableValues(): array
    {
        return [
            self::Sent->value,
            self::Overdue->value,
        ];
    }

    public static function remindableValues(): array
    {
        return [
            self::Sent->value,
            self::Overdue->value,
        ];
    }
}
