<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        DB::table('users')->insert([
            [
                'name' => '4 Vision Dev',
                'username' => 'cosmos',
                'email' => 'dev@4visionmedia.com',
                'email_verified_at' => DB::raw('NOW()'),
                'password' => Hash::make('orion'),
                'level_id' => 0,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ],
            [
                'name' => 'Admin',
                'user' => 'admin',
                'email' => 'admin@4visionmedia.com',
                'email_verified_at' => DB::raw('NOW()'),
                'password' => Hash::make('admin'),
                'level_id' => 1,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'name' => 'Rijwan',
                'user' => 'A-0001',
                'email' => '',
                'email_verified_at' => DB::raw('NOW()'),
                'password' => Hash::make('A-0001'),
                'level_id' => 99,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'name' => 'Gudang 1',
                'user' => 'gudang',
                'email' => '',
                'email_verified_at' => DB::raw('NOW()'),
                'password' => Hash::make('123456'),
                'level_id' => 71,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ]
        ]);
    }
}