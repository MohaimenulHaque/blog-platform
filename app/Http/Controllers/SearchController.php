<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Display search results.
     */
    public function __invoke(Request $request): View
    {
        $query = trim((string) $request->query('q'));

        $posts = filled($query)
            ? Post::query()
                ->published()
                ->public()
                ->with(['author', 'category', 'tags'])
                ->advancedSearch($query)
                ->latest('published_at')
                ->paginate(config('blog.pagination.public_posts'))
                ->withQueryString()
            : null;

        $popularCategories = $this->remember('search.popular.categories', fn () => Category::query()
            ->active()
            ->withCount(['posts' => fn (Builder $q) => $q->published()->public()])
            ->orderByDesc('posts_count')
            ->orderBy('name')
            ->limit(6)
            ->get());

        $popularTags = $this->remember('search.popular.tags', fn () => Tag::query()
            ->withCount(['posts' => fn (Builder $q) => $q->published()->public()])
            ->orderByDesc('posts_count')
            ->orderBy('name')
            ->limit(10)
            ->get());

        return view('pages.search', compact('query', 'posts', 'popularCategories', 'popularTags'));
    }
}
