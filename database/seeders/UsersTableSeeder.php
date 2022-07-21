<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $a = User::create([
            'id' => 1,
            'name' => 'Admin',
            'email' => 'admin@swakop.app',
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'shift' => 1,
            'basic_salary' => 2000000,
        ]);

        $b = User::create([
            'id' => 2,
            'name' => 'Kasir',
            'email' => 'kasir@swakop.app',
            'username' => 'kasir',
            'password' => Hash::make('kasir'),
            'shift' => 1,
            'basic_salary' => 2000000,
        ]);

        collect(['admin', 'kasir'])->each(fn ($n) => Role::create([ 'name' => $n, 'guard_name' => 'web' ]));

        $a->roles()->attach([1]);
        $b->roles()->attach([2]);
    }
}
