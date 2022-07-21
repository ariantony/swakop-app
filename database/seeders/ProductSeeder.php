<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        $csvFile = fopen(base_path("database/csv/products.csv"), "r");
        $firstline = true;
        while (($data = fgetcsv($csvFile, 1000, ",")) !== FALSE) {
            if (!$firstline) {
                Product::create([
                    'group_id' => $data[1],
                    'name' => $data[2],
                    'barcode' => $data[3],
                ]);
            }
            $firstline = false;
        }
        fclose($csvFile);
    }
}
