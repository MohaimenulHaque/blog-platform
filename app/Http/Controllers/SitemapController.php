<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * The frequency with which search engines should re-crawl the sitemap.
     */
    protected int $cacheTtl = 3600;

    /**
     * Render the XML sitemap.
     */
    public function __invoke(): Response
    {
        $cached = Cache::remember(
            'sitemap.xml',
            now()->addSeconds($this->cacheTtl),
            fn (): string => $this->build(),
        );

        return response($cached, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }

    /**
     * Build the sitemap markup.
     */
    protected function build(): string
    {
        $entries = $this->collectEntries();

        $urls = collect($entries)
            ->map(fn (array $entry): string => $this->urlTag(
                $entry['loc'],
                $entry['lastmod'] ?? null,
                $entry['changefreq'] ?? 'weekly',
                $entry['priority'] ?? 0.5,
            ))
            ->implode("\n");

        return '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"
            .$urls."\n"
            .'</urlset>'."\n";
    }

    /**
     * Collect every public, indexable URL on the site.
     *
     * @return array<int, array{loc: string, lastmod?: string, changefreq?: string, priority?: float}>
     */
    protected function collectEntries(): array
    {
        $entries = [
            ['loc' => route('home'), 'changefreq' => 'daily', 'priority' => 1.0],
            ['loc' => route('blog.index'), 'changefreq' => 'daily', 'priority' => 0.9],
            ['loc' => route('categories.index'), 'changefreq' => 'weekly', 'priority' => 0.6],
            ['loc' => route('tags.index'), 'changefreq' => 'weekly', 'priority' => 0.5],
            ['loc' => route('authors.index'), 'changefreq' => 'weekly', 'priority' => 0.6],
        ];

        foreach (Post::query()->published()->public()->latest('published_at')->get(['slug', 'updated_at']) as $post) {
            $entries[] = [
                'loc' => route('blog.show', $post->slug),
                'lastmod' => $post->updated_at?->toW3cString(),
                'changefreq' => 'monthly',
                'priority' => 0.8,
            ];
        }

        foreach (Category::query()->active()->get(['slug', 'updated_at']) as $category) {
            $entries[] = [
                'loc' => route('categories.show', $category->slug),
                'lastmod' => $category->updated_at?->toW3cString(),
                'changefreq' => 'weekly',
                'priority' => 0.7,
            ];
        }

        foreach (Tag::query()->get(['slug', 'updated_at']) as $tag) {
            $entries[] = [
                'loc' => route('tags.show', $tag->slug),
                'lastmod' => $tag->updated_at?->toW3cString(),
                'changefreq' => 'weekly',
                'priority' => 0.5,
            ];
        }

        foreach (User::query()
            ->whereHas('roles', fn (Builder $query) => $query->where('slug', 'author'))
            ->get(['username', 'updated_at']) as $author) {
            $entries[] = [
                'loc' => route('authors.show', $author->username),
                'lastmod' => $author->updated_at?->toW3cString(),
                'changefreq' => 'monthly',
                'priority' => 0.6,
            ];
        }

        return $entries;
    }

    /**
     * Build a single <url> element, escaping the loc attribute.
     */
    protected function urlTag(string $loc, ?string $lastmod, string $changefreq, float $priority): string
    {
        $xml = "\t<url>\n"
            ."\t\t<loc>".e($loc, false)."</loc>\n";

        if ($lastmod) {
            $xml .= "\t\t<lastmod>".e($lastmod, false)."</lastmod>\n";
        }

        $xml .= "\t\t<changefreq>".e($changefreq, false)."</changefreq>\n"
            ."\t\t<priority>".number_format($priority, 1)."</priority>\n"
            ."\t</url>";

        return $xml;
    }
}
