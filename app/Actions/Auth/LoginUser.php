<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginUser
{
    public function handle(Request $request, string $email, string $password, bool $remember): bool
    {
        if (! Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            return false;
        }

        $request->session()->regenerate();

        return true;
    }
}
