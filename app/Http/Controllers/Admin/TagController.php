<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;
use App\Services\ContentCache;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TagController extends Controller
{
    public function __construct(protected SlugService $slugs)
    {
    }

    /**
     * Display a listing of the tags.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Tag::class);

        $query = Tag::query()
            ->withCount(['posts' => fn ($q) => $q->published()->public()])
            ->search($request->query('search'));

        $trashed = $request->boolean('trashed');

        if ($trashed) {
            $query->onlyTrashed();
        }

        $tags = $query->orderBy('name')->paginate(config('blog.pagination.admin_tags'))->withQueryString();

        return view('admin.tags.index', [
            'tags' => $tags,
            'trashed' => $trashed,
            'search' => $request->query('search'),
        ]);
    }

    /**
     * Show the form for creating a new tag.
     */
    public function create(): View
    {
        $this->authorize('create', Tag::class);

        return view('admin.tags.create');
    }

    /**
     * Store a newly created tag.
     */
    public function store(StoreTagRequest $request): RedirectResponse
    {
        $this->authorize('create', Tag::class);

        $data = $request->validated();

        $data['slug'] = $this->slugs->unique($data['name'], Tag::class, null, $data['slug'] ?? null);

        $tag = Tag::create($data);

        ContentCache::flush();

        return Redirect::route('admin.tags.edit', $tag)
            ->with('status', 'Tag created.');
    }

    /**
     * Show the form for editing the specified tag.
     */
    public function edit(Tag $tag): View
    {
        $this->authorize('update', $tag);

        return view('admin.tags.edit', ['tag' => $tag]);
    }

    /**
     * Update the specified tag.
     */
    public function update(UpdateTagRequest $request, Tag $tag): RedirectResponse
    {
        $this->authorize('update', $tag);

        $data = $request->validated();

        $data['slug'] = $this->slugs->unique($data['name'], Tag::class, $tag->id, $data['slug'] ?? null);

        $tag->update($data);

        ContentCache::flush();

        return Redirect::route('admin.tags.edit', $tag)
            ->with('status', 'Tag updated.');
    }

    /**
     * Remove the specified tag.
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $this->authorize('delete', $tag);

        $tag->delete();

        ContentCache::flush();

        return Redirect::route('admin.tags.index')
            ->with('status', 'Tag moved to trash.');
    }

    /**
     * Restore the specified trashed tag.
     */
    public function restore(Tag $tag): RedirectResponse
    {
        $this->authorize('restore', $tag);

        $tag->restore();

        ContentCache::flush();

        return Redirect::route('admin.tags.index')
            ->with('status', 'Tag restored.');
    }
}
