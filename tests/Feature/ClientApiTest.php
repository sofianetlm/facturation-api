<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_client(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/clients', ['name' => 'Agence Test'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Agence Test');

        $this->assertDatabaseHas('clients', [
            'name' => 'Agence Test',
            'user_id' => $user->id,
        ]);
    }

    public function test_client_creation_requires_a_name(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/clients', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_user_only_sees_their_own_clients(): void
    {
        $user = User::factory()->create();
        Client::factory()->count(2)->create(['user_id' => $user->id]);
        Client::factory()->create(); // appartient à un autre utilisateur

        Sanctum::actingAs($user);

        $this->getJson('/api/clients')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_user_cannot_view_another_users_client(): void
    {
        $foreignClient = Client::factory()->create();
        Sanctum::actingAs(User::factory()->create());

        $this->getJson("/api/clients/{$foreignClient->id}")->assertNotFound();
    }

    public function test_user_cannot_delete_another_users_client(): void
    {
        $foreignClient = Client::factory()->create();
        Sanctum::actingAs(User::factory()->create());

        $this->deleteJson("/api/clients/{$foreignClient->id}")->assertNotFound();
        $this->assertDatabaseHas('clients', ['id' => $foreignClient->id]);
    }
}