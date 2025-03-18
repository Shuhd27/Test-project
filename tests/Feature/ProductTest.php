<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('een product kan worden aangemaakt', function () {
    $product = Product::create([
        'name' => 'Test Product',
        'price' => 9.99,
    ]);

    expect($product->exists)->toBeTrue();
    $this->assertDatabaseHas('products', ['name' => 'Test Product']);
});

test('een product kan worden gelezen', function () {
    $product = Product::factory()->create();
    expect(Product::find($product->id))->not->toBeNull();
});

test('een product kan worden bijgewerkt', function () {
    $product = Product::factory()->create();
    $product->update(['price' => 19.99]);

    $this->assertDatabaseHas('products', ['id' => $product->id, 'price' => 19.99]);
});

test('een product kan worden verwijderd', function () {
    $product = Product::factory()->create();
    $product->delete();

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});