<?php

namespace Database\Seeders;

use App\Models\Detail;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DetailTransactionFirstStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = fopen(base_path("database/csv/details.csv"), "r");
        $firstline = true;
        while (($data = fgetcsv($csvFile, 1000, ",")) !== FALSE) {
            if (!$firstline) {
                Detail::create([
                    'transaction_id' => $data[1],
                    'product_id' => $data[2],
                    'type' => $data[3],
                    'qty_unit' => $data[4],
                    'cost_unit' => $data[5],
                    'subtotal' => $data[6],
                ]);
            }
            $firstline = false;
        }
        fclose($csvFile);
    }
}
