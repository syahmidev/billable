<?php

declare(strict_types=1);

namespace App\Enums;

enum StripeEventStatus: string
{
    case Received = 'received';
    case Processed = 'processed';
    case Ignored = 'ignored';
    case Failed = 'failed';

    public function isHandled(): bool
    {
        return in_array($this, [self::Processed, self::Ignored], true);
    }
}
