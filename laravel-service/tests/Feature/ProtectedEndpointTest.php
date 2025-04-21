<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProtectedEndpointTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();
    }

    public function test_protected_endpoint_requires_token()
    {
        $response = $this->getJson('/api/protected');
        $response->assertStatus(401);
    }

    public function test_protected_endpoint_with_valid_token()
    {
        $userInfo = [
            'sub' => '123',
            'email' => 'test@example.com',
            'preferred_username' => 'testuser',
            'realm_access' => [
                'roles' => ['user']
            ]
        ];

        Http::fake([
            config('keycloak.url') . '/realms/' . config('keycloak.realm') . '/protocol/openid-connect/userinfo' => Http::response($userInfo, 200)
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer valid-token'
        ])->getJson('/api/protected');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'This is a protected endpoint',
                'service' => 'Laravel Service'
            ])
            ->assertJsonStructure([
                'message',
                'user',
                'service'
            ]);
    }

    public function test_protected_endpoint_with_invalid_token()
    {
        Http::fake([
            config('keycloak.url') . '/realms/' . config('keycloak.realm') . '/protocol/openid-connect/userinfo' => Http::response([], 401)
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token'
        ])->getJson('/api/protected');

        $response->assertStatus(401);
    }

    public function test_health_endpoint()
    {
        $response = $this->getJson('/health');
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'healthy'
            ]);
    }
} 