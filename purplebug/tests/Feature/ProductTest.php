<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_product()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
                         ->postJson('/api/products', [
                             'name' => 'Test Laptop',
                             'price' => 45000.00,
                             'stocks' => 10,
                             'description' => 'High performance laptop'
                         ]);

        $response->assertStatus(201);
    }

    public function test_products_are_listed_publicly()
    {
        $response = $this->getJson('/api/products');
        $response->assertStatus(200);
    }
}
