<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_checkout_cart()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create(['role' => 'guest']);
        $product = Product::factory()->create(['stocks' => 10]);

        // Add item to cart
        $this->actingAs($user)->postJson('/api/cart', [
            'product_id' => $product->id
        ]);

        // Checkout
        $response = $this->actingAs($user)
                         ->postJson('/api/orders/checkout');

        $response->assertStatus(200);
    }
}
