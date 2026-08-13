<?php

namespace App\Policies;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user can view any posts in the admin area.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('author-content');
    }

    /**
     * Determine whether the user can manage content across all authors.
     */
    public function manageContent(User $user): bool
    {
        return $this->isManager($user);
    }

    /**
     * Determine whether the user can view the post in the admin area.
     */
    public function view(User $user, Post $post): bool
    {
        return $this->isManager($user) || $post->author_id === $user->id;
    }

    /**
     * Determine whether the user can create posts.
     */
    public function create(User $user): bool
    {
        return $user->can('author-content');
    }

    /**
     * Determine whether the user can update the post.
     */
    public function update(User $user, Post $post): bool
    {
        if ($this->isManager($user)) {
            return true;
        }

        return $post->author_id === $user->id
            && in_array($post->status, [PostStatus::Draft->value, PostStatus::Pending->value], true);
    }

    /**
     * Determine whether the user can delete the post.
     */
    public function delete(User $user, Post $post): bool
    {
        if ($this->isManager($user)) {
            return true;
        }

        return $post->author_id === $user->id
            && in_array($post->status, [PostStatus::Draft->value, PostStatus::Pending->value], true);
    }

    /**
     * Determine whether the user can restore the post.
     */
    public function restore(User $user, Post $post): bool
    {
        if ($this->isManager($user)) {
            return true;
        }

        return $post->author_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the post.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return $this->isManager($user);
    }

    /**
     * Determine whether the user can publish the post.
     */
    public function publish(User $user): bool
    {
        return $this->isManager($user);
    }

    /**
     * Determine whether the user can unpublish the post.
     */
    public function unpublish(User $user, Post $post): bool
    {
        return $this->isManager($user);
    }

    /**
     * Determine whether the user can schedule the post.
     */
    public function schedule(User $user, Post $post): bool
    {
        return $this->isManager($user);
    }

    /**
     * Determine whether the user can archive the post.
     */
    public function archive(User $user, Post $post): bool
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
