<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use App\Models\Server;

class ServerApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $headers;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and get token
        $this->user = User::factory()->create();
        $token = $this->user->createToken('api-token')->plainTextToken;

        $this->headers = [
            'Authorization' => "Bearer $token",
            'Accept' => 'application/json',
        ];
    }

    #[Test]
    public function unauthenticated_user_cannot_access_protected_routes()
    {
        $response = $this->getJson('/api/servers');
        $response->assertStatus(401); // Unauthorized
    }

    #[Test]
    public function user_can_list_servers()
    {
        Server::factory()->count(5)->create();

        $response = $this->getJson('/api/servers', $this->headers);

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    #[Test]
    public function user_can_create_server()
    {
        $payload = [
            'name' => 'Server 1',
            'ip_address' => '192.168.1.10',
            'provider' => 'aws',
            'status' => 'active',
            'cpu_cores' => 4,
            'ram_mb' => 2048,
            'storage_gb' => 100,
        ];

        $response = $this->postJson('/api/servers', $payload, $this->headers);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Server 1']);
    }

    #[Test]
    public function create_server_fails_with_invalid_data()
    {
        $payload = [
            'name' => '',
            'ip_address' => 'invalid-ip',
            'provider' => 'unknown',
            'status' => 'invalid',
            'cpu_cores' => 500,
            'ram_mb' => 100,
            'storage_gb' => 5,
        ];

        $response = $this->postJson('/api/servers', $payload, $this->headers);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'ip_address',
                'provider',
                'status',
                'cpu_cores',
                'ram_mb',
                'storage_gb'
            ]);
    }

    #[Test]
    public function user_can_show_server()
    {
        $server = Server::factory()->create();

        $response = $this->getJson("/api/servers/{$server->id}", $this->headers);

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $server->id]);
    }

    #[Test]
    public function user_can_update_server()
    {
        $server = Server::factory()->create();

        $payload = [
            'name' => 'Updated Server',
            'ip_address' => '192.168.1.20',
            'provider' => $server->provider,
            'status' => 'inactive',
            'cpu_cores' => 8,
            'ram_mb' => 4096,
            'storage_gb' => 200,
        ];

        $response = $this->putJson("/api/servers/{$server->id}", $payload, $this->headers);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Server']);
    }

    #[Test]
    public function user_can_delete_server()
    {
        $server = Server::factory()->create();

        $response = $this->deleteJson("/api/servers/{$server->id}", [], $this->headers);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Server deleted']);
    }

    #[Test]
    public function user_can_perform_bulk_action()
    {
        $servers = Server::factory()->count(3)->create();

        $ids = $servers->pluck('id')->toArray();

        $payload = [
            'ids' => $ids,
            'action' => 'delete',
        ];

        $response = $this->postJson('/api/servers/bulk', $payload, $this->headers);

        $response->assertStatus(200)
            ->assertJson(
                fn($json) =>
                $json->where('data.message', 'Bulk action performed successfully.')
                    ->etc()
            );
    }

    #[Test]
    public function user_can_access_optimized_list()
    {
        Server::factory()->count(10)->create();

        $response = $this->getJson('/api/servers/optimized', $this->headers);

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    #[Test]
    public function user_can_access_slow_list()
    {
        Server::factory()->count(50)->create(); // simulate slow query

        $response = $this->getJson('/api/servers/slow', $this->headers);

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }
}
