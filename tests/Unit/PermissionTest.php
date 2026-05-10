<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\Permission;
use App\Enums\UserRole;
use PHPUnit\Framework\TestCase;

class PermissionTest extends TestCase
{
    public function test_owner_role_receives_every_workspace_permission(): void
    {
        $this->assertSame(Permission::values(), UserRole::Owner->permissions());
    }

    public function test_member_role_cannot_manage_team_billing_or_deletions(): void
    {
        $memberPermissions = UserRole::Member->permissions();

        $this->assertContains(Permission::InvoicesCreate->value, $memberPermissions);
        $this->assertContains(Permission::InvoicesSend->value, $memberPermissions);
        $this->assertNotContains(Permission::TeamManage->value, $memberPermissions);
        $this->assertNotContains(Permission::BillingManage->value, $memberPermissions);
        $this->assertNotContains(Permission::ClientsDelete->value, $memberPermissions);
        $this->assertNotContains(Permission::InvoicesDelete->value, $memberPermissions);
    }
}
