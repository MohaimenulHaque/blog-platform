<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostLikeController extends Controller
{
    /**
     * Toggle the authenticated user's like on the post.
     */
    public function toggle(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();

        $wasLiked = DB::transaction(function () use ($user, $post): bool {
            $like = $post->likes()->where('user_id', $user->id)->first();

            if ($like) {
                $like->delete();
                $post->decrement('like_count');

                return false;
            }

            $post->likes()->create(['user_id' => $user->id]);
            $post->increment('like_count');

            return true;
        });

        return response()->json([
            'liked' => $wasLiked,
            'count' => $post->fresh()->like_count,
        ]);
    }
}
