<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductFixSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        $csvFile = fopen(base_path("database/csv/products_pma.csv"), "r");
        $firstline = true;
        while (($data = fgetcsv($csvFile, 0, ",")) !== FALSE) {
            if (!$firstline) {
                if (empty(trim($data[4]))) {
                    Product::create([
                        'group_id' => $data[1],
                        'name' => trim($data[3]),
                        'barcode' => $data[4],
                    ]);
                    continue;
                }
                
                Product::updateOrCreate([
                    'barcode' => $data[4],
                ], [
                    'group_id' => $data[1],
                    'name' => trim($data[3]),
                ]);
            }
            $firstline = false;
        }
        fclose($csvFile);
    }
}
