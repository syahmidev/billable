<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['invoice_number', 'client_id', 'status', 'issue_date', 'due_date', 'subtotal', 'discount_percent', 'tax_percent', 'total', 'notes', 'sent_at', 'payment_token', 'stripe_payment_intent_id'])]
class Invoice extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = InvoiceStatus::Draft->value;

    public const STATUS_SENT = InvoiceStatus::Sent->value;

    public const STATUS_PAID = InvoiceStatus::Paid->value;

    public const STATUS_OVERDUE = InvoiceStatus::Overdue->value;

    public const STATUS_CANCELLED = InvoiceStatus::Cancelled->value;

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'tax_percent' => 'decimal:2',
            'total' => 'decimal:2',
            'sent_at' => 'datetime',
        ];
    }

    public static function generateNumber(): string
    {
        $next = static::withTrashed()->count() + 1;

        return 'INV-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function discountAmount(): float
    {
        return (float) $this->subtotal * ((float) $this->discount_percent / 100);
    }

    public function taxAmount(): float
    {
        return ((float) $this->subtotal - $this->discountAmount()) * ((float) $this->tax_percent / 100);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
