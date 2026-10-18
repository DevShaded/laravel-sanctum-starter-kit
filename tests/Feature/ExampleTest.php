<?php

declare(strict_types=1);

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

final class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_health_endpoint_returns_a_successful_response(): void
    {
        $response = $this->getJson('/up');

        $response->assertOk()->assertExactJson(['status' => 'up']);
    }

    public function test_the_root_returns_a_json_404(): void
    {
        $response = $this->get('/');

        $response->assertNotFound()->assertHeader('Content-Type', 'application/json');
    }

    public function test_the_csrf_cookie_endpoint_initializes_browser_cookies(): void
    {
        $response = $this->get('/sanctum/csrf-cookie');

        $response->assertNoContent()->assertCookie('XSRF-TOKEN')
            ->assertCookie(Config::string('session.cookie'));
    }
}
