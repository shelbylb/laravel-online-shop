<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_added_to_cart(): void
    {
        $product = Product::factory()->create([
            'stock' => 5,
        ]);

        $response = $this->post(
            route('cart.items.store', $product),
            ['quantity' => 2]
        );

        $response->assertRedirect();

        $response->assertSessionHas(
            'cart.items.' . $product->id,
            2
        );
    }
}
