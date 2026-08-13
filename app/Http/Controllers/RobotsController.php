<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * Paths that should never be crawled or indexed.
     *
     * @var array<int, string>
     */
    protected array $disallowed = [
        '/admin',
        '/dashboard',
        '/profile',
        '/bookmarks',
        '/notifications',
        '/search',
        '/newsletter/unsubscribe',
        '/login',
        '/register',
        '/forgot-password',
        '/reset-password',
        '/verify-email',
        '/email/verify',
        '/confirm-password',
    ];

    /**
     * Render robots.txt.
     */
    public function __invoke(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin/',
            'Disallow: /dashboard',
            'Disallow: /profile',
            'Disallow: /bookmarks',
            'Disallow: /notifications',
            'Disallow: /search',
            'Disallow: /newsletter/unsubscribe',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /forgot-password',
            'Disallow: /reset-password',
            'Disallow: /verify-email',
            'Disallow: /email/verify',
            'Disallow: /confirm-password',
            '',
            'Allow: /',
            '',
            'Sitemap: '.url('/sitemap.xml'),
            '',
        ];

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}
