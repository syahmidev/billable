<?php

declare(strict_types=1);

namespace App\Enums;

enum PlanSlug: string
{
    case Free = 'free';
    case Pro = 'pro';
    case Business = 'business';
}
