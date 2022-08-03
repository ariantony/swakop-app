<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionTempSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::where('id', '>', 557)
                    ->get()
                    ->each(function ($p) {
                        Transaction::create([
                            'user_id' => 1,
                            'total_cost' => 0,
                            'payment_method' => 'cash',
                        ]);
                    });
    }
}
