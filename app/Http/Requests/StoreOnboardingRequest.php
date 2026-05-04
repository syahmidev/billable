<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Actions\Tenant\CreateWorkspace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreOnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workspace_name' => ['required', 'string', 'max:100'],
            'subdomain' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9][a-z0-9-]*[a-z0-9]$|^[a-z0-9]$/'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->has('subdomain')) {
                return;
            }

            if (app(CreateWorkspace::class)->isSubdomainTaken((string) $this->input('subdomain'))) {
                $validator->errors()->add('subdomain', 'This subdomain is not available.');
            }
        });
    }
}
