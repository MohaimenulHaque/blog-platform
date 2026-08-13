<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentLikeController extends Controller
{
    /**
     * Toggle the authenticated user's like on the comment.
     */
    public function toggle(Request $request, Comment $comment): JsonResponse
    {
        $user = $request->user();

        $wasLiked = DB::transaction(function () use ($user, $comment): bool {
            $like = $comment->likes()->where('user_id', $user->id)->first();

            if ($like) {
                $like->delete();
                $comment->decrement('likes_count');

                return false;
            }

            $comment->likes()->create(['user_id' => $user->id]);
            $comment->increment('likes_count');

            return true;
        });

        return response()->json([
            'liked' => $wasLiked,
            'count' => $comment->fresh()->likes_count,
        ]);
    }
}
