<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateTeamMemberRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && Gate::forUser($user)->allows('manage-team-members');
    }

    public function rules(): array
    {
        return [
            'role' => ['required', Rule::in(UserRole::values())],
        ];
    }
}
