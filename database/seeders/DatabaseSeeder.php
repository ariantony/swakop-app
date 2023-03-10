<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            SettingSeeder::class,
            GroupSeeder::class,
            // ProductSeeder::class,
            // ProductFixSeeder::class,
            // PriceSeeder::class,
            // TransactionFirstStockSeeder::class,
            // DetailTransactionFirstStockSeeder::class,
            FixProductSeeder::class,
            FixPriceSeeder::class,
            FixTransactionSeeder::class,
            FixDetailSeeder::class,
        ]);
    }
}
