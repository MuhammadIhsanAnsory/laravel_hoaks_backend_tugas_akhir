<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        User::create([
            'name' => 'Admin',
            'email' => 'admin@a.com',
            'password' => bcrypt('123123123'),
            'role' => 'admin' 
        ]);

        User::create([
            'name' => 'User',
            'email' => 'user@a.com',
            'password' => bcrypt('123123123'),
            'role' => 'user' 
        ]);
    }
}
