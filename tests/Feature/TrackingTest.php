<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracking_script_hidden_when_not_configured(): void
    {
        $response = $this->get(route('landing'));

        $response->assertDontSee('/script.js');
    }

    public function test_cta_button_has_data_umami_event(): void
    {
        $response = $this->get(route('landing'));

        $response->assertSee('data-umami-event="CTA Click"', false);
    }

    public function test_lead_form_has_data_umami_event(): void
    {
        $response = $this->get(route('landing'));

        $response->assertSee('data-umami-event="Lead Captured"', false);
    }

    public function test_download_button_has_data_umami_event(): void
    {
        $order = Order::factory()->create([
            'status' => 'verified',
        ]);

        $response = $this->get(route('orders.show', $order->invoice_token));

        $response->assertSee('data-umami-event="Download Ebook"', false);
    }

    public function test_upsell_cta_has_data_umami_event(): void
    {
        $lead = \App\Models\Lead::factory()->create();

        $response = $this->get(route('lead-magnet.show', $lead->download_token));

        $response->assertSee('data-umami-event="CTA Click"', false);
    }

    public function test_umami_events_logged_on_order_creation(): void
    {
        \Illuminate\Support\Facades\Log::shouldReceive('info')
            ->once()
            ->with('Umami Event: Order Created', \Mockery::on(function ($context) {
                return isset($context['invoice_token']);
            }));

        $this->post(route('orders.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'whatsapp' => '08123456789',
        ]);
    }

    public function test_umami_events_logged_on_lead_capture(): void
    {
        \Illuminate\Support\Facades\Log::shouldReceive('info')
            ->once()
            ->with('Umami Event: Lead Captured', \Mockery::on(function ($context) {
                return isset($context['email']);
            }));

        $this->post(route('lead-magnet.store'), [
            'email' => 'lead@example.com',
        ]);
    }
}
