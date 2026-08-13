<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'username' => 'admin', 'password' => 'password']
        );
        $admin->assignRole('admin');

        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'username' => 'testuser', 'password' => 'password']
        );
        $user->assignRole('user');

        $this->call([
            CategorySeeder::class,
            TagSeeder::class,
            AuthorSeeder::class,
            PostSeeder::class,
        ]);
    }
}
