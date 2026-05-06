<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamMemberRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() === true
            && $this->user()->belongsToTenant((string) tenant('id'));
    }

    public function rules(): array
    {
        return [
            'role' => ['required', Rule::in(UserRole::values())],
        ];
    }
}
