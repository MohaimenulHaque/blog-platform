<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine whether the user can create comments.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the comment.
     */
    public function update(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the comment.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id || $this->isManager($user);
    }

    /**
     * Determine whether the user can restore the comment.
     */
    public function restore(User $user, Comment $comment): bool
    {
        return $this->isManager($user);
    }

    /**
     * Determine whether the user can permanently delete the comment.
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        return $this->isManager($user);
    }

    /**
     * Determine whether the user can moderate comments.
     */
    public function moderate(User $user): bool
    {
        return $this->isManager($user);
    }

    /**
     * Determine whether the user is a content manager.
     */
    protected function isManager(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'editor']);
    }
}
