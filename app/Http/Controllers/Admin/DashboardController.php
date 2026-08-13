<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CommentStatus;
use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Media;
use App\Models\NewsletterSubscriber;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with statistics and charts.
     */
    public function index(): View
    {
        $this->authorize('access-admin');

        $users = User::count();
        $posts = Post::count();
        $published = Post::published()->count();
        $draft = Post::where('status', PostStatus::Draft)->count();
        $scheduled = Post::where('status', PostStatus::Scheduled)->count();
        $categories = Category::count();
        $tags = Tag::count();
        $comments = Comment::count();
        $pendingComments = Comment::pending()->count();
        $subscribers = NewsletterSubscriber::subscribed()->count();
        $views = Post::sum('view_count');
        $likes = PostLike::count();

        $recentPosts = Post::query()
            ->with(['author', 'category'])
            ->latest()
            ->limit(6)
            ->get();

        $recentComments = Comment::query()
            ->with(['user', 'post'])
            ->latest()
            ->limit(6)
            ->get();

        return view('admin.dashboard', [
            'stats' => compact(
                'users',
                'posts',
                'published',
                'draft',
                'scheduled',
                'categories',
                'tags',
                'comments',
                'pendingComments',
                'subscribers',
                'views',
                'likes'
            ),
            'charts' => $this->chartData(),
            'recentPosts' => $recentPosts,
            'recentComments' => $recentComments,
        ]);
    }

    /**
     * Build the chart datasets for the dashboard.
     *
     * @return array<string, mixed>
     */
    protected function chartData(): array
    {
        $months = collect(range(11, 0))->map(fn (int $offset) => now()->startOfMonth()->subMonths($offset));
        $labels = $months->map(fn (Carbon $month) => $month->format('M Y'));

        $countByMonth = fn (string $table, string $column = 'created_at') => $months->map(
            fn (Carbon $month) => DB::table($table)
                ->where($column, '>=', $month->copy()->startOfMonth())
                ->where($column, '<', $month->copy()->addMonth()->startOfMonth())
                ->count()
        );

        $postsPublished = $months->map(
            fn (Carbon $month) => Post::where('published_at', '>=', $month->copy()->startOfMonth())
                ->where('published_at', '<', $month->copy()->addMonth()->startOfMonth())
                ->count()
        );

        $viewsByMonth = $months->map(
            fn (Carbon $month) => (int) Post::where('published_at', '>=', $month->copy()->startOfMonth())
                ->where('published_at', '<', $month->copy()->addMonth()->startOfMonth())
                ->sum('view_count')
        );

        $popularCategories = Category::query()
            ->withCount(['posts' => fn ($q) => $q->published()->public()])
            ->orderByDesc('posts_count')
            ->limit(6)
            ->get();

        $popularPosts = Post::query()
            ->published()
            ->public()
            ->orderByDesc('view_count')
            ->limit(6)
            ->get(['title', 'view_count']);

        return [
            'labels' => $labels->values()->all(),
            'users' => $countByMonth('users')->values()->all(),
            'postsPublished' => $postsPublished->values()->all(),
            'views' => $viewsByMonth->values()->all(),
            'comments' => $countByMonth('comments')->values()->all(),
            'popularCategories' => [
                'labels' => $popularCategories->pluck('name')->all(),
                'counts' => $popularCategories->pluck('posts_count')->all(),
            ],
            'popularPosts' => [
                'labels' => $popularPosts->pluck('title')->all(),
                'counts' => $popularPosts->pluck('view_count')->all(),
            ],
        ];
    }
}
