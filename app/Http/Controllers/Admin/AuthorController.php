<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AuthorController extends Controller
{
    /**
     * The social platforms available on an author profile.
     *
     * @var list<string>
     */
    protected array $socialPlatforms = ['twitter', 'facebook', 'instagram', 'linkedin', 'github', 'youtube'];

    /**
     * Display a listing of the authors.
     */
    public function index(Request $request): View
    {
        $this->authorize('access-admin');

        $query = User::query()
            ->whereHas('roles', fn (Builder $q) => $q->where('slug', 'author'))
            ->with(['role'])
            ->withCount(['posts', 'publishedPosts']);

        if ($request->filled('search')) {
            $term = mb_strtolower(trim($request->query('search')));
            $query->where(function (Builder $q) use ($term): void {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(username) LIKE ?', ["%{$term}%"]);
            });
        }

        $authors = $query->orderBy('name')->paginate(config('blog.pagination.admin_authors'))->withQueryString();

        return view('admin.authors.index', [
            'authors' => $authors,
            'search' => $request->query('search'),
        ]);
    }

    /**
     * Show the form for creating a new author.
     */
    public function create(): View
    {
        $this->authorize('access-admin');

        return view('admin.authors.create', [
            'socialPlatforms' => $this->socialPlatforms,
        ]);
    }

    /**
     * Store a newly created author.
     */
    public function store(StoreAuthorRequest $request): RedirectResponse
    {
        $this->authorize('access-admin');

        $user = User::create([
            'name' => $request->validated('name'),
            'username' => $request->validated('username'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'designation' => $request->validated('designation'),
            'bio' => $request->validated('bio'),
            'website' => $request->validated('website'),
            'social_links' => $this->socialLinks($request),
            'email_verified_at' => now(),
        ]);

        if ($request->hasFile('avatar')) {
            $user->update([
                'avatar' => $request->file('avatar')->store('avatars', 'public'),
            ]);
        }

        $user->assignRole('author');

        return Redirect::route('admin.authors.edit', $user)
            ->with('status', 'Author created.');
    }

    /**
     * Show the form for editing the specified author.
     */
    public function edit(User $user): View
    {
        $this->authorize('access-admin');

        abort_unless($user->isAuthor(), 404);

        return view('admin.authors.edit', [
            'author' => $user,
            'socialPlatforms' => $this->socialPlatforms,
        ]);
    }

    /**
     * Update the specified author.
     */
    public function update(UpdateAuthorRequest $request, User $user): RedirectResponse
    {
        $this->authorize('access-admin');

        abort_unless($user->isAuthor(), 404);

        $user->update([
            'name' => $request->validated('name'),
            'username' => $request->validated('username'),
            'email' => $request->validated('email'),
            'designation' => $request->validated('designation'),
            'bio' => $request->validated('bio'),
            'website' => $request->validated('website'),
            'social_links' => $this->socialLinks($request),
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => $request->validated('password')]);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->update([
                'avatar' => $request->file('avatar')->store('avatars', 'public'),
            ]);
        }

        return Redirect::route('admin.authors.edit', $user)
            ->with('status', 'Author updated.');
    }

    /**
     * Remove the specified author role.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('access-admin');

        abort_unless($user->isAuthor(), 404);

        $user->removeRole('author');

        return Redirect::route('admin.authors.index')
            ->with('status', 'Author role removed.');
    }

    /**
     * Build the social links array from the request.
     *
     * @return array<string, string>
     */
    protected function socialLinks(Request $request): array
    {
        $links = [];

        foreach ($this->socialPlatforms as $platform) {
            $value = $request->input("social_links.{$platform}");

            if (filled($value)) {
                $links[$platform] = $value;
            }
        }

        return $links;
    }
}
