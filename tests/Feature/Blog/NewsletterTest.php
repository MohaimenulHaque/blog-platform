<?php

namespace Tests\Feature\Blog;

use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_visitor_can_subscribe(): void
    {
        $this->postJson(route('newsletter.subscribe'), ['email' => 'reader@example.com'])
            ->assertOk()
            ->assertJson(['message' => 'Thanks for subscribing!']);

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'reader@example.com',
            'subscribed' => true,
        ]);
    }

    public function test_subscription_email_must_be_valid(): void
    {
        $this->post(route('newsletter.subscribe'), ['email' => 'not-an-email'])
            ->assertSessionHasErrors('email');
    }

    public function test_duplicate_subscription_is_rejected(): void
    {
        NewsletterSubscriber::factory()->create(['email' => 'reader@example.com']);

        $this->postJson(route('newsletter.subscribe'), ['email' => 'reader@example.com'])
            ->assertStatus(409)
            ->assertJson(['message' => 'You are already subscribed to the newsletter.']);

        $this->assertDatabaseCount('newsletter_subscribers', 1);
    }

    public function test_an_unsubscribed_email_can_resubscribe(): void
    {
        NewsletterSubscriber::factory()->unsubscribed()->create(['email' => 'reader@example.com']);

        $this->postJson(route('newsletter.subscribe'), ['email' => 'reader@example.com'])
            ->assertOk()
            ->assertJson(['message' => 'Welcome back! You have been resubscribed.']);

        $subscriber = NewsletterSubscriber::firstOrFail();

        $this->assertTrue($subscriber->isSubscribed());
        $this->assertNull($subscriber->unsubscribed_at);
        $this->assertDatabaseCount('newsletter_subscribers', 1);
    }

    public function test_subscription_is_case_insensitive(): void
    {
        $this->postJson(route('newsletter.subscribe'), ['email' => 'Reader@Example.com'])
            ->assertOk();

        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'reader@example.com']);
    }

    public function test_unsubscribe_page_renders_for_a_valid_token(): void
    {
        $subscriber = NewsletterSubscriber::factory()->create(['email' => 'reader@example.com']);

        $this->get(route('newsletter.unsubscribe', $subscriber->token))
            ->assertOk()
            ->assertSee('Unsubscribe from the newsletter?')
            ->assertSee('reader@example.com');
    }

    public function test_unsubscribe_page_404s_for_an_invalid_token(): void
    {
        $this->get(route('newsletter.unsubscribe', 'invalid-token'))
            ->assertNotFound();
    }

    public function test_a_subscriber_can_unsubscribe(): void
    {
        $subscriber = NewsletterSubscriber::factory()->create();

        $this->postJson(route('newsletter.unsubscribe.confirm', $subscriber->token))
            ->assertOk()
            ->assertJson(['message' => 'You have been unsubscribed from the newsletter.']);

        $this->assertFalse($subscriber->fresh()->isSubscribed());
        $this->assertNotNull($subscriber->fresh()->unsubscribed_at);
    }

    public function test_an_unsubscribed_email_sees_the_unsubscribed_page(): void
    {
        $subscriber = NewsletterSubscriber::factory()->unsubscribed()->create();

        $this->get(route('newsletter.unsubscribe', $subscriber->token))
            ->assertOk()
            ->assertSee('You are unsubscribed');
    }
}
