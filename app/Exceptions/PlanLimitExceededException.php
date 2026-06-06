<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class PlanLimitExceededException extends RuntimeException
{
    public static function clients(int $limit): self
    {
        return new self("Your plan allows up to {$limit} clients. Upgrade to add more.");
    }

    public static function invoices(int $limit): self
    {
        return new self("Your plan allows up to {$limit} invoices per month. Upgrade to add more.");
    }
}
