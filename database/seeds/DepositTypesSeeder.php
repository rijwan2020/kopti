<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepositTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('deposit_types')->insert([
            [
                'name' => 'Simpanan Pokok',
                'code' => 'SP',
                'account_code' => '03.01.01',
                'type' => 1,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ],
            [
                'name' => 'Simpanan Wajib',
                'code' => 'SW',
                'account_code' => '03.01.03',
                'type' => 2,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ],
            [
                'name' => 'Simpanan Sukarela',
                'code' => 'SS',
                'account_code' => '02.01.46',
                'type' => 3,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ],
            [
                'name' => 'Simpanan Wajib Khusus',
                'code' => 'SK',
                'account_code' => '01.01.11',
                'type' => 3,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ],
            [
                'name' => 'Simpanan Anggota',
                'code' => 'SA',
                'account_code' => '03.01.04',
                'type' => 3,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ],
            [
                'name' => 'Permodalan Anggota',
                'code' => 'PA',
                'account_code' => '02.02.02',
                'type' => 3,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ],
            [
                'name' => 'Tabungan Simpati',
                'code' => 'TaSi',
                'account_code' => '02.01.50',
                'type' => 3,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ],
            [
                'name' => 'Tabanas',
                'code' => 'Tbn',
                'account_code' => '02.01.48	',
                'type' => 3,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ],
            [
                'name' => 'Taska',
                'code' => 'Tsk',
                'account_code' => '02.01.47	',
                'type' => 3,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ],
        ]);
    }
}