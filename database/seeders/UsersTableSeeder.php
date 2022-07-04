<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'id' => 1,
            'name' => 'Admin',
            'email' => 'admin@swakop.app',
            'username' => 'admin',
            'password' => Hash::make('admin'),
        ]);

        User::create([
            'id' => 2,
            'name' => 'Kasir',
            'email' => 'kasir@swakop.app',
            'username' => 'kasir',
            'password' => Hash::make('kasir'),
        ]);
    }
}
