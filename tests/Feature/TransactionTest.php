<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    public function test_product_unit_stock_is_equal_with_transactions()
    {
        $product = Product::create([
            'code' => 1,
            'name' => 'test',
            'barcode' => '1',
            'qty_per_unit' => 0,
            'qty_per_box' => 0,
            'qty_per_carton' => 0,
        ]);

        $product->transactions()->delete();

        $user = User::factory()->create();

        $buy = $product->transactions()->create([
            'product_id' => $product->id,
            'type' => 'buy',
            'qty_unit' => 10,
            'cost_unit' => 1000,
            'user_id' => $user->id,
        ]);

        $sell = $product->transactions()->create([
            'product_id' => $product->id,
            'type' => 'sell',
            'qty_unit' => 9,
            'cost_unit' => 1000,
            'user_id' => $user->id,
        ]);

        $product = Product::find($product->id);
        
        $this->assertTrue($product->stock_unit === 1);
    }

    public function test_product_box_stock_is_equal_with_transactions()
    {
        $product = Product::create([
            'code' => 1,
            'name' => 'test',
            'barcode' => '1',
            'qty_per_unit' => 0,
            'qty_per_box' => 0,
            'qty_per_carton' => 0,
        ]);

        $product->transactions()->delete();

        $user = User::factory()->create();

        $buy = $product->transactions()->create([
            'product_id' => $product->id,
            'type' => 'buy',
            'qty_box' => 10,
            'cost_box' => 1000,
            'user_id' => $user->id,
        ]);

        $sell = $product->transactions()->create([
            'product_id' => $product->id,
            'type' => 'sell',
            'qty_box' => 9,
            'cost_box' => 1000,
            'user_id' => $user->id,
        ]);

        $product = Product::find($product->id);
        
        $this->assertTrue($product->stock_box === 1);
    }

    public function test_product_carton_stock_is_equal_with_transactions()
    {
        $product = Product::create([
            'code' => 1,
            'name' => 'test',
            'barcode' => '1',
            'qty_per_unit' => 0,
            'qty_per_box' => 0,
            'qty_per_carton' => 0,
        ]);

        $product->transactions()->delete();

        $user = User::factory()->create();

        $buy = $product->transactions()->create([
            'product_id' => $product->id,
            'type' => 'buy',
            'qty_carton' => 10,
            'cost_carton' => 1000,
            'user_id' => $user->id,
        ]);

        $sell = $product->transactions()->create([
            'product_id' => $product->id,
            'type' => 'sell',
            'qty_carton' => 9,
            'cost_carton' => 1000,
            'user_id' => $user->id,
        ]);

        $product = Product::find($product->id);
        
        $this->assertTrue($product->stock_carton === 1);
    }
}
