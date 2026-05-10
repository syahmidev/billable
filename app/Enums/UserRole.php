<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Owner = 'owner';
    case Member = 'member';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Owner',
            self::Member => 'Member',
        };
    }

    public static function values(): array
    {
        return array_map(fn (self $role) => $role->value, self::cases());
    }

    public function permissions(): array
    {
        return match ($this) {
            self::Owner => Permission::values(),
            self::Member => [
                Permission::ActivityView->value,
                Permission::BillingView->value,
                Permission::ClientsCreate->value,
                Permission::ClientsUpdate->value,
                Permission::ClientsView->value,
                Permission::InvoicesCreate->value,
                Permission::InvoicesRemind->value,
                Permission::InvoicesSend->value,
                Permission::InvoicesUpdate->value,
                Permission::InvoicesView->value,
            ],
        };
    }
}
