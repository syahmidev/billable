<?php

declare(strict_types=1);

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Support\AppUrl;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /billing',
            'Disallow: /clients',
            'Disallow: /dashboard',
            'Disallow: /invoices',
            'Disallow: /login',
            'Disallow: /onboarding',
            'Disallow: /pay',
            'Disallow: /plans',
            'Disallow: /register',
            'Disallow: /stripe',
            '',
            'Sitemap: '.AppUrl::central('/sitemap.xml'),
            '',
        ]);

        return response($content, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
