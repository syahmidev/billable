<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Enums\PlanSlug;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'plan' => ['nullable', new Enum(PlanSlug::class)],
        ];
    }
}
