<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

final class ApiTokenTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_returns_401_json_without_a_bearer_token(): void
    {
        $response = $this->get('/api/user');

        $response->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    public function test_returns_the_token_owner_as_json_api_without_exposing_credentials(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/user');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.api+json')
            ->assertJsonPath('data.id', (string) $user->id)
            ->assertJsonPath('data.type', 'users')
            ->assertJsonPath('data.attributes.email', $user->email)
            ->assertExactJsonStructure([
                'data' => [
                    'id', 'type',
                    'attributes' => ['name', 'email', 'email_verified_at', 'created_at', 'updated_at'],
                ],
            ])
            ->assertHeaderMissing('Set-Cookie');
    }

    public function test_returns_only_allowed_attributes_when_requested_in_a_sparse_fieldset(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/user?fields[users]=name,password,remember_token');

        $response->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => (string) $user->id,
                    'type' => 'users',
                    'attributes' => ['name' => $user->name],
                ],
            ]);
    }

    public function test_returns_401_for_an_invalid_bearer_token(): void
    {
        $response = $this->withToken('invalid-token')->getJson('/api/user');

        $response->assertUnauthorized();
    }

    public function test_returns_401_for_a_revoked_bearer_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test');
        $token->accessToken->delete();

        $response = $this->withToken($token->plainTextToken)->getJson('/api/user');

        $response->assertUnauthorized();
    }

    public function test_returns_401_for_an_expired_bearer_token(): void
    {
        $this->freezeTime();
        $user = User::factory()->create();
        $token = $user->createToken('test', ['*'], now()->subMinute());

        $response = $this->withToken($token->plainTextToken)->getJson('/api/user');

        $response->assertUnauthorized();
    }

    public function test_returns_401_for_a_browser_session_without_a_bearer_token(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $response = $this->getJson('/api/user');

        $response->assertUnauthorized();
    }
}
