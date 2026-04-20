<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_user_can_add_to_cart()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create(['role' => 'guest']);
        $product = Product::factory()->create(['stocks' => 5]);

        $response = $this->actingAs($user)
                         ->postJson('/api/cart', [
                             'product_id' => $product->id
                         ]);

        $response->assertStatus(200);
    }
}
