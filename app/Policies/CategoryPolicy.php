<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine whether the user can manage categories.
     */
    public function manage(User $user): bool
    {
        return $user->can('manage-content');
    }

    /**
     * Determine whether the user can view categories.
     */
    public function viewAny(User $user): bool
    {
        return $this->manage($user);
    }

    /**
     * Determine whether the user can create categories.
     */
    public function create(User $user): bool
    {
        return $this->manage($user);
    }

    /**
     * Determine whether the user can update the category.
     */
    public function update(User $user, Category $category): bool
    {
        return $this->manage($user);
    }

    /**
     * Determine whether the user can delete the category.
     */
    public function delete(User $user, Category $category): bool
    {
        return $this->manage($user);
    }

    /**
     * Determine whether the user can restore the category.
     */
    public function restore(User $user, Category $category): bool
    {
        return $this->manage($user);
    }
}
