<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_successfully()
    {
        User::factory()->create([
            'email' => 'admin@purplebug.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'full_name' => 'Admin User'
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@purplebug.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'token', 'user']);
    }

    public function test_guest_can_register()
    {
        $response = $this->postJson('/api/register', [
            'full_name' => 'Test Guest',
            'email' => 'guest@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'token']);
    }
}
