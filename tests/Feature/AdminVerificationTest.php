<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);
    }

    public function test_admin_login_and_dashboard(): void
    {
        // 1. Guest is redirected to login when accessing dashboard
        $this->get(route('admin.orders.index'))
            ->assertRedirect(route('admin.login'));

        // 2. Login page is accessible
        $this->get(route('admin.login'))
            ->assertStatus(200)
            ->assertSee('Email')
            ->assertSee('Password');

        // 3. Invalid credentials show error and stay unauthenticated
        $this->post(route('admin.login.store'), [
            'email' => 'admin@test.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors(['email']);

        $this->assertGuest();

        // 4. Valid credentials log in, regenerate session, and redirect to dashboard
        $response = $this->post(route('admin.login.store'), [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.orders.index'));
        $this->assertAuthenticated();

        // 5. Dashboard accessible when authenticated and shows expected structure
        $response = $this->get(route('admin.orders.index'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard'); // dashboard heading
        $response->assertSee('Keluar'); // logout link/button

        // 6. Orders appear in correct priority: proof_submitted first, then pending,
        //    then rejected, then verified
        $orderProof = Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Proof Submitter',
            'email' => 'proof@example.com',
            'whatsapp' => '08111111111',
            'amount' => 99000,
            'status' => 'proof_submitted',
        ]);
        $orderPending = Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Pending Buyer',
            'email' => 'pending@example.com',
            'whatsapp' => '08222222222',
            'amount' => 99000,
            'status' => 'pending',
        ]);
        $orderRejected = Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Rejected Buyer',
            'email' => 'rejected@example.com',
            'whatsapp' => '08333333333',
            'amount' => 99000,
            'status' => 'rejected',
        ]);
        $orderVerified = Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Verified Buyer',
            'email' => 'verified@example.com',
            'whatsapp' => '08444444444',
            'amount' => 99000,
            'status' => 'verified',
        ]);

        $response = $this->get(route('admin.orders.index'));
        $response->assertStatus(200);
        $response->assertSee('Proof Submitter');
        $response->assertSee('Pending Buyer');
        $response->assertSee('Rejected Buyer');
        $response->assertSee('Verified Buyer');

        // 7. Dashboard shows proof preview link for orders with uploads
        // Create a payment proof record and verify the link exists
        Storage::fake('local');
        $fixture = base_path('tests/Fixtures/proofs/sample-proof.jpg');
        $storedPath = Storage::disk('local')->putFileAs(
            'payment-proofs/' . $orderProof->invoice_token,
            new UploadedFile($fixture, 'proof.jpg', 'image/jpeg', null, true),
            'proof.jpg'
        );

        $proof = PaymentProof::create([
            'order_id' => $orderProof->id,
            'disk' => 'local',
            'path' => $storedPath,
            'mime' => 'image/jpeg',
            'size' => filesize($fixture),
            'status' => 'submitted',
        ]);

        $response = $this->get(route('admin.orders.index'));
        $response->assertStatus(200);
        $response->assertSee(route('admin.orders.proof', [$orderProof, $proof], false));

        // 8. Non-admin user cannot access dashboard
        $nonAdmin = User::factory()->create([
            'is_admin' => false,
        ]);
        $response = $this->actingAs($nonAdmin)->get(route('admin.orders.index'));
        $response->assertStatus(403);

        // 9. Proof preview route requires auth (guest redirected to login)
        $this->post(route('admin.logout'));
        $this->get(route('admin.orders.proof', [$orderProof, $proof]))
            ->assertRedirect(route('admin.login'));

        // 10. Proof preview streams image content for authenticated admin
        $this->actingAs($this->admin);
        $response = $this->get(route('admin.orders.proof', [$orderProof, $proof]));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/jpeg');

        // 11. Proof preview verifies the proof belongs to the order
        $otherOrder = Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Other',
            'email' => 'other@example.com',
            'whatsapp' => '08555555555',
            'amount' => 99000,
            'status' => 'pending',
        ]);
        $this->get(route('admin.orders.proof', [$otherOrder, $proof]))
            ->assertStatus(404);

        // 12. Logout invalidates session
        $this->post(route('admin.logout'));
        $this->assertGuest();
        $this->get(route('admin.orders.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_confirm_and_reject_actions(): void
    {
        // Create an order in proof_submitted state with a payment proof
        $order = Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Bayu Test',
            'email' => 'bayu@example.com',
            'whatsapp' => '08123456789',
            'amount' => 99000,
            'status' => 'proof_submitted',
        ]);

        Storage::fake('local');
        $fixture = base_path('tests/Fixtures/proofs/sample-proof.jpg');
        $storedPath = Storage::disk('local')->putFileAs(
            'payment-proofs/' . $order->invoice_token,
            new UploadedFile($fixture, 'proof.jpg', 'image/jpeg', null, true),
            'proof.jpg'
        );

        $proof = PaymentProof::create([
            'order_id' => $order->id,
            'disk' => 'local',
            'path' => $storedPath,
            'mime' => 'image/jpeg',
            'size' => filesize($fixture),
            'status' => 'submitted',
        ]);

        // 1. Guest cannot confirm (redirected to login)
        $this->post(route('admin.orders.confirm', $order))
            ->assertRedirect(route('admin.login'));

        // 2. Guest cannot reject (redirected to login)
        $this->post(route('admin.orders.reject', $order), [
            'reject_reason' => 'Bukti kurang jelas',
        ])->assertRedirect(route('admin.login'));

        // 3. Non-admin user cannot confirm
        $nonAdmin = User::factory()->create(['is_admin' => false]);
        $this->actingAs($nonAdmin)
            ->post(route('admin.orders.confirm', $order))
            ->assertStatus(403);

        // 4. Non-admin user cannot reject
        $this->actingAs($nonAdmin)
            ->post(route('admin.orders.reject', $order), [
                'reject_reason' => 'Alasan',
            ])->assertStatus(403);

        // 5. Admin confirm: changes status to verified, sets audit fields, marks proof accepted
        $this->actingAs($this->admin);
        $this->post(route('admin.orders.confirm', $order))
            ->assertRedirect(route('admin.orders.index'));

        $order->refresh();
        $proof->refresh();

        $this->assertEquals('verified', $order->status);
        $this->assertNotNull($order->verified_at);
        $this->assertEquals($this->admin->id, $order->verified_by);
        $this->assertNull($order->reject_reason);
        $this->assertEquals('accepted', $proof->status);

        // 6. Admin reject: requires reason, stores reason, marks proof rejected
        $order2 = Order::create([
            'invoice_token' => \Illuminate\Support\Str::random(32),
            'name' => 'Citra Test',
            'email' => 'citra@example.com',
            'whatsapp' => '08222222222',
            'amount' => 99000,
            'status' => 'proof_submitted',
        ]);

        $storedPath2 = Storage::disk('local')->putFileAs(
            'payment-proofs/' . $order2->invoice_token,
            new UploadedFile($fixture, 'proof2.jpg', 'image/jpeg', null, true),
            'proof2.jpg'
        );

        $proof2 = PaymentProof::create([
            'order_id' => $order2->id,
            'disk' => 'local',
            'path' => $storedPath2,
            'mime' => 'image/jpeg',
            'size' => filesize($fixture),
            'status' => 'submitted',
        ]);

        // 7. Reject without reason fails validation
        $this->post(route('admin.orders.reject', $order2), [])
            ->assertSessionHasErrors(['reject_reason']);

        // 8. Reject with reason succeeds
        $this->post(route('admin.orders.reject', $order2), [
            'reject_reason' => 'Nominal tidak sesuai. Harap upload ulang dengan nominal yang benar.',
        ])->assertRedirect(route('admin.orders.index'));

        $order2->refresh();
        $proof2->refresh();

        $this->assertEquals('rejected', $order2->status);
        $this->assertEquals('Nominal tidak sesuai. Harap upload ulang dengan nominal yang benar.', $order2->reject_reason);
        $this->assertNull($order2->verified_at);
        $this->assertNull($order2->verified_by);
        $this->assertEquals('rejected', $proof2->status);
    }
}
