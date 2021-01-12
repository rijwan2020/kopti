<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('positions')->insert([
            [
                'name' => 'Ketua',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'level_id' => 11,
                'type' => 0,
                'default' => 1
            ],
            [
                'name' => 'Wakil Ketua',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'level_id' => 12,
                'type' => 0,
                'default' => 1
            ],
            [
                'name' => 'Sekretaris',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'level_id' => 13,
                'type' => 0,
                'default' => 1
            ],
            [
                'name' => 'Wakil Sekretaris',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'level_id' => 14,
                'type' => 0,
                'default' => 1
            ],
            [
                'name' => 'Bendahara',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'level_id' => 15,
                'type' => 0,
                'default' => 1
            ],
            [
                'name' => 'Wakil Bendahara',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'level_id' => 16,
                'type' => 0,
                'default' => 1
            ],
            [
                'name' => 'Pengawas',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'level_id' => 17,
                'type' => 0,
                'default' => 1
            ],
            [
                'name' => 'Customer Service',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'level_id' => 31,
                'type' => 1,
                'default' => 1
            ],
            [
                'name' => 'Teller',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'level_id' => 32,
                'type' => 1,
                'default' => 1
            ],
            [
                'name' => 'Manager',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'level_id' => 33,
                'type' => 1,
                'default' => 1
            ],
        ]);
    }
}