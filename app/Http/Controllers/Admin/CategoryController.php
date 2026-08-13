<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CategoryStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Post;
use App\Services\ContentCache;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(protected SlugService $slugs)
    {
    }

    /**
     * Display a listing of the categories.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Category::class);

        $query = Category::query()
            ->withCount(['posts' => fn ($q) => $q->published()->public()])
            ->search($request->query('search'));

        $trashed = $request->boolean('trashed');

        if ($trashed) {
            $query->onlyTrashed();
        }

        $categories = $query->orderBy('name')->paginate(config('blog.pagination.admin_categories'))->withQueryString();

        return view('admin.categories.index', [
            'categories'    => $categories,
            'statuses'      => CategoryStatus::options(),
            'trashed'       => $trashed,
            'search'        => $request->query('search'),
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        $this->authorize('create', Category::class);

        return view('admin.categories.create', [
            'statuses' => CategoryStatus::options(),
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->authorize('create', Category::class);

        $data = $request->validated();

        $data['slug'] = $this->slugs->unique($data['name'], Category::class, null, $data['slug'] ?? null);

        $category = Category::create($data);

        if ($request->hasFile('image')) {
            $category->update([
                'image' => $request->file('image')->store(config('blog.images.thumbnail_dir'), 'public'),
            ]);
        }

        ContentCache::flush();

        return Redirect::route('admin.categories.edit', $category)
            ->with('status', 'Category created.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        $this->authorize('update', $category);

        return view('admin.categories.edit', [
            'category' => $category,
            'statuses' => CategoryStatus::options(),
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $data = $request->validated();

        $data['slug'] = $this->slugs->unique($data['name'], Category::class, $category->id, $data['slug'] ?? null);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $data['image'] = $request->file('image')->store(config('blog.images.thumbnail_dir'), 'public');
        }

        $category->update($data);

        ContentCache::flush();

        return Redirect::route('admin.categories.edit', $category)
            ->with('status', 'Category updated.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        if ($category->posts()->exists()) {
            return Redirect::route('admin.categories.index')
                ->with('error', 'This category still has posts and cannot be deleted.');
        }

        $category->delete();

        ContentCache::flush();

        return Redirect::route('admin.categories.index')
            ->with('status', 'Category moved to trash.');
    }

    /**
     * Restore the specified trashed category.
     */
    public function restore(Category $category): RedirectResponse
    {
        $this->authorize('restore', $category);

        $category->restore();

        ContentCache::flush();

        return Redirect::route('admin.categories.index')
            ->with('status', 'Category restored.');
    }
}
