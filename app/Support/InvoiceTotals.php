<?php

declare(strict_types=1);

namespace App\Support;

final readonly class InvoiceTotals
{
    public function __construct(
        public float $subtotal,
        public float $discountAmount,
        public float $taxAmount,
        public float $total,
    ) {}

    public static function fromItems(array $items, mixed $discountPercent = 0, mixed $taxPercent = 0): self
    {
        $subtotal = array_reduce(
            $items,
            fn (float $sum, array $item): float => $sum + self::lineTotal($item),
            0.0
        );

        $discountAmount = $subtotal * (self::numeric($discountPercent) / 100);
        $taxAmount = ($subtotal - $discountAmount) * (self::numeric($taxPercent) / 100);

        return new self(
            subtotal: $subtotal,
            discountAmount: $discountAmount,
            taxAmount: $taxAmount,
            total: $subtotal - $discountAmount + $taxAmount,
        );
    }

    public static function lineTotal(array $item): float
    {
        return self::numeric($item['quantity'] ?? 0) * self::numeric($item['unit_price'] ?? 0);
    }

    private static function numeric(mixed $value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }
}
