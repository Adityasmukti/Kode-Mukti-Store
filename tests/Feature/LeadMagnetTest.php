<?php

namespace Tests\Feature;

use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_download_page_shows_heading_and_cta(): void
    {
        $lead = Lead::factory()->create();

        $response = $this->get(route('lead-magnet.show', $lead->download_token));

        $response->assertStatus(200);
        $response->assertSee('Download 50 Prompt Pemasaran Gratis');
        $response->assertSee('Download Lead Magnet');
        $response->assertSee('Lihat Ebook Rp 99.000');
    }

    public function test_download_page_with_invalid_token_returns_404(): void
    {
        $response = $this->get(route('lead-magnet.show', 'invalid-token-123'));

        $response->assertStatus(404);
    }

    public function test_download_file_streams_for_valid_token(): void
    {
        Storage::fake('local');
        $leadMagnetPath = config('products.lead_magnet_path');
        Storage::disk('local')->put($leadMagnetPath, 'fake prompt content');

        $lead = Lead::factory()->create();

        $response = $this->get(route('lead-magnet.download', $lead->download_token));

        $response->assertStatus(200);
        $response->assertDownload('50-prompt-pemasaran-gratis.txt');
    }

    public function test_download_file_with_invalid_token_returns_404(): void
    {
        $response = $this->get(route('lead-magnet.download', 'invalid-token-123'));

        $response->assertStatus(404);
    }
}
