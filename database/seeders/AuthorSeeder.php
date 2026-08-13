<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AuthorSeeder extends Seeder
{
    /**
     * The authors to seed.
     *
     * @var array<int, array{name: string, username: string, email: string, designation: string, website: string, bio: string, social_links: array<string, string>}>
     */
    public const AUTHORS = [
        [
            'name' => 'Sofia Marchetti',
            'username' => 'sofia',
            'email' => 'sofia@example.com',
            'designation' => 'Senior Technology Writer',
            'website' => 'https://sofiamarchetti.dev',
            'bio' => 'Sofia covers software engineering and developer tooling. She spent eight years as a backend engineer before turning to writing full time.',
            'social_links' => [
                'twitter' => 'https://twitter.com/sofiamarchetti',
                'github' => 'https://github.com/sofiamarchetti',
                'linkedin' => 'https://linkedin.com/in/sofiamarchetti',
            ],
        ],
        [
            'name' => 'James Okafor',
            'username' => 'james',
            'email' => 'james@example.com',
            'designation' => 'Productivity Columnist',
            'website' => null,
            'bio' => 'James writes about deep work, focus and the psychology of getting things done. His morning newsletter goes out to twelve thousand readers.',
            'social_links' => [
                'twitter' => 'https://twitter.com/jamesokafor',
                'linkedin' => 'https://linkedin.com/in/jamesokafor',
            ],
        ],
        [
            'name' => 'Aisha Rahman',
            'username' => 'aisha',
            'email' => 'aisha@example.com',
            'designation' => 'Design & UX Editor',
            'website' => 'https://aisharahman.design',
            'bio' => 'Aisha leads our design coverage, exploring everything from typography to accessibility. Previously a staff designer at a large fintech.',
            'social_links' => [
                'twitter' => 'https://twitter.com/aisharahman',
                'dribbble' => 'https://dribbble.com/aisharahman',
            ],
        ],
        [
            'name' => 'Daniel Chen',
            'username' => 'daniel',
            'email' => 'daniel@example.com',
            'designation' => 'Science Contributor',
            'website' => null,
            'bio' => 'Daniel holds a PhD in computational biology and translates dense research into stories anyone can understand.',
            'social_links' => [
                'twitter' => 'https://twitter.com/danielchen',
                'linkedin' => 'https://linkedin.com/in/danielchen',
            ],
        ],
        [
            'name' => 'Priya Sharma',
            'username' => 'priya',
            'email' => 'priya@example.com',
            'designation' => 'Career Coach & Writer',
            'website' => 'https://priyasharma.careers',
            'bio' => 'Priya helps engineers and designers navigate promotions, negotiation and burnout. Her writing is direct, practical and kind.',
            'social_links' => [
                'twitter' => 'https://twitter.com/priyasharma',
                'linkedin' => 'https://linkedin.com/in/priyasharma',
            ],
        ],
        [
            'name' => 'Marco Rossi',
            'username' => 'marco',
            'email' => 'marco@example.com',
            'designation' => 'Editor-at-Large',
            'website' => null,
            'bio' => 'Marco is the longest serving member of the editorial team, with a soft spot for long-form essays on technology and society.',
            'social_links' => [
                'twitter' => 'https://twitter.com/marcorossi',
            ],
        ],
    ];

    /**
     * Seed the application's author users.
     */
    public function run(): void
    {
        foreach (self::AUTHORS as $author) {
            $user = User::firstOrCreate(
                ['email' => $author['email']],
                [
                    'name' => $author['name'],
                    'username' => $author['username'],
                    'designation' => $author['designation'],
                    'website' => $author['website'],
                    'bio' => $author['bio'],
                    'social_links' => $author['social_links'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ]
            );

            if (! $user->hasRole('author')) {
                $user->assignRole('author');
            }
        }
    }
}
