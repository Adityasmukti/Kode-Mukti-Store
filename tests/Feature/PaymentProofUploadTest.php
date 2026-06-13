<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentProofUploadTest extends TestCase
{
    use RefreshDatabase;

    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->order = Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Test User',
            'email' => 'test@example.com',
            'whatsapp' => '08123456789',
            'amount' => 99000,
            'status' => 'pending',
        ]);
    }

    public function test_upload_valid_jpg_proof_succeeds(): void
    {
        $fixture = base_path('tests/Fixtures/proofs/sample-proof.jpg');
        $tempPath = sys_get_temp_dir() . '/proof.jpg';
        copy($fixture, $tempPath);
        $file = new UploadedFile($tempPath, 'proof.jpg', 'image/jpeg', null, true);

        $response = $this->post(
            route('orders.proof.store', $this->order->invoice_token),
            ['proof' => $file]
        );

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // Assert a payment_proofs record was created
        $this->assertDatabaseHas('payment_proofs', [
            'order_id' => $this->order->id,
            'mime' => 'image/jpeg',
            'status' => 'submitted',
        ]);

        // Assert order status changed to proof_submitted
        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'status' => 'proof_submitted',
        ]);

        // Assert file was stored on local private disk
        $proof = $this->order->paymentProofs()->first();
        $this->assertNotNull($proof);
        Storage::disk('local')->assertExists($proof->path);
        $this->assertStringStartsWith('payment-proofs/', $proof->path);
        $this->assertEquals('local', $proof->disk);
    }

    public function test_invalid_file_type_is_rejected(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->post(
            route('orders.proof.store', $this->order->invoice_token),
            ['proof' => $file]
        );

        $response->assertSessionHasErrors(['proof']);
    }

    public function test_file_too_large_is_rejected(): void
    {
        // UploadedFile size is the real file size; max is 4096 KB
        // The fixture is 631 bytes, so to test max we need a file > 4096 KB
        // Use UploadedFile::fake()->create() with explicit size > 4096 KB
        $file = UploadedFile::fake()->create('large.jpg', 5000, 'image/jpeg');

        $response = $this->post(
            route('orders.proof.store', $this->order->invoice_token),
            ['proof' => $file]
        );

        $response->assertSessionHasErrors(['proof']);
    }

    public function test_upload_without_file_is_rejected(): void
    {
        $response = $this->post(
            route('orders.proof.store', $this->order->invoice_token),
            []
        );

        $response->assertSessionHasErrors(['proof']);
    }

    public function test_upload_to_unknown_token_returns_404(): void
    {
        $fixture = base_path('tests/Fixtures/proofs/sample-proof.jpg');
        $tempPath = sys_get_temp_dir() . '/proof.jpg';
        copy($fixture, $tempPath);
        $file = new UploadedFile($tempPath, 'proof.jpg', 'image/jpeg', null, true);

        $response = $this->post(
            route('orders.proof.store', 'nonexistent-token-123'),
            ['proof' => $file]
        );

        $response->assertStatus(404);
    }

    public function test_reupload_after_rejection_creates_new_proof_record(): void
    {
        // First, set order as rejected
        $this->order->update([
            'status' => 'rejected',
            'reject_reason' => 'Bukti kurang jelas',
        ]);

        // Upload a new proof (re-upload)
        $fixture = base_path('tests/Fixtures/proofs/sample-proof.jpg');
        $tempPath = sys_get_temp_dir() . '/proof-v2.jpg';
        copy($fixture, $tempPath);
        $file = new UploadedFile($tempPath, 'proof-v2.jpg', 'image/jpeg', null, true);

        $response = $this->post(
            route('orders.proof.store', $this->order->invoice_token),
            ['proof' => $file]
        );

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // Assert a payment_proofs record was created
        $this->assertDatabaseHas('payment_proofs', [
            'order_id' => $this->order->id,
            'mime' => 'image/jpeg',
            'status' => 'submitted',
        ]);

        // Assert order status changed back to proof_submitted and reject_reason cleared
        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'status' => 'proof_submitted',
            'reject_reason' => null,
        ]);
    }

    public function test_proof_file_is_not_stored_on_public_disk(): void
    {
        $fixture = base_path('tests/Fixtures/proofs/sample-proof.jpg');
        $tempPath = sys_get_temp_dir() . '/proof.jpg';
        copy($fixture, $tempPath);
        $file = new UploadedFile($tempPath, 'proof.jpg', 'image/jpeg', null, true);

        $this->post(
            route('orders.proof.store', $this->order->invoice_token),
            ['proof' => $file]
        );

        // Assert no files under public disk
        Storage::disk('public')->assertMissing('payment-proofs');
    }
}
