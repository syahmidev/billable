<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StripeEventStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['stripe_event_id', 'type', 'status', 'tenant_id', 'invoice_id', 'payment_intent_id', 'attempts', 'payload', 'last_error', 'processed_at'])]
class StripeEvent extends Model
{
    protected $connection = 'central';

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'processed_at' => 'datetime',
            'status' => StripeEventStatus::class,
        ];
    }

    public function recordAttempt(): void
    {
        $this->forceFill([
            'attempts' => $this->attempts + 1,
            'status' => StripeEventStatus::Received,
            'last_error' => null,
        ])->save();
    }

    public function markProcessed(): void
    {
        $this->forceFill([
            'status' => StripeEventStatus::Processed,
            'processed_at' => now(),
            'last_error' => null,
        ])->save();
    }

    public function markIgnored(string $reason): void
    {
        $this->forceFill([
            'status' => StripeEventStatus::Ignored,
            'processed_at' => now(),
            'last_error' => $reason,
        ])->save();
    }

    public function markFailed(string $reason): void
    {
        $this->forceFill([
            'status' => StripeEventStatus::Failed,
            'last_error' => $reason,
        ])->save();
    }
}
