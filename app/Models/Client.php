<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'email', 'phone', 'company', 'address', 'notes'])]
class Client extends Model
{
    use SoftDeletes;

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
