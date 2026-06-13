<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_returns_200(): void
    {
        $response = $this->get(route('landing'));

        $response->assertStatus(200);
    }

    public function test_landing_page_contains_pas_headline_and_cta(): void
    {
        $response = $this->get(route('landing'));

        $response->assertSee('Ultimate ChatGPT Mastery & Prompt Swipe File');
        $response->assertSee('Dapatkan Sekarang');
        $response->assertSee('Rp 4.900');
    }

    public function test_landing_page_contains_lead_magnet_copy(): void
    {
        $response = $this->get(route('landing'));

        $response->assertSee('Ambil 50 Prompt Gratis');
        $response->assertSee('50 Prompt Pemasaran Gratis');
    }

    public function test_landing_page_contains_checkout_form(): void
    {
        $response = $this->get(route('landing'));

        $response->assertSee('name="name"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="whatsapp"', false);
        $response->assertSee(route('orders.store'));
    }

    public function test_landing_page_contains_lead_form(): void
    {
        $response = $this->get(route('landing'));

        $response->assertSee(route('lead-magnet.store'));
        $response->assertSee('email');
    }

    public function test_landing_page_has_no_fake_testimonials(): void
    {
        $response = $this->get(route('landing'));

        $response->assertDontSee('Testimoni');
        $response->assertDontSee('testimoni');
        $response->assertDontSee('Saya sangat puas');
        $response->assertDontSee('recommended');
    }

    public function test_landing_page_has_no_fake_scarcity(): void
    {
        $response = $this->get(route('landing'));

        $response->assertDontSee('hampir habis');
        $response->assertDontSee('tersisa');
        $response->assertDontSee('buruan');
    }

    public function test_landing_page_shows_strikethrough_original_price(): void
    {
        $response = $this->get(route('landing'));

        $response->assertSee('Rp 490.000');
    }

    public function test_landing_page_contains_countdown(): void
    {
        $response = $this->get(route('landing'));

        $response->assertSee('promo');
    }
}
