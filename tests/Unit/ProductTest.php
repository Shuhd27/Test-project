<!-- <php

test('example', function () {
    expect(true)->toBeTrue();
});
-->
<?php
use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function een_product_kan_worden_aangemaakt()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 9.99,
        ]);

        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    /** @test */
    public function een_product_kan_worden_gelezen()
    {
        $product = Product::factory()->create();
        $this->assertNotNull(Product::find($product->id));
    }

    /** @test */
    public function een_product_kan_worden_bijgewerkt()
    {
        $product = Product::factory()->create();
        $product->update(['price' => 19.99]);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'price' => 19.99]);
    }

    /** @test */
    public function een_product_kan_worden_verwijderd()
    {
        $product = Product::factory()->create();
        $product->delete();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}