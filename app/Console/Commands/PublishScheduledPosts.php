<?php

namespace App\Console\Commands;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all posts whose scheduled publish time has arrived.';

    /**
     * Execute the console command.
     */
    public function handle(PostService $posts): int
    {
        $due = Post::query()
            ->where('status', PostStatus::Scheduled)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get();

        $count = 0;

        foreach ($due as $post) {
            $posts->publish($post);
            $count++;
        }

        $this->info("Published {$count} scheduled post(s).");

        return self::SUCCESS;
    }
}
