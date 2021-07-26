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
         DB::table('admins')->insert([
            'name' => 'Admin',
            'email' => 'admin@mailinator.com',
            'password' => '$2y$10$oCoRTm/bWOHKMZLN0hrgJOji51dIyG8C3/844SUxSVBxDs1pMXJKy',
            'is_super' =>1
        ]);
    }
}
