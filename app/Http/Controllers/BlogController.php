<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Display the list of published posts.
     */
    public function index(Request $request): View
    {
        $posts = Post::query()
            ->published()
            ->public()
            ->with(['author.role', 'category', 'tags'])
            ->search($request->query('q'))
            ->ofCategory($request->query('category'))
            ->ofTag($request->query('tag'))
            ->ofAuthor($request->query('author'))
            ->sorted($request->query('sort', 'latest'))
            ->paginate(config('blog.pagination.public_posts'))
            ->withQueryString();

        $categories = $this->remember('blog.sidebar.categories', fn () => Category::query()->active()->orderBy('name')->get());

        $tags = $this->remember('blog.sidebar.tags', fn () => Tag::query()
            ->withCount(['posts' => fn (Builder $q) => $q->published()->public()])
            ->orderBy('name')
            ->get());

        $authors = $this->remember('blog.sidebar.authors', fn () => User::query()
            ->whereHas('roles', fn (Builder $q) => $q->where('slug', 'author'))
            ->withCount(['publishedPosts'])
            ->orderBy('name')
            ->get());

        return view('posts.index', [
            'posts' => $posts,
            'search' => trim((string) $request->query('q')),
            'filters' => [
                'category' => $request->query('category'),
                'tag' => $request->query('tag'),
                'author' => $request->query('author'),
                'sort' => $request->query('sort', 'latest'),
            ],
            'categories' => $categories,
            'tags' => $tags,
            'authors' => $authors,
        ]);
    }

    /**
     * Display a single published post.
     */
    public function show(Request $request, string $slug): View
    {
        $post = Post::query()
            ->published()
            ->public()
            ->with(['author.role', 'category', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        $post->increment('view_count');

        $toc = $this->extractHeadings($post->content);

        $related = Post::query()
            ->published()
            ->public()
            ->with(['author', 'category'])
            ->whereKeyNot($post->id)
            ->where(function (Builder $query) use ($post): void {
                $query->where('category_id', $post->category_id)
                    ->orWhereHas('tags', fn (Builder $q) => $q->whereIn('tags.id', $post->tags->pluck('id')));
            })
            ->latest('published_at')
            ->limit(3)
            ->get();

        $previous = Post::query()
            ->published()
            ->public()
            ->with(['author'])
            ->where('published_at', '<', $post->published_at)
            ->latest('published_at')
            ->first();

        $next = Post::query()
            ->published()
            ->public()
            ->with(['author'])
            ->where('published_at', '>', $post->published_at)
            ->oldest('published_at')
            ->first();

        $authUser = $request->user();

        $comments = $post->comments()
            ->approved()
            ->with([
                'user',
                'post',
                'likes' => fn ($query) => $query->where('user_id', $authUser?->id ?? 0),
                'replies.user',
                'replies.likes' => fn ($query) => $query->where('user_id', $authUser?->id ?? 0),
            ])
            ->whereNull('parent_id')
            ->latest()
            ->get();

        $liked = $authUser?->postLikes()->where('post_id', $post->id)->exists() ?? false;
        $bookmarked = $authUser?->bookmarks()->where('post_id', $post->id)->exists() ?? false;

        return view('posts.show', [
            'post' => $post,
            'article' => $this->addHeadingAnchors($post->content, $toc),
            'toc' => $toc,
            'related' => $related,
            'previous' => $previous,
            'next' => $next,
            'comments' => $comments,
            'commentCount' => $post->comment_count,
            'liked' => $liked,
            'bookmarked' => $bookmarked,
        ]);
    }

    /**
     * Extract the h2 and h3 headings from the post content.
     *
     * @return array<int, array{level: int, text: string, id: string}>
     */
    protected function extractHeadings(?string $content): array
    {
        if (blank($content)) {
            return [];
        }

        preg_match_all('/<h([23])(?:\s[^>]*)?>(.*?)<\/h\1>/is', $content, $matches, PREG_SET_ORDER);

        return Collection::make($matches)
            ->map(function (array $match, int $index): ?array {
                $text = trim(strip_tags($match[2]));

                if ($text === '') {
                    return null;
                }

                return [
                    'level' => (int) $match[1],
                    'text' => $text,
                    'id' => 'heading-'.($index + 1),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Inject anchor ids into the content headings for the table of contents.
     *
     * @param  array<int, array{level: int, text: string, id: string}>  $toc
     */
    protected function addHeadingAnchors(?string $content, array $toc): string
    {
        if (blank($content) || $toc === []) {
            return (string) $content;
        }

        $ids = collect($toc)->pluck('id');
        $index = 0;

        return (string) preg_replace_callback(
            '/<h([23])(\s[^>]*)?>(.*?)<\/h\1>/is',
            function (array $match) use ($ids, &$index): string {
                $id = $ids[$index] ?? 'heading-'.($index + 1);
                $index++;

                $attributes = trim($match[2] ?? '');

                return '<h'.$match[1].' id="'.$id.'" '.$attributes.'>'.$match[3].'</h'.$match[1].'>';
            },
            $content
        );
    }
}
