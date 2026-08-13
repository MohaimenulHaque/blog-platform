<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * The categories to seed.
     *
     * @var array<int, array{name: string, description: string}>
     */
    public const CATEGORIES = [
        ['name' => 'Technology', 'description' => 'Software, hardware and the innovations shaping the digital world.'],
        ['name' => 'Productivity', 'description' => 'Systems, habits and tools for doing your best work with less effort.'],
        ['name' => 'Design', 'description' => 'Interfaces, aesthetics and the craft behind great user experiences.'],
        ['name' => 'Career', 'description' => 'Growth, negotiation and navigating a meaningful professional life.'],
        ['name' => 'Science', 'description' => 'Discoveries and ideas from the frontier of scientific research.'],
        ['name' => 'Wellbeing', 'description' => 'Mental health, focus and sustainable ways to live and work.'],
    ];

    /**
     * Seed the application's categories.
     */
    public function run(): void
    {
        foreach (self::CATEGORIES as $category) {
            Category::firstOrCreate(
                ['slug' => \Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'status' => \App\Enums\CategoryStatus::Active,
                ]
            );
        }
    }
}
