<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Actions\Team\CreateTeamMember;
use App\Actions\Team\RemoveTeamMember;
use App\Actions\Team\UpdateTeamMemberRole;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreTeamMemberRequest;
use App\Http\Requests\Tenant\UpdateTeamMemberRoleRequest;
use App\Models\User;
use App\Queries\Tenant\TeamMemberListingQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class TeamMemberController extends Controller
{
    public function index(Request $request, TeamMemberListingQuery $members): Response
    {
        Gate::authorize('view-team-members');

        return Inertia::render('Tenant/Team/Index', [
            'members' => $members->handle((string) tenant('id'), $request->user()),
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
        Gate::authorize('manage-team-members');
        $this->ensureTenantMember($member);

        $action->handle($member, $request->user());

        return back()->with('success', 'Team member removed.');
    }

    private function ensureTenantMember(User $member): void
    {
        abort_unless($member->tenant_id === (string) tenant('id'), 404);
    }
}
