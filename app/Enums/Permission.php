<?php

declare(strict_types=1);

namespace App\Enums;

enum Permission: string
{
    case ActivityView = 'activity.view';
    case BillingManage = 'billing.manage';
    case BillingView = 'billing.view';
    case ClientsCreate = 'clients.create';
    case ClientsDelete = 'clients.delete';
    case ClientsUpdate = 'clients.update';
    case ClientsView = 'clients.view';
    case InvoicesCreate = 'invoices.create';
    case InvoicesDelete = 'invoices.delete';
    case InvoicesRemind = 'invoices.remind';
    case InvoicesSend = 'invoices.send';
    case InvoicesUpdate = 'invoices.update';
    case InvoicesView = 'invoices.view';
    case TeamManage = 'team.manage';
    case TeamView = 'team.view';

    public static function values(): array
    {
        return array_map(fn (self $permission) => $permission->value, self::cases());
    }
}
