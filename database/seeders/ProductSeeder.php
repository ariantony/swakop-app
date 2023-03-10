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
        while (($data = fgetcsv($csvFile, 0, ",")) !== FALSE) {
            if (!$firstline) {
                if (empty(trim($data[3]))) {
                    Product::create([
                        'group_id' => $data[1],
                        'name' => trim($data[2]),
                        'barcode' => $data[3],
                    ]);
                    continue;
                }
                
                Product::updateOrCreate([
                    'barcode' => $data[3],
                ], [
                    'group_id' => $data[1],
                    'name' => trim($data[2]),
                ]);
            }
            $firstline = false;
        }
        fclose($csvFile);
    }
}
