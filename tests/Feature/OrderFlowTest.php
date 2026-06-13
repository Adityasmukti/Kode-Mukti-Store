<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_order_and_redirects_to_tokenized_url(): void
    {
        $response = $this->post(route('orders.store'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'whatsapp' => '08123456789',
        ]);

        $response->assertRedirect();
        $redirectUrl = $response->headers->get('Location');
        $this->assertStringContainsString('/orders/', $redirectUrl);

        // Extract invoice_token from redirect URL
        preg_match('/\/orders\/([a-zA-Z0-9]+)/', $redirectUrl, $matches);
        $this->assertNotEmpty($matches[1], 'Redirect URL must contain a non-empty invoice token');

        // Assert order exists in database
        $this->assertDatabaseHas('orders', [
            'invoice_token' => $matches[1],
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'whatsapp' => '08123456789',
            'amount' => 99000,
            'status' => 'pending',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        // Empty submission
        $response = $this->post(route('orders.store'), []);
        $response->assertSessionHasErrors(['name', 'email', 'whatsapp']);

        // Missing email
        $response = $this->post(route('orders.store'), [
            'name' => 'Budi',
            'whatsapp' => '08123456789',
        ]);
        $response->assertSessionHasErrors(['email']);

        // Invalid email
        $response = $this->post(route('orders.store'), [
            'name' => 'Budi',
            'email' => 'not-an-email',
            'whatsapp' => '08123456789',
        ]);
        $response->assertSessionHasErrors(['email']);
    }

    public function test_show_pending_returns_expected_content(): void
    {
        // Create an order directly
        $order = \App\Models\Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Siti Rahmawati',
            'email' => 'siti@example.com',
            'whatsapp' => '08987654321',
            'amount' => 99000,
            'status' => 'pending',
        ]);

        $response = $this->get(route('orders.show', $order->invoice_token));
        $response->assertStatus(200);

        // Check pending status badge
        $response->assertSee('Menunggu Pembayaran');
        // Check amount
        $response->assertSee('Rp 99.000');
        // Check QRIS image area
        $response->assertSee('/images/qris.png');
        // Check payment steps
        $response->assertSee('scan QRIS', false);
        $response->assertSee('bayar sesuai nominal', false);
        // Check upload form helper text
        $response->assertSee('Format: JPG, PNG, atau WebP. Maksimal 4 MB.');
    }

    public function test_show_returns_404_for_invalid_token(): void
    {
        $response = $this->get(route('orders.show', 'nonexistent-token-12345'));
        $response->assertStatus(404);
    }

    public function test_show_never_exposes_numeric_order_id(): void
    {
        $response = $this->get('/orders/1');
        $response->assertStatus(404);
    }
}
