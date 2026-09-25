<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    public function test_sends_contact_message_email(): void
    {
        Mail::fake();

        config()->set('services.support.email', 'support@wildmongolia.com');

        $response = $this->post(route('contact.send'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Booking #12345',
            'message' => 'Need help with my itinerary.',
        ]);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHas('success');

        Mail::assertSent(ContactMessageMail::class, function (ContactMessageMail $mail) {
            return $mail->payload['email'] === 'john@example.com'
                && $mail->payload['subject'] === 'Booking #12345';
        });
    }

    public function test_validates_required_contact_fields(): void
    {
        Mail::fake();

        $response = $this->post(route('contact.send'), []);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);

        Mail::assertNothingSent();
    }
}
