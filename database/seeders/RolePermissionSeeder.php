<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Models\Permission as PermissionModel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Permission::cases() as $permission) {
            PermissionModel::findOrCreate($permission->value);
        }

        foreach (UserRole::cases() as $role) {
            Role::findOrCreate($role->value)->syncPermissions($role->permissions());
        }

        User::query()
            ->whereNotNull('role')
            ->get()
            ->each(function (User $user): void {
                $role = UserRole::tryFrom((string) $user->role);

                if ($role) {
                    $user->syncRoles($role->value);
                }
            });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
