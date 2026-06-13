<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadAccessTest extends TestCase
{
    use RefreshDatabase;

    private string $zipPath;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        // Create a fake ZIP file on the private disk
        $this->zipPath = Config::get('products.ebook_zip_path');
        Storage::disk('local')->put($this->zipPath, 'fake-zip-content');
    }

    public function test_download_returns_403_for_pending_order(): void
    {
        $order = Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Pending',
            'email' => 'pending@example.com',
            'whatsapp' => '08111111111',
            'amount' => 99000,
            'status' => 'pending',
        ]);

        $this->get(route('orders.download', $order->invoice_token))
            ->assertStatus(403);
    }

    public function test_download_returns_403_for_proof_submitted_order(): void
    {
        $order = Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Proof Submitted',
            'email' => 'proof@example.com',
            'whatsapp' => '08222222222',
            'amount' => 99000,
            'status' => 'proof_submitted',
        ]);

        $this->get(route('orders.download', $order->invoice_token))
            ->assertStatus(403);
    }

    public function test_download_returns_403_for_rejected_order(): void
    {
        $order = Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Rejected',
            'email' => 'rejected@example.com',
            'whatsapp' => '08333333333',
            'amount' => 99000,
            'status' => 'rejected',
            'reject_reason' => 'Test rejection',
        ]);

        $this->get(route('orders.download', $order->invoice_token))
            ->assertStatus(403);
    }

    public function test_download_returns_200_for_verified_order(): void
    {
        $order = Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Verified',
            'email' => 'verified@example.com',
            'whatsapp' => '08444444444',
            'amount' => 99000,
            'status' => 'verified',
        ]);

        $response = $this->get(route('orders.download', $order->invoice_token));

        // Should succeed
        $response->assertStatus(200);

        // Should have correct download content disposition
        $response->assertHeader('Content-Disposition');
        $disposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('Ultimate-ChatGPT-Mastery-Bundle.zip', $disposition);
    }

    public function test_download_returns_404_for_invalid_token(): void
    {
        $this->get(route('orders.download', 'nonexistent-token-12345'))
            ->assertStatus(404);
    }

    public function test_download_works_repeatedly_for_verified_order(): void
    {
        $order = Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Repeat',
            'email' => 'repeat@example.com',
            'whatsapp' => '08555555555',
            'amount' => 99000,
            'status' => 'verified',
        ]);

        // First download
        $this->get(route('orders.download', $order->invoice_token))
            ->assertStatus(200);

        // Second download (same link, no expiry)
        $this->get(route('orders.download', $order->invoice_token))
            ->assertStatus(200);
    }

    public function test_download_does_not_expose_storage_path(): void
    {
        $order = Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Hidden',
            'email' => 'hidden@example.com',
            'whatsapp' => '08666666666',
            'amount' => 99000,
            'status' => 'pending',
        ]);

        $response = $this->get(route('orders.download', $order->invoice_token));
        $response->assertStatus(403);

        // Response must not contain the actual storage path
        $content = $response->getContent();
        $this->assertStringNotContainsString('storage/app', $content ?? '');
        $this->assertStringNotContainsString('Ultimate-ChatGPT-Mastery-Bundle', $content ?? '');
        $this->assertStringNotContainsString(storage_path(), $content ?? '');
    }
}
