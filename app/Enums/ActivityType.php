<?php

declare(strict_types=1);

namespace App\Enums;

enum ActivityType: string
{
    case BillingPlanChanged = 'billing.plan_changed';
    case ClientArchived = 'client.archived';
    case ClientCreated = 'client.created';
    case ClientUpdated = 'client.updated';
    case InvoiceCreated = 'invoice.created';
    case InvoiceDeleted = 'invoice.deleted';
    case InvoicePaid = 'invoice.paid';
    case InvoiceReminderSent = 'invoice.reminder_sent';
    case InvoiceSent = 'invoice.sent';
    case InvoiceUpdated = 'invoice.updated';
    case TeamMemberAdded = 'team.member_added';
    case TeamMemberRemoved = 'team.member_removed';
    case TeamMemberUpdated = 'team.member_updated';

    public function label(): string
    {
        return match ($this) {
            self::BillingPlanChanged => 'Billing updated',
            self::ClientArchived => 'Client archived',
            self::ClientCreated => 'Client created',
            self::ClientUpdated => 'Client updated',
            self::InvoiceCreated => 'Invoice created',
            self::InvoiceDeleted => 'Invoice deleted',
            self::InvoicePaid => 'Invoice paid',
            self::InvoiceReminderSent => 'Reminder sent',
            self::InvoiceSent => 'Invoice sent',
            self::InvoiceUpdated => 'Invoice updated',
            self::TeamMemberAdded => 'Team member added',
            self::TeamMemberRemoved => 'Team member removed',
            self::TeamMemberUpdated => 'Team member updated',
        };
    }
}
