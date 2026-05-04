<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PlanSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'slug', 'stripe_price_id', 'price', 'features', 'is_active'])]
class Plan extends Model
{
    protected $connection = 'pgsql';

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_active' => 'boolean',
            'price' => 'integer',
        ];
    }

    public function isFree(): bool
    {
        return $this->slug === PlanSlug::Free->value || $this->price === 0;
    }

    public function formattedPrice(): string
    {
        return $this->price === 0 ? 'Free' : '$'.$this->price.'/mo';
    }
}
