<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PostPublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Post $post)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New post published',
            'message' => $this->post->author->name.' published "'.$this->post->title.'".',
            'url' => $this->post->url,
            'icon' => 'sparkles',
        ];
    }

    /**
     * The users to notify about the published post.
     *
     * @return array<int, User>
     */
    public static function recipients(Post $post): array
    {
        return User::query()
            ->where(fn ($q) => $q
                ->whereHas('role', fn ($role) => $role->whereIn('slug', ['admin', 'editor']))
                ->orWhereHas('roles', fn ($roles) => $roles->whereIn('slug', ['admin', 'editor'])))
            ->whereKeyNot($post->author_id)
            ->get()
            ->all();
    }
}
