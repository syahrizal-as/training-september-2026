<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $authUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authUser = User::factory()->create();
    }

    public function test_unauthenticated_cannot_access_user_api(): void
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }

    public function test_can_get_list_of_users_when_authenticated(): void
    {
        Sanctum::actingAs($this->authUser);
        User::factory()->count(3)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'email', 'created_at', 'updated_at'],
            ],
            'links',
            'meta',
        ]);
    }

    public function test_can_create_new_user_via_api(): void
    {
        Sanctum::actingAs($this->authUser);

        $payload = [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.name', 'Budi Santoso');
        $response->assertJsonPath('data.email', 'budi@example.com');

        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
        ]);
    }

    public function test_create_validates_required_fields(): void
    {
        Sanctum::actingAs($this->authUser);

        $response = $this->postJson('/api/users', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_can_show_single_user_via_api(): void
    {
        Sanctum::actingAs($this->authUser);
        $targetUser = User::factory()->create();

        $response = $this->getJson("/api/users/{$targetUser->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.id', $targetUser->id);
        $response->assertJsonPath('data.email', $targetUser->email);
    }

    public function test_can_update_user_via_api(): void
    {
        Sanctum::actingAs($this->authUser);
        $targetUser = User::factory()->create([
            'name' => 'Nama Lama',
        ]);

        $payload = [
            'name' => 'Nama Baru Diubah',
            'email' => $targetUser->email,
        ];

        $response = $this->putJson("/api/users/{$targetUser->id}", $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.name', 'Nama Baru Diubah');

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Nama Baru Diubah',
        ]);
    }

    public function test_can_delete_user_via_api(): void
    {
        Sanctum::actingAs($this->authUser);
        $targetUser = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$targetUser->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
        ]);
    }
}
