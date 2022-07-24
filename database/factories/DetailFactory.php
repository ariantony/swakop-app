<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Detail>
 */
class DetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $product = Product::whereHas('price')->find(rand(1, Product::count()));

        return [
            'transaction_id' => rand(562, Transaction::count() - 1),
            'product_id' => $product->id,
            'type' => ['buy', 'sell'][rand(0, 1)],
            'qty_unit' => rand(1, 1000),
            'cost_unit' => $product->price->cost_selling_per_unit,
            'created_at' => Carbon::createFromDate(2022, rand(1, 12), rand(1, 30)),
        ];
    }
}
