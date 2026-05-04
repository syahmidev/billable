<?php

declare(strict_types=1);

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Support\AppUrl;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $url = htmlspecialchars(AppUrl::central('/'), ENT_XML1);

        $xml = <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
            <url>
                <loc>{$url}</loc>
                <changefreq>weekly</changefreq>
                <priority>1.0</priority>
            </url>
        </urlset>
        XML;

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
