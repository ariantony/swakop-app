<?php

namespace Database\Seeders;

use App\Models\Price;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FixPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = fopen(base_path("database/csv/FIX_prices.csv"), "r");
        $firstline = true;
        while (($data = fgetcsv($csvFile, 1000, ",")) !== FALSE) {
            if (!$firstline) {
                Price::create([
                    'product_id' => $data[1],
                    'cost_selling_per_unit' => $data[2],
                    'price_per_unit' => $data[3],
                    'effective_date' => date('Y-m-d'),
                ]);
            }
            $firstline = false;
        }
        fclose($csvFile);
    }
}
