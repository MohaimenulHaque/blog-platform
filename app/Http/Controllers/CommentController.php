<?php

namespace App\Http\Controllers;

use App\Enums\CommentStatus;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Notifications\NewCommentNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a newly created comment.
     */
    public function store(StoreCommentRequest $request, Post $post): JsonResponse
    {
        $autoApprove = (bool) config('blog.comments.auto_approve', false);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $request->validated('parent_id'),
            'body' => $request->validated('body'),
            'status' => $autoApprove ? CommentStatus::Approved->value : CommentStatus::Pending->value,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($comment->isApproved()) {
            $post->syncCommentCount();
        }

        if ($post->author_id !== $request->user()->id) {
            $post->author->notify(new NewCommentNotification($comment));
        }

        return response()->json([
            'status' => $comment->isApproved() ? 'approved' : 'pending',
            'message' => $comment->isApproved()
                ? 'Comment posted successfully.'
                : 'Comment submitted and awaiting approval.',
            'html' => $comment->isApproved()
                ? view('comments.partials.comment', [
                    'comment' => $comment,
                    'replyable' => $comment->parent_id === null,
                    'user' => $request->user(),
                ])->render()
                : null,
        ]);
    }

    /**
     * Update the specified comment.
     */
    public function update(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        $comment->forceFill([
            'body' => $request->validated('body'),
            'status' => $comment->isApproved() ? CommentStatus::Pending->value : $comment->status,
        ])->save();

        if (! $comment->isApproved()) {
            $comment->post->syncCommentCount();
        }

        return response()->json([
            'status' => $comment->status,
            'message' => 'Comment updated and returned to pending review.',
        ]);
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $wasApproved = $comment->isApproved();

        $comment->delete();

        if ($wasApproved) {
            $comment->post->syncCommentCount();
        }

        return response()->json([
            'message' => 'Comment deleted.',
        ]);
    }
}
