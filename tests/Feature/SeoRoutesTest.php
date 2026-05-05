<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class SeoRoutesTest extends TestCase
{
    public function test_robots_file_points_to_the_sitemap_and_blocks_app_routes(): void
    {
        $baseUrl = rtrim((string) config('app.url'), '/');

        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /dashboard', false)
            ->assertSee('Disallow: /billing', false)
            ->assertSee("Sitemap: {$baseUrl}/sitemap.xml", false);
    }

    public function test_sitemap_contains_the_central_landing_page(): void
    {
        $baseUrl = rtrim((string) config('app.url'), '/');

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee("<loc>{$baseUrl}/</loc>", false);
    }
}
