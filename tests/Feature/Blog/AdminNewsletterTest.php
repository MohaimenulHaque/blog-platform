<?php

namespace Tests\Feature\Blog;

use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_can_access_the_subscriber_list(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.newsletter.index'))
            ->assertOk();
    }

    public function test_editors_cannot_access_the_subscriber_list(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.newsletter.index'))
            ->assertForbidden();
    }

    public function test_admins_can_delete_a_subscriber(): void
    {
        $subscriber = NewsletterSubscriber::factory()->create(['email' => 'reader@example.com']);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('admin.newsletter.destroy', $subscriber))
            ->assertRedirect()
            ->assertSessionHas('status', 'Subscriber removed.');

        $this->assertDatabaseMissing('newsletter_subscribers', ['email' => 'reader@example.com']);
    }

    public function test_admins_can_export_subscribers_as_csv(): void
    {
        NewsletterSubscriber::factory()->create(['email' => 'reader@example.com', 'name' => 'Reader One']);
        NewsletterSubscriber::factory()->create(['email' => 'reader2@example.com', 'name' => null]);
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.newsletter.export'))
            ->assertOk();

        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('reader@example.com', $response->streamedContent());
        $this->assertStringContainsString('reader2@example.com', $response->streamedContent());
    }

    public function test_subscriber_list_supports_searching(): void
    {
        NewsletterSubscriber::factory()->create(['email' => 'unique-reader@example.com']);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.newsletter.index', ['q' => 'unique-reader']))
            ->assertOk()
            ->assertSee('unique-reader@example.com');
    }

    public function test_subscriber_list_supports_status_filtering(): void
    {
        NewsletterSubscriber::factory()->create(['email' => 'keeper@example.com']);
        NewsletterSubscriber::factory()->unsubscribed()->create(['email' => 'leaver@example.com']);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.newsletter.index', ['status' => 'unsubscribed']))
            ->assertOk()
            ->assertSee('leaver@example.com')
            ->assertDontSee('keeper@example.com');
    }
}
