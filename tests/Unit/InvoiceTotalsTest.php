<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\InvoiceTotals;
use PHPUnit\Framework\TestCase;

class InvoiceTotalsTest extends TestCase
{
    public function test_it_calculates_invoice_totals(): void
    {
        $totals = InvoiceTotals::fromItems(
            [
                ['quantity' => 2, 'unit_price' => 50],
                ['quantity' => 1.5, 'unit_price' => 100],
            ],
            discountPercent: 10,
            taxPercent: 8,
        );

        $this->assertEqualsWithDelta(250, $totals->subtotal, 0.001);
        $this->assertEqualsWithDelta(25, $totals->discountAmount, 0.001);
        $this->assertEqualsWithDelta(18, $totals->taxAmount, 0.001);
        $this->assertEqualsWithDelta(243, $totals->total, 0.001);
    }

    public function test_it_calculates_line_totals_from_numeric_strings(): void
    {
        $this->assertEqualsWithDelta(99.99, InvoiceTotals::lineTotal([
            'quantity' => '3',
            'unit_price' => '33.33',
        ]), 0.001);
    }
}
