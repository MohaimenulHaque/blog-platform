<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the public homepage.
     */
    public function __invoke(): View
    {
        $recent = Post::query()
            ->published()
            ->public()
            ->with(['author', 'category', 'tags'])
            ->latest('published_at')
            ->limit(10)
            ->get();

        $hero = $recent->shift();
        $featured = $recent->splice(0, 3)->values();
        $latest = $recent->take(6)->values();

        $trending = $this->remember('home.trending', fn () => Post::query()
            ->published()
            ->public()
            ->with(['author', 'category'])
            ->orderByDesc('view_count')
            ->limit(5)
            ->get());

        $categories = $this->remember('home.categories', fn () => Category::query()
            ->active()
            ->withCount(['posts' => fn (Builder $q) => $q->published()->public()])
            ->orderByDesc('posts_count')
            ->orderBy('name')
            ->limit(6)
            ->get());

        $authors = $this->remember('home.authors', fn () => User::query()
            ->whereHas('roles', fn (Builder $q) => $q->where('slug', 'author'))
            ->with('role')
            ->withCount(['publishedPosts'])
            ->orderByDesc('published_posts_count')
            ->orderBy('name')
            ->limit(4)
            ->get());

        $stats = $this->remember('home.stats', fn () => [
            'posts' => Post::query()->published()->public()->count(),
            'authors' => User::query()
                ->whereHas('roles', fn (Builder $q) => $q->where('slug', 'author'))
                ->count(),
            'views' => (int) Post::query()->published()->public()->sum('view_count'),
        ]);

        return view('home', compact('hero', 'featured', 'trending', 'latest', 'categories', 'authors', 'stats'));
    }
}
