<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class AuthorController extends Controller
{
    /**
     * Display the list of authors.
     */
    public function index(): View
    {
        $authors = User::query()
            ->whereHas('roles', fn (Builder $query) => $query->where('slug', 'author'))
            ->with('role')
            ->withCount(['publishedPosts'])
            ->orderBy('name')
            ->paginate(config('blog.pagination.public_authors'));

        return view('authors.index', [
            'authors' => $authors,
        ]);
    }

    /**
     * Display an author's profile and published posts.
     */
    public function show(string $username): View
    {
        $author = User::query()
            ->whereHas('roles', fn (Builder $query) => $query->where('slug', 'author'))
            ->with('role')
            ->where('username', $username)
            ->firstOrFail();

        $posts = Post::query()
            ->published()
            ->public()
            ->with(['category', 'tags'])
            ->where('author_id', $author->id)
            ->latest('published_at')
            ->paginate(config('blog.pagination.public_posts'))
            ->withQueryString();

        $stats = $this->authorStats($author);

        return view('authors.show', [
            'author' => $author,
            'posts' => $posts,
            'stats' => $stats,
        ]);
    }

    /**
     * Aggregate an author's public statistics.
     *
     * @return array{posts: int, views: int, likes: int, comments: int}
     */
    protected function authorStats(User $author): array
    {
        $row = Post::query()
            ->published()
            ->public()
            ->where('author_id', $author->id)
            ->selectRaw('COUNT(*) as posts')
            ->selectRaw('COALESCE(SUM(view_count), 0) as views')
            ->selectRaw('COALESCE(SUM(like_count), 0) as likes')
            ->selectRaw('COALESCE(SUM(comment_count), 0) as comments')
            ->first();

        return [
            'posts' => (int) ($row->posts ?? 0),
            'views' => (int) ($row->views ?? 0),
            'likes' => (int) ($row->likes ?? 0),
            'comments' => (int) ($row->comments ?? 0),
        ];
    }
}
