<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Actions\Team\CreateTeamMember;
use App\Actions\Team\RemoveTeamMember;
use App\Actions\Team\UpdateTeamMemberRole;
use App\Enums\Permission;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreTeamMemberRequest;
use App\Http\Requests\Tenant\UpdateTeamMemberRoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeamMemberController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->hasTenantPermission(Permission::TeamView), 403);

        $members = User::query()
            ->where('tenant_id', tenant('id'))
            ->get()
            ->sortBy(fn (User $member): string => ($member->role === UserRole::Owner->value ? '0' : '1').strtolower($member->name))
            ->values()
            ->map(fn (User $member): array => [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'role' => $member->role,
                'is_current_user' => $member->is($request->user()),
                'created_at' => $member->created_at?->format('M d, Y'),
            ]);

        return Inertia::render('Tenant/Team/Index', [
            'members' => $members,
            'roles' => array_map(fn (UserRole $role): array => [
                'value' => $role->value,
                'label' => $role->label(),
            ], UserRole::cases()),
        ]);
    }

    public function store(StoreTeamMemberRequest $request, CreateTeamMember $action): RedirectResponse
    {
        $member = $action->handle(
            data: $request->validated(),
            actor: $request->user(),
            tenantId: (string) tenant('id'),
        );

        return back()->with('success', "{$member->name} was added to the team.");
    }

    public function update(
        UpdateTeamMemberRoleRequest $request,
        User $member,
        UpdateTeamMemberRole $action,
    ): RedirectResponse {
        $this->ensureTenantMember($member);

        $action->handle(
            member: $member,
            actor: $request->user(),
            role: UserRole::from($request->validated('role')),
        );

        return back()->with('success', "{$member->name}'s role was updated.");
    }

    public function destroy(Request $request, User $member, RemoveTeamMember $action): RedirectResponse
    {
        abort_unless($request->user()?->hasTenantPermission(Permission::TeamManage), 403);
        $this->ensureTenantMember($member);

        $action->handle($member, $request->user());

        return back()->with('success', 'Team member removed.');
    }

    private function ensureTenantMember(User $member): void
    {
        abort_unless($member->tenant_id === tenant('id'), 404);
    }
}
