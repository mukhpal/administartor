<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('users')->insert([
            'first_name'=>'Admin',
            'email'=>'admin@mailinator.com',
            'is_admin'=>'1',
            'password'=> bcrypt('123456'),
        ]);
    }
}
