<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TransactionFirstStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = fopen(base_path("database/csv/transactions.csv"), "r");
        $firstline = true;
        while (($data = fgetcsv($csvFile, 1000, ",")) !== FALSE) {
            if (!$firstline) {
                Transaction::create([
                    'user_id' => $data[1],
                    'total_cost' => $data[2],
                    'payment_method' => $data[3],
                ]);
            }
            $firstline = false;
        }
        fclose($csvFile);
    }
}
