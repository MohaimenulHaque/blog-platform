<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CommentStatus;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Notifications\CommentApprovedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CommentController extends Controller
{
    /**
     * Display a listing of comments for moderation.
     */
    public function index(Request $request): View
    {
        $this->authorize('moderate', Comment::class);

        $comments = Comment::query()
            ->with(['user', 'post'])
            ->search($request->query('q'))
            ->when(
                $request->query('status'),
                fn ($query, $status) => $query->where('status', $status)
            )
            ->latest()
            ->paginate(config('blog.pagination.admin_comments', 15))
            ->withQueryString();

        return view('admin.comments.index', [
            'comments' => $comments,
            'search' => trim((string) $request->query('q')),
            'filter' => $request->query('status', ''),
            'statuses' => CommentStatus::options(),
        ]);
    }

    /**
     * Display a listing of trashed comments.
     */
    public function trashed(Request $request): View
    {
        $this->authorize('moderate', Comment::class);

        $comments = Comment::query()
            ->onlyTrashed()
            ->with(['user', 'post'])
            ->search($request->query('q'))
            ->latest()
            ->paginate(config('blog.pagination.admin_comments', 15))
            ->withQueryString();

        return view('admin.comments.trashed', [
            'comments' => $comments,
            'search' => trim((string) $request->query('q')),
        ]);
    }

    /**
     * Restore the specified trashed comment.
     */
    public function restore(Comment $comment): RedirectResponse
    {
        $this->authorize('restore', $comment);

        $comment->restore();

        if ($comment->isApproved()) {
            $comment->post->syncCommentCount();
        }

        return Redirect::route('admin.comments.trashed')
            ->with('status', 'Comment restored.');
    }

    /**
     * Permanently delete the specified trashed comment.
     */
    public function forceDestroy(Comment $comment): RedirectResponse
    {
        $this->authorize('forceDelete', $comment);

        $comment->forceDelete();

        return Redirect::route('admin.comments.trashed')
            ->with('status', 'Comment permanently deleted.');
    }

    /**
     * Update the moderation status of the comment.
     */
    public function status(Request $request, Comment $comment): RedirectResponse
    {
        $this->authorize('moderate', $comment);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,approved,rejected,spam'],
        ]);

        $status = CommentStatus::from($validated['status']);

        DB::transaction(function () use ($comment, $status): void {
            $wasApproved = $comment->isApproved();

            $comment->forceFill(['status' => $status->value])->save();

            if ($wasApproved !== $comment->isApproved()) {
                $comment->post->syncCommentCount();
            }

            if ($status === CommentStatus::Approved && $comment->user_id !== null) {
                $comment->user->notify(new CommentApprovedNotification($comment));
            }
        });

        return back()->with('status', 'Comment marked as '.$status->label().'.');
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Request $request, Comment $comment): RedirectResponse
    {
        $this->authorize('moderate', $comment);

        $wasApproved = $comment->isApproved();

        $comment->delete();

        if ($wasApproved) {
            $comment->post->syncCommentCount();
        }

        return back()->with('status', 'Comment deleted.');
    }
}
