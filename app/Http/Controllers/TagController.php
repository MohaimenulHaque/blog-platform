<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * Display the list of tags.
     */
    public function index(): View
    {
        $tags = Tag::query()
            ->withCount(['posts' => fn ($query) => $query->published()->public()])
            ->orderBy('name')
            ->paginate(config('blog.pagination.public_tags'));

        return view('tags.index', [
            'tags' => $tags,
        ]);
    }

    /**
     * Display the posts within a tag.
     */
    public function show(Tag $tag): View
    {
        $posts = Post::query()
            ->published()
            ->public()
            ->with(['author', 'category'])
            ->whereHas('tags', fn ($query) => $query->where('tags.id', $tag->id))
            ->latest('published_at')
            ->paginate(config('blog.pagination.public_posts'))
            ->withQueryString();

        return view('tags.show', [
            'tag' => $tag,
            'posts' => $posts,
        ]);
    }
}
