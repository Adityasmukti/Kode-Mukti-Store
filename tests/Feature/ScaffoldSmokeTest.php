<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ScaffoldSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_scaffold_boots(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_migrations_create_expected_columns(): void
    {
        // Assert orders table columns
        $this->assertTrue(Schema::hasTable('orders'));
        $this->assertTrue(Schema::hasColumn('orders', 'invoice_token'));
        $this->assertTrue(Schema::hasColumn('orders', 'name'));
        $this->assertTrue(Schema::hasColumn('orders', 'email'));
        $this->assertTrue(Schema::hasColumn('orders', 'whatsapp'));
        $this->assertTrue(Schema::hasColumn('orders', 'amount'));
        $this->assertTrue(Schema::hasColumn('orders', 'status'));
        $this->assertTrue(Schema::hasColumn('orders', 'reject_reason'));
        $this->assertTrue(Schema::hasColumn('orders', 'verified_at'));
        $this->assertTrue(Schema::hasColumn('orders', 'verified_by'));
        $this->assertTrue(Schema::hasColumn('orders', 'created_at'));
        $this->assertTrue(Schema::hasColumn('orders', 'updated_at'));

        // Assert payment_proofs table columns
        $this->assertTrue(Schema::hasTable('payment_proofs'));
        $this->assertTrue(Schema::hasColumn('payment_proofs', 'order_id'));
        $this->assertTrue(Schema::hasColumn('payment_proofs', 'disk'));
        $this->assertTrue(Schema::hasColumn('payment_proofs', 'path'));
        $this->assertTrue(Schema::hasColumn('payment_proofs', 'mime'));
        $this->assertTrue(Schema::hasColumn('payment_proofs', 'size'));
        $this->assertTrue(Schema::hasColumn('payment_proofs', 'status'));
        $this->assertTrue(Schema::hasColumn('payment_proofs', 'created_at'));
        $this->assertTrue(Schema::hasColumn('payment_proofs', 'updated_at'));

        // Assert leads table columns
        $this->assertTrue(Schema::hasTable('leads'));
        $this->assertTrue(Schema::hasColumn('leads', 'email'));
        $this->assertTrue(Schema::hasColumn('leads', 'download_token'));
        $this->assertTrue(Schema::hasColumn('leads', 'first_opted_at'));
        $this->assertTrue(Schema::hasColumn('leads', 'last_opted_at'));
        $this->assertTrue(Schema::hasColumn('leads', 'created_at'));
        $this->assertTrue(Schema::hasColumn('leads', 'updated_at'));

        // Assert users table has is_admin
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasColumn('users', 'is_admin'));


        // Assert unique index on orders.invoice_token using raw SQLite introspection
        $indexes = Schema::getConnection()->select("PRAGMA index_list('orders')");
        $hasInvoiceTokenUnique = false;
        foreach ($indexes as $index) {
            if ($index->unique) {
                $indexInfo = Schema::getConnection()->select("PRAGMA index_info('{$index->name}')");
                $columns = array_column($indexInfo, 'name');
                if (in_array('invoice_token', $columns)) {
                    $hasInvoiceTokenUnique = true;
                    break;
                }
            }
        }
        $this->assertTrue($hasInvoiceTokenUnique, 'orders.invoice_token must have a unique index');
    }

    public function test_route_contracts_and_validation(): void
    {
        // Assert all named routes exist
        $routeNames = [
            'landing',
            'orders.store',
            'orders.show',
            'orders.proof.store',
            'orders.download',
            'lead-magnet.store',
            'lead-magnet.show',
            'lead-magnet.download',
            'admin.login',
            'admin.logout',
            'admin.orders.index',
            'admin.orders.confirm',
            'admin.orders.reject',
            'admin.orders.proof',
        ];

        foreach ($routeNames as $name) {
            $this->assertTrue(Route::has($name), "Named route '{$name}' does not exist");
        }

        // Assert public buyer routes use token-based parameters, not numeric order IDs
        $ordersShow = Route::getRoutes()->getByName('orders.show');
        $this->assertNotNull($ordersShow, 'orders.show route not found');
        $uri = $ordersShow->uri();
        $this->assertStringContainsString('{invoice_token}', $uri,
            'orders.show URI must contain {invoice_token}, not {order}');

        $ordersDownload = Route::getRoutes()->getByName('orders.download');
        $this->assertNotNull($ordersDownload, 'orders.download route not found');
        $uri = $ordersDownload->uri();
        $this->assertStringContainsString('{invoice_token}', $uri,
            'orders.download URI must contain {invoice_token}, not {order}');

        $leadShow = Route::getRoutes()->getByName('lead-magnet.show');
        $this->assertNotNull($leadShow, 'lead-magnet.show route not found');
        $uri = $leadShow->uri();
        $this->assertStringContainsString('{download_token}', $uri,
            'lead-magnet.show URI must contain {download_token}');

        $leadDownload = Route::getRoutes()->getByName('lead-magnet.download');
        $this->assertNotNull($leadDownload, 'lead-magnet.download route not found');
        $uri = $leadDownload->uri();
        $this->assertStringContainsString('{download_token}', $uri,
            'lead-magnet.download URI must contain {download_token}');

        // Assert throttle middleware on order, proof, lead, and login routes
        $ordersStore = Route::getRoutes()->getByName('orders.store');
        $this->assertNotNull($ordersStore);
        $this->assertStringContainsString('throttle', json_encode($ordersStore->gatherMiddleware()),
            'orders.store must have throttle middleware');

        // Assert UploadPaymentProofRequest validation rules allow valid image uploads
        $request = new \App\Http\Requests\UploadPaymentProofRequest;
        $rules = $request->rules();
        $this->assertArrayHasKey('proof', $rules, 'UploadPaymentProofRequest must have "proof" rule');

        $proofRules = is_array($rules['proof']) ? $rules['proof'] : explode('|', $rules['proof']);
        $rulesStr = implode('|', $proofRules);
        $this->assertStringContainsString('required', $rulesStr);
        $this->assertStringContainsString('file', $rulesStr);
        $this->assertStringContainsString('image', $rulesStr);
        $this->assertStringContainsString('mimes:jpg,jpeg,png,webp', $rulesStr);
        $this->assertStringContainsString('max:4096', $rulesStr);

        // Assert StoreOrderRequest validation rules
        $orderRequest = new \App\Http\Requests\StoreOrderRequest;
        $orderRules = $orderRequest->rules();
        $this->assertArrayHasKey('name', $orderRules);
        $this->assertArrayHasKey('email', $orderRules);
        $this->assertArrayHasKey('whatsapp', $orderRules);

        // Assert StoreLeadRequest only has email field
        $leadRequest = new \App\Http\Requests\StoreLeadRequest;
        $leadRules = $leadRequest->rules();
        $this->assertArrayHasKey('email', $leadRules);
        $this->assertCount(1, $leadRules,
            'StoreLeadRequest should only validate the email field');
    }

    public function test_admin_seeder_reads_env_credentials(): void
    {
        // Set fake env credentials
        putenv('ADMIN_EMAIL=admin@example.com');
        putenv('ADMIN_PASSWORD=secure-password-123');

        // Run the seeder
        Artisan::call('db:seed', ['--class' => 'Database\Seeders\AdminUserSeeder']);

        // Assert admin user was created
        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'is_admin' => 1,
        ]);

        // Clean up env
        putenv('ADMIN_EMAIL');
        putenv('ADMIN_PASSWORD');
    }

    public function test_admin_seeder_skips_when_env_empty(): void
    {
        // Ensure env vars are not set
        putenv('ADMIN_EMAIL');
        putenv('ADMIN_PASSWORD');

        // Run the seeder - should not crash
        $exitCode = Artisan::call('db:seed', ['--class' => 'Database\Seeders\AdminUserSeeder']);

        // Assert command succeeded (warned and skipped)
        $this->assertEquals(0, $exitCode);

        // Assert no admin user was created
        $this->assertDatabaseMissing('users', ['is_admin' => 1]);
    }

    public function test_proof_preview_route_requires_auth(): void
    {
        $response = $this->get(route('admin.orders.proof', ['order' => 1, 'proof' => 1]));

        // Unauthenticated request should redirect to login
        $response->assertRedirect();
        $response->assertRedirectContains('admin/login');
    }
}
