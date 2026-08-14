<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostStatus;
use App\Enums\PostVisibility;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\ContentCache;
use App\Services\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(protected PostService $posts)
    {
    }

    /**
     * Display a listing of the posts.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Post::class);

        $query = Post::query()
            ->with(['author', 'category'])
            ->search($request->query('search'));

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        if (! $request->user()->can('manage-content')) {
            $query->where('author_id', $request->user()->id);
        }

        $posts = $query->latest()->paginate(config('blog.pagination.admin_posts'))->withQueryString();

        return view('admin.posts.index', [
            'posts' => $posts,
            'categories' => Category::orderBy('name')->get(),
            'statuses' => PostStatus::options(),
            'filters' => [
                'search' => $request->query('search'),
                'status' => $request->query('status'),
                'category_id' => $request->query('category_id'),
            ],
        ]);
    }

    /**
     * Display a listing of trashed posts.
     */
    public function trashed(Request $request): View
    {
        $this->authorize('manage-content', Post::class);

        $query = Post::onlyTrashed()->with(['author', 'category']);

        if ($request->filled('search')) {
            $query->search($request->query('search'));
        }

        $posts = $query->latest()->paginate(config('blog.pagination.admin_posts'))->withQueryString();

        return view('admin.posts.trashed', [
            'posts' => $posts,
            'search' => $request->query('search'),
        ]);
    }

    /**
     * Show the form for creating a new post.
     */
    public function create(): View
    {
        $this->authorize('create', Post::class);
        return view('admin.posts.create', $this->formData());
    }

    /**
     * Store a newly created post.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $this->authorize('create', Post::class);
        $post = $this->posts->create($request->validated(), $request->user()->id);
        $this->posts->storeImages($post, $request->all());
        return Redirect::route('admin.posts.edit', $post)->with('status', 'Post created.');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): View
    {
        $this->authorize('view', $post);

        $post->load(['author', 'category', 'tags']);

        return view('admin.posts.show', ['post' => $post]);
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        return view('admin.posts.edit', [
            'post' => $post->load(['tags']),
            ...$this->formData(),
        ]);
    }

    /**
     * Update the specified post.
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $post = $this->posts->update($post, $request->validated());

        $this->posts->storeImages($post, $request->all());

        return Redirect::route('admin.posts.edit', $post)
            ->with('status', 'Post updated.');
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        ContentCache::flush();

        return Redirect::route('admin.posts.index')
            ->with('status', 'Post moved to trash.');
    }

    /**
     * Restore the specified trashed post.
     */
    public function restore(Post $post): RedirectResponse
    {
        $this->authorize('restore', $post);

        $post->restore();

        ContentCache::flush();

        return Redirect::route('admin.posts.index')
            ->with('status', 'Post restored.');
    }

    /**
     * Publish the specified post.
     */
    public function publish(Post $post): RedirectResponse
    {
        $this->authorize('publish', $post);

        $this->posts->publish($post);

        return Redirect::route('admin.posts.show', $post)
            ->with('status', 'Post published.');
    }

    /**
     * Unpublish the specified post.
     */
    public function unpublish(Post $post): RedirectResponse
    {
        $this->authorize('unpublish', $post);

        $this->posts->unpublish($post);

        return Redirect::route('admin.posts.show', $post)
            ->with('status', 'Post unpublished.');
    }

    /**
     * Schedule the specified post.
     */
    public function schedule(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('schedule', $post);

        $validated = $request->validate([
            'scheduled_at' => ['required', 'date'],
        ]);

        $this->posts->schedule($post, $validated['scheduled_at']);

        return Redirect::route('admin.posts.show', $post)
            ->with('status', 'Post scheduled.');
    }

    /**
     * Archive the specified post.
     */
    public function archive(Post $post): RedirectResponse
    {
        $this->authorize('archive', $post);

        $this->posts->archive($post);

        return Redirect::route('admin.posts.show', $post)
            ->with('status', 'Post archived.');
    }

    /**
     * Move the specified post back to draft.
     */
    public function draft(Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $this->posts->draft($post);

        return Redirect::route('admin.posts.show', $post)
            ->with('status', 'Post moved to draft.');
    }

    /**
     * Shared data for create and edit forms.
     *
     * @return array<string, mixed>
     */
    protected function formData(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'statuses' => PostStatus::options(),
            'visibilities' => PostVisibility::options(),
        ];
    }
}
