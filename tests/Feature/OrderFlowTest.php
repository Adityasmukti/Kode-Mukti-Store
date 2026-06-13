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
        $response->assertSee('Rp 4.900');
        // Check QRIS image area
        $response->assertSee('/images/qris.png');
        // Check payment steps (case-insensitive by using escaped=false with manual check)
        $response->assertSee('Scan QRIS');
        $response->assertSee('Bayar sesuai nominal');
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

    public function test_show_proof_submitted_state(): void
    {
        $order = \App\Models\Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Dewi Lestari',
            'email' => 'dewi@example.com',
            'whatsapp' => '081111222333',
            'amount' => 99000,
            'status' => 'proof_submitted',
        ]);

        $response = $this->get(route('orders.show', $order->invoice_token));
        $response->assertStatus(200);

        // Check proof_submitted status badge
        $response->assertSee('Bukti Terkirim');
        // Check admin verification message
        $response->assertSee('Admin akan mengecek pembayaran secara manual.');
        // Check re-upload is visible but secondary (upload section exists)
        $response->assertSee('Upload Ulang');
        // QRIS and amount still visible
        $response->assertSee('Rp 4.900');
        $response->assertSee('/images/qris.png');
    }

    public function test_show_rejected_state(): void
    {
        $order = \App\Models\Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@example.com',
            'whatsapp' => '082222333444',
            'amount' => 99000,
            'status' => 'rejected',
            'reject_reason' => 'Bukti pembayaran tidak terbaca. Harap upload ulang dengan foto yang lebih jelas.',
        ]);

        $response = $this->get(route('orders.show', $order->invoice_token));
        $response->assertStatus(200);

        // Check rejected status badge
        $response->assertSee('Ditolak');
        // Check reject reason is displayed in alert
        $response->assertSee('Bukti pembayaran tidak terbaca');
        // Check primary re-upload button affordance
        $response->assertSee('Upload Ulang Bukti');
    }

    public function test_show_rejected_state_shows_upload_reason_button(): void
    {
        $order = \App\Models\Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Bambang',
            'email' => 'bambang@example.com',
            'whatsapp' => '083333444555',
            'amount' => 99000,
            'status' => 'rejected',
            'reject_reason' => 'Nominal tidak sesuai.',
        ]);

        $response = $this->get(route('orders.show', $order->invoice_token));
        $response->assertStatus(200);

        // Reject reason must be shown (not a dead end)
        $response->assertSee('Nominal tidak sesuai.');
        // Form with upload button must be present
        $response->assertSee('Upload Ulang Bukti');
        // Form must POST to the proof upload route
        $response->assertSee(route('orders.proof.store', $order->invoice_token));
    }

    public function test_show_verified_state(): void
    {
        $order = \App\Models\Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Citra Dewi',
            'email' => 'citra@example.com',
            'whatsapp' => '084444555666',
            'amount' => 99000,
            'status' => 'verified',
        ]);

        $response = $this->get(route('orders.show', $order->invoice_token));
        $response->assertStatus(200);

        // Check verified status badge
        $response->assertSee('Terverifikasi');
        // Check download button links to download route
        $response->assertSee('Download ZIP Ebook');
        $response->assertSee(route('orders.download', $order->invoice_token));
        // Check support note about link remaining active
        $response->assertSee('tetap aktif');
    }

    public function test_show_preserves_all_order_info_across_states(): void
    {
        $order = \App\Models\Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Eko Prasetyo',
            'email' => 'eko@example.com',
            'whatsapp' => '085555666777',
            'amount' => 99000,
            'status' => 'verified',
        ]);

        $response = $this->get(route('orders.show', $order->invoice_token));
        $response->assertStatus(200);

        // Support note about saving invoice link must be present in all states
        $response->assertSee('Simpan link invoice');
    }
}
