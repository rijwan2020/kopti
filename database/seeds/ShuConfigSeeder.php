<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShuConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shu_configs')->insert([
            [
                'allocation' => 'Cadangan',
                'account' => '03.03.01',
                'default' => 1,
                'percent' => 25,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'allocation' => 'Dana Pemb Menurut Perbandingan Simpanan',
                'account' => '02.01.42',
                'default' => 1,
                'percent' => 25,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'allocation' => 'Dana Pembagian Menurut Transaksi',
                'account' => '02.01.42',
                'default' => 1,
                'percent' => 20,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'allocation' => 'Dana Pengurus',
                'account' => '02.01.43',
                'default' => 1,
                'percent' => 10,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'allocation' => 'Dana Karyawan',
                'account' => '02.01.44',
                'default' => 1,
                'percent' => 10,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'allocation' => 'Dana Pendidikan',
                'account' => '02.01.39',
                'default' => 1,
                'percent' => 5,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'allocation' => 'Dana Sosial',
                'account' => '02.01.41',
                'default' => 1,
                'percent' => 2.5,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'allocation' => 'Dana Pembangunan Daerah Kerja',
                'account' => '02.01.40',
                'default' => 1,
                'percent' => 2.5,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
        ]);
    }
}