<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;

class TagPolicy
{
    /**
     * Determine whether the user can manage tags.
     */
    public function manage(User $user): bool
    {
        return $user->can('manage-content');
    }

    /**
     * Determine whether the user can view tags.
     */
    public function viewAny(User $user): bool
    {
        return $this->manage($user);
    }

    /**
     * Determine whether the user can create tags.
     */
    public function create(User $user): bool
    {
        return $this->manage($user);
    }

    /**
     * Determine whether the user can update the tag.
     */
    public function update(User $user, Tag $tag): bool
    {
        return $this->manage($user);
    }

    /**
     * Determine whether the user can delete the tag.
     */
    public function delete(User $user, Tag $tag): bool
    {
        return $this->manage($user);
    }

    /**
     * Determine whether the user can restore the tag.
     */
    public function restore(User $user, Tag $tag): bool
    {
        return $this->manage($user);
    }
}
