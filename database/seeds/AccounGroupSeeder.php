<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccounGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('account_groups')->insert([
            [
                'name' => 'Kas dan Bank', //1
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 6
            ],
            [
                'name' => 'Simpanan Jangka Pendek', //2
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 6
            ],
            [
                'name' => 'Piutang Usaha Anggota', //3
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 6
            ],
            [
                'name' => 'Piutang Non Anggota', //4
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 6
            ],
            [
                'name' => 'Piutang Lain Lain', //5
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 6
            ],
            [
                'name' => 'Penyisihan Piutang Tak Tertagih', //6
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 6
            ],
            [
                'name' => 'Persediaan Barang', //7
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 6
            ],
            [
                'name' => 'Pend. yang Masih Harus Diterima', //8
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 6
            ],
            [
                'name' => 'Biaya Dibayar Dimuka', //9
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 6
            ],
            [
                'name' => 'Aktiva Lancar Lainnya', //10
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 6
            ],
            [
                'name' => 'Penyertaan Modal di Inkoti Pusat', //11
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 7
            ],
            [
                'name' => 'Penyertaan Modal di Unit Usaha', //12
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 7
            ],
            [
                'name' => 'Simpanan Pada Koperasi', //13
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 7
            ],
            [
                'name' => 'Simpanan Pada Non Koperasi', //14
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 7
            ],
            [
                'name' => 'Tanah', //15
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 8
            ],
            [
                'name' => 'Bangunan Gedung', //16
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 8
            ],
            [
                'name' => 'Kendaraan', //17
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 8
            ],
            [
                'name' => 'Perlatan dan Perlengkapan Kantor', //18
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 8
            ],
            [
                'name' => 'Sound System', //19
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 8
            ],
            [
                'name' => 'Akumulasi Penyusutan', //20
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 8
            ],
            [
                'name' => 'Hutang Usaha Non Anggota', //21
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 9
            ],
            [
                'name' => 'Hutang Bank', //22
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 9
            ],
            [
                'name' => 'Dana Pembagian SHU', //23
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 9
            ],
            [
                'name' => 'Simpanan Anggota dan Non Anggota', //24
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 9
            ],
            [
                'name' => 'Hutang Lain Lain', //25
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 9
            ],
            [
                'name' => 'Pendapatan yang Ditangguhkan', //26
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 9
            ],
            [
                'name' => 'Biaya yang Masih Harus Dibayar', //27
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 9
            ],
            [
                'name' => 'Titipan Anggota', //28
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 9
            ],
            [
                'name' => 'Hutang Anggota', //29
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 10
            ],
            [
                'name' => 'Dana Pembangunan', //30
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 10
            ],
            [
                'name' => 'Simpanan Pokok Anggota', //31
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 11
            ],
            [
                'name' => 'Simpanan Wajib Anggota', //32
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 11
            ],
            [
                'name' => 'Permodalan Organisasi', //33
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 11
            ],
            [
                'name' => 'Cadangan dari Dana Program Khusus', //34
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 12
            ],
            [
                'name' => 'Cadangan', //35
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 13
            ],
            [
                'name' => 'Sisa Hasil Usaha', //36
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 14
            ],
            [
                'name' => 'Penjualan Pada Anggota', //37
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 15
            ],
            [
                'name' => 'Pendapatan Penjualan Pada Anggota', //38
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 15
            ],
            [
                'name' => 'Penjualan Pada Non Anggota', //39
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 15
            ],
            [
                'name' => 'Diskon Penjualan', //40
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 15
            ],
            [
                'name' => 'Retur Penjualan', //41
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 15
            ],
            [
                'name' => 'Pedapatan Organisasi', //42
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 16
            ],
            [
                'name' => 'Pedapatan Dana Dana', //43
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 17
            ],
            [
                'name' => 'Pendapatan Usaha Anggota', //44
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 18
            ],
            [
                'name' => 'Pedapatan Usaha Non Anggota', //45
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 18
            ],
            [
                'name' => 'Pedapatan Lain Lain', //46
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 1,
                'account_id' => 19
            ],
            [
                'name' => 'Harga Pokok Pembelian', //47
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 20
            ],
            [
                'name' => 'Biaya Usaha', //48
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 21
            ],
            [
                'name' => 'Biaya Organisasi', //49
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 22
            ],
            [
                'name' => 'Biaya Lain Lain', //50
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
                'default' => 1,
                'type' => 0,
                'account_id' => 23
            ],
        ]);
    }
}