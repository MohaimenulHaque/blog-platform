<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookmarkController extends Controller
{
    /**
     * Display the authenticated user's bookmarked posts.
     */
    public function index(Request $request): View
    {
        $bookmarks = $request->user()
            ->bookmarks()
            ->with(['post.author', 'post.category'])
            ->latest()
            ->paginate(config('blog.pagination.public_posts', 9));

        return view('bookmarks.index', [
            'bookmarks' => $bookmarks,
        ]);
    }

    /**
     * Toggle the authenticated user's bookmark on the post.
     */
    public function toggle(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();

        $bookmarked = DB::transaction(function () use ($user, $post): bool {
            $bookmark = $post->bookmarks()->where('user_id', $user->id)->first();

            if ($bookmark) {
                $bookmark->delete();

                return false;
            }

            $post->bookmarks()->create(['user_id' => $user->id]);

            return true;
        });

        return response()->json([
            'bookmarked' => $bookmarked,
        ]);
    }
}
