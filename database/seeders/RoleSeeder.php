<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * The default roles to seed.
     *
     * @var array<int, array{slug: string, name: string, description: string}>
     */
    public const ROLES = [
        ['slug' => 'admin', 'name' => 'Admin', 'description' => 'Full access to the platform, settings and all content.'],
        ['slug' => 'editor', 'name' => 'Editor', 'description' => 'Manages and moderates blog content.'],
        ['slug' => 'author', 'name' => 'Author', 'description' => 'Creates and manages their own posts.'],
        ['slug' => 'user', 'name' => 'User', 'description' => 'Reads posts, comments, likes and bookmarks.'],
    ];

    /**
     * Seed the roles table.
     */
    public function run(): void
    {
        foreach (self::ROLES as $role) {
            Role::firstOrCreate(
                ['slug' => $role['slug']],
                ['name' => $role['name'], 'description' => $role['description']]
            );
        }
    }
}
