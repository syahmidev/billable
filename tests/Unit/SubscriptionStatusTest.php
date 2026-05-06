<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\SubscriptionStatus;
use PHPUnit\Framework\TestCase;

class SubscriptionStatusTest extends TestCase
{
    public function test_failure_statuses_require_attention(): void
    {
        $this->assertTrue(SubscriptionStatus::PastDue->needsAttention());
        $this->assertTrue(SubscriptionStatus::Incomplete->needsAttention());
        $this->assertTrue(SubscriptionStatus::Unpaid->needsAttention());
        $this->assertFalse(SubscriptionStatus::Active->needsAttention());
        $this->assertSame('warning', SubscriptionStatus::PastDue->tone());
    }
}
