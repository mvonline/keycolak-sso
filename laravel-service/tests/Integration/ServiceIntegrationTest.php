<?php

namespace Tests\Integration;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ServiceIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();
    }

    public function test_service_integration_with_valid_token()
    {
        $userInfo = [
            'sub' => '123',
            'email' => 'test@example.com',
            'preferred_username' => 'testuser',
            'realm_access' => [
                'roles' => ['user']
            ]
        ];

        // Mock Keycloak response
        Http::fake([
            config('keycloak.url') . '/realms/' . config('keycloak.realm') . '/protocol/openid-connect/userinfo' => Http::response($userInfo, 200)
        ]);

        // Mock Golang service response
        Http::fake([
            'http://golang-service:8081/api/protected' => Http::response([
                'message' => 'This is a protected endpoint',
                'service' => 'Golang Service',
                'user' => $userInfo
            ], 200)
        ]);

        // Mock Spring Boot service response
        Http::fake([
            'http://springboot-service:8082/api/protected' => Http::response([
                'message' => 'This is a protected endpoint',
                'service' => 'Spring Boot Service',
                'user' => $userInfo
            ], 200)
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

    public function test_service_integration_with_invalid_token()
    {
        // Mock Keycloak error response
        Http::fake([
            config('keycloak.url') . '/realms/' . config('keycloak.realm') . '/protocol/openid-connect/userinfo' => Http::response([], 401)
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token'
        ])->getJson('/api/protected');

        $response->assertStatus(401);
    }
} 