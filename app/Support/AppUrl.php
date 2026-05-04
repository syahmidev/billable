<?php

declare(strict_types=1);

namespace App\Support;

class AppUrl
{
    public static function central(string $path = '/'): string
    {
        return self::baseUrl().self::normalizePath($path);
    }

    public static function tenant(string $domain, string $path = '/dashboard'): string
    {
        return self::scheme()."://{$domain}".self::normalizePath($path);
    }

    public static function tenantDomain(string $subdomain): string
    {
        return "{$subdomain}.".self::host();
    }

    private static function baseUrl(): string
    {
        return rtrim((string) config('app.url', 'http://localhost'), '/');
    }

    private static function scheme(): string
    {
        return parse_url(self::baseUrl(), PHP_URL_SCHEME) ?: 'http';
    }

    private static function host(): string
    {
        return parse_url(self::baseUrl(), PHP_URL_HOST) ?: 'localhost';
    }

    private static function normalizePath(string $path): string
    {
        return '/'.ltrim($path, '/');
    }
}
