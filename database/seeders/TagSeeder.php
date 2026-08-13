<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * The tags to seed.
     *
     * @var array<int, string>
     */
    public const TAGS = [
        'Laravel',
        'PHP',
        'JavaScript',
        'TypeScript',
        'Artificial Intelligence',
        'Machine Learning',
        'Web Development',
        'Open Source',
        'Data Science',
        'Cloud Computing',
        'Remote Work',
        'Career Growth',
        'UX Research',
        'CSS',
        'Databases',
        'DevOps',
        'Security',
        'Writing',
        'Startups',
        'Focus',
    ];

    /**
     * Seed the application's tags.
     */
    public function run(): void
    {
        foreach (self::TAGS as $tag) {
            Tag::firstOrCreate(['slug' => \Str::slug($tag)], ['name' => $tag]);
        }
    }
}
