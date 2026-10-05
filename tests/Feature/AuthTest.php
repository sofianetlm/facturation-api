<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ])->assertCreated()->assertJsonStructure(['user', 'token']);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create(['password' => 'secret1234']);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret1234',
        ])->assertOk()->assertJsonStructure(['user', 'token']);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'mauvais-mot-de-passe',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_protected_route_requires_token(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
    }
}