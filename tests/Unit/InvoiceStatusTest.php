<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\InvoiceStatus;
use PHPUnit\Framework\TestCase;

class InvoiceStatusTest extends TestCase
{
    public function test_only_sent_and_overdue_invoices_are_remindable(): void
    {
        $this->assertSame([
            InvoiceStatus::Sent->value,
            InvoiceStatus::Overdue->value,
        ], InvoiceStatus::remindableValues());
    }
}
