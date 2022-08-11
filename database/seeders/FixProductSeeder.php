<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FixProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = fopen(base_path("database/csv/FIX_products.csv"), "r");
        $firstline = true;
        while (($data = fgetcsv($csvFile, 0, ",")) !== FALSE) {
            if (!$firstline) {
                Product::create([
                        'group_id' => $data[1],
                        'name' => trim($data[2]),
                        'barcode' => $data[3],
                    ]);
            }
            $firstline = false;
        }
        fclose($csvFile);
    }
}
