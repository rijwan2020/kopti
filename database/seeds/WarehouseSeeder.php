<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('store_warehouses')->insert([
            [
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'code' => 'G1',
                'name' => 'Toko 2',
                'cp' => 'Ero',
                'phone' => '089656443044',
                'address' => 'Jalaksana'
            ],
            [
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'code' => 'G2',
                'name' => 'Toko 3',
                'cp' => 'Iyan',
                'phone' => '089643746002',
                'address' => 'Kapandayan'
            ],
            [
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'code' => 'G3',
                'name' => 'Toko Clm',
                'cp' => 'Dian',
                'phone' => '081223938559',
                'address' => 'Cilimus'
            ],
            [
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'code' => 'G4',
                'name' => 'Toko CDD',
                'cp' => 'Etin',
                'phone' => '085724007388',
                'address' => 'Cibingbin'
            ],
        ]);
    }
}