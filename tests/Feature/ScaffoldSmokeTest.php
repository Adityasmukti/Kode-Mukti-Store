<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
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

        // Assert unique indexes
        $ordersColumns = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableDetails('orders')
            ->getIndexes();

        $hasInvoiceTokenUnique = false;
        foreach ($ordersColumns as $index) {
            if ($index->isUnique() && in_array('invoice_token', $index->getColumns())) {
                $hasInvoiceTokenUnique = true;
                break;
            }
        }
        $this->assertTrue($hasInvoiceTokenUnique, 'orders.invoice_token must have a unique index');
    }
}
