<?php

namespace Tests\Feature\Blog;

use App\Mail\ContactMessageMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_renders_the_form(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Get in touch')
            ->assertSee('Send a message');
    }

    public function test_a_visitor_can_submit_the_contact_form(): void
    {
        Mail::fake();

        $this->post(route('contact.submit'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'subject' => 'Story pitch',
            'message' => 'I would like to write a story about solar power.',
        ])->assertRedirect()
            ->assertSessionHas('status', 'Thanks for reaching out! We will get back to you soon.');

        Mail::assertQueued(ContactMessageMail::class, function (ContactMessageMail $mail): bool {
            $to = $mail->envelope()->to;

            return $mail->subject === 'Story pitch'
                && count($to) === 1
                && $to[0]->address === (config('blog.contact_recipient') ?: config('mail.from.address'));
        });
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $this->post(route('contact.submit'), [])
            ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }

    public function test_contact_form_rejects_invalid_email(): void
    {
        $this->post(route('contact.submit'), [
            'name' => 'Jane Doe',
            'email' => 'not-an-email',
            'subject' => 'Hello',
            'message' => 'This is a valid long message body.',
        ])->assertSessionHasErrors('email');
    }

    public function test_contact_form_rejects_a_too_short_message(): void
    {
        $this->post(route('contact.submit'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'subject' => 'Hello',
            'message' => 'Short',
        ])->assertSessionHasErrors('message');
    }
}
