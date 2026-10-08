<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_has_add_to_cart_button(): void
    {
        // Arrange
        $product = Product::factory()->create(
            ['stock'=>5,]
        );

        // Act
        $response = $this->get(
            route('products.show', $product)
        );


        // Assert
        $response->assertOk();

        $response->assertSee(
            route('cart.items.store', $product),
            false
        );
    }
}
