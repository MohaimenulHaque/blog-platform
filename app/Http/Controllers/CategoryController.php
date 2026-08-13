<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display the list of active categories.
     */
    public function index(): View
    {
        $categories = Category::query()
            ->active()
            ->withCount(['posts' => fn ($query) => $query->published()->public()])
            ->orderBy('name')
            ->paginate(config('blog.pagination.public_categories'));

        return view('categories.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Display the posts within a category.
     */
    public function show(Category $category): View
    {
        if (! $category->isActive()) {
            abort(404);
        }

        $posts = Post::query()
            ->published()
            ->public()
            ->with(['author', 'tags'])
            ->where('category_id', $category->id)
            ->latest('published_at')
            ->paginate(config('blog.pagination.public_posts'))
            ->withQueryString();

        $category->loadCount(['posts' => fn (Builder $q) => $q->published()->public()]);

        $relatedCategories = Category::query()
            ->active()
            ->where('id', '!=', $category->id)
            ->withCount(['posts' => fn ($query) => $query->published()->public()])
            ->orderByDesc('posts_count')
            ->limit(8)
            ->get();

        return view('categories.show', [
            'category' => $category,
            'posts' => $posts,
            'relatedCategories' => $relatedCategories,
        ]);
    }
}
