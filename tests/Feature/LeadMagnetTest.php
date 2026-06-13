<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadMagnetTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_lead_with_valid_email(): void
    {
        $response = $this->post(route('lead-magnet.store'), [
            'email' => 'user@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('leads', ['email' => 'user@example.com']);
    }

    public function test_store_lead_redirects_to_download_page(): void
    {
        $response = $this->post(route('lead-magnet.store'), [
            'email' => 'user@example.com',
        ]);

        $response->assertRedirect();
        $redirectUrl = $response->headers->get('Location');
        $this->assertStringContainsString('/lead-magnet/', $redirectUrl);
    }

    public function test_duplicate_email_does_not_create_duplicate_row(): void
    {
        $this->post(route('lead-magnet.store'), ['email' => 'user@example.com']);
        $this->post(route('lead-magnet.store'), ['email' => 'user@example.com']);

        $this->assertDatabaseCount('leads', 1);
    }

    public function test_duplicate_email_still_redirects_to_download_page(): void
    {
        $this->post(route('lead-magnet.store'), ['email' => 'user@example.com']);
        $response = $this->post(route('lead-magnet.store'), ['email' => 'user@example.com']);

        $response->assertRedirect();
        $redirectUrl = $response->headers->get('Location');
        $this->assertStringContainsString('/lead-magnet/', $redirectUrl);
    }

    public function test_store_lead_with_invalid_email_returns_validation_error(): void
    {
        $response = $this->post(route('lead-magnet.store'), [
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_store_lead_with_missing_email_returns_validation_error(): void
    {
        $response = $this->post(route('lead-magnet.store'), []);

        $response->assertSessionHasErrors('email');
    }
}
