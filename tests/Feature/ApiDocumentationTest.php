<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

final class ApiDocumentationTest extends TestCase
{
    public function test_renders_scalar_documentation_locally_without_session_cookies(): void
    {
        $this->app->detectEnvironment(fn (): string => 'local');

        $response = $this->get('/docs/api');

        $response->assertOk()->assertHeader('Content-Type', 'text/html; charset=utf-8')->assertSeeHtml('Scalar.createApiReference')
            ->assertHeaderMissing('Set-Cookie');
    }

    public function test_generates_openapi_with_bearer_authentication_for_the_user_endpoint(): void
    {
        $this->app->detectEnvironment(fn (): string => 'local');

        $response = $this->getJson('/docs/api.json');

        $response->assertOk()
            ->assertJsonStructure(['openapi', 'paths' => ['/user' => ['get' => ['responses']]]])
            ->assertJsonPath('components.securitySchemes.http.type', 'http')
            ->assertJsonPath('components.securitySchemes.http.scheme', 'bearer')
            ->assertJsonPath('security.0.http', [])
            ->assertHeaderMissing('Set-Cookie');
    }

    #[TestWith(['/docs/api'])]
    #[TestWith(['/docs/api.json'])]
    public function test_returns_403_for_documentation_in_production(string $path): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        $response = $this->get($path);

        $response->assertForbidden()
            ->assertHeader('Content-Type', 'application/json')
            ->assertHeaderMissing('Set-Cookie');
    }
}
