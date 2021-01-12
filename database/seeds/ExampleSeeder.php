<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('members')->insert([
            [
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'code' => 'A-0001',
                'name' => 'Rijwan',
                'gender' => 1,
                'region_id' => 1,
                'join_date' => '2020-03-01',
                'status' => 1,
                'user_id' => 3
            ],
            [
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'code' => 'N-0001',
                'name' => 'Muhammad',
                'gender' => 1,
                'region_id' => 2,
                'join_date' => null,
                'status' => 0,
                'user_id' => 0
            ]
        ]);
        DB::table('store_items')->insert([
            [
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'code' => 'KM',
                'name' => 'Kacang Merah',
                'harga_jual' => 2500,
                'qty' => 100
            ],
            [
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'code' => 'KH',
                'name' => 'Kacang Hijau',
                'harga_jual' => 2500,
                'qty' => 100
            ],
        ]);
        DB::table('store_item_details')->insert([
            [
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'item_id' => 1,
                'warehouse_id' => 0,
                'suplier_id' => 1,
                'tanggal_masuk' => '2020-01-01',
                'tanggal_kadaluarsa' => '2020-12-01',
                'purchase_id' => 0,
                'harga_beli' => 2100,
                'qty_awal' => 10,
                'qty' => 10,
                'total' => 21000,
            ],
            [
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'item_id' => 1,
                'warehouse_id' => 0,
                'suplier_id' => 1,
                'tanggal_masuk' => '2020-01-01',
                'tanggal_kadaluarsa' => '2020-12-02',
                'purchase_id' => 0,
                'harga_beli' => 2000,
                'qty_awal' => 90,
                'qty' => 90,
                'total' => 180000,
            ],
            [
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'item_id' => 2,
                'warehouse_id' => 0,
                'suplier_id' => 1,
                'tanggal_masuk' => '2020-01-01',
                'tanggal_kadaluarsa' => '2020-12-01',
                'purchase_id' => 0,
                'harga_beli' => 2100,
                'qty_awal' => 10,
                'qty' => 10,
                'total' => 21000,
            ],
            [
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'item_id' => 2,
                'warehouse_id' => 0,
                'suplier_id' => 1,
                'tanggal_masuk' => '2020-01-01',
                'tanggal_kadaluarsa' => '2020-12-02',
                'purchase_id' => 0,
                'harga_beli' => 2000,
                'qty_awal' => 90,
                'qty' => 90,
                'total' => 180000,
            ],
        ]);
        DB::table('store_item_cards')->insert([
            [
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'item_id' => 1,
                'warehouse_id' => 0,
                'tanggal_transaksi' => '2020-01-01',
                'no_ref' => 'TRX-00001',
                'qty' => 10,
                'keterangan' => 'Persediaan awal pusat',
                'tipe' => 0,
                'masuk' => 21000,
            ],
            [
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'item_id' => 1,
                'warehouse_id' => 0,
                'tanggal_transaksi' => '2020-01-02',
                'no_ref' => 'TRX-00002',
                'qty' => 90,
                'keterangan' => 'Persediaan awal pusat',
                'tipe' => 0,
                'masuk' => 180000,
            ],
            [
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'item_id' => 2,
                'warehouse_id' => 0,
                'tanggal_transaksi' => '2020-01-01',
                'no_ref' => 'TRX-00001',
                'qty' => 10,
                'keterangan' => 'Persediaan awal pusat',
                'tipe' => 0,
                'masuk' => 21000,
            ],
            [
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'item_id' => 2,
                'warehouse_id' => 0,
                'tanggal_transaksi' => '2020-01-02',
                'no_ref' => 'TRX-00002',
                'qty' => 90,
                'keterangan' => 'Persediaan awal pusat',
                'tipe' => 0,
                'masuk' => 180000,
            ],
        ]);
        DB::table('store_warehouse_users')->insert([
            [
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'warehouse_id' => 1,
                'user_id' => 4
            ]
        ]);
    }
}