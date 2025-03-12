<?php

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;


class ProductTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function een_product_kan_worden_aangemaakt()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 9.99,
        ]);

        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    #[Test]
    public function een_product_kan_worden_gelezen()
    {
        $product = Product::factory()->create();
        $this->assertNotNull(Product::find($product->id));
    }

    #[Test]
    public function een_product_kan_worden_bijgewerkt()
    {
        $product = Product::factory()->create();
        $product->update(['price' => 19.99]);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'price' => 19.99]);
    }

    #[Test]
    public function een_product_kan_worden_verwijderd()
    {
        $product = Product::factory()->create();
        $product->delete();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}