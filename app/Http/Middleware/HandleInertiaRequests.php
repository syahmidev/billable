<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\AppUrl;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'tenant_id' => $request->user()->tenant_id,
                    'role' => $request->user()->role,
                ] : null,
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'seo' => [
                'site_name' => config('seo.site_name'),
                'title' => config('seo.title'),
                'description' => config('seo.description'),
                'image' => config('seo.image'),
                'twitter_site' => config('seo.twitter_site'),
                'base_url' => rtrim(AppUrl::central('/'), '/'),
                'current_url' => $request->url(),
            ],
        ]);
    }
}
