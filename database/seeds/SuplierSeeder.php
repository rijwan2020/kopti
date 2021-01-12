<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('store_supliers')->insert([
            [
                'code' => 'Sup-01',
                'name' => 'H. Rahmat Jakarta',
                'account_code' => '02.01.04',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-02',
                'name' => 'Jaja Jakarta',
                'account_code' => '02.01.05',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-03',
                'name' => 'Sansan Bandung',
                'account_code' => '02.01.06',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-04',
                'name' => 'Daim Solo',
                'account_code' => '02.01.07',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-05',
                'name' => 'PT Sentralmulti',
                'account_code' => '02.01.08',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-06',
                'name' => 'Inkopti',
                'account_code' => '02.01.09',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-07',
                'name' => 'PT Gemabaru Jakarta',
                'account_code' => '02.01.10',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-08',
                'name' => 'Sumber Rezeki',
                'account_code' => '02.01.11',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-09',
                'name' => 'H Wargono',
                'account_code' => '02.01.12',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-10',
                'name' => 'Jateng',
                'account_code' => '02.01.13',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-11',
                'name' => 'Rudi/Puskopti',
                'account_code' => '02.01.15',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-12',
                'name' => 'PT Sekawan Makmur-Anies',
                'account_code' => '02.01.16',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-13',
                'name' => 'H Sobari',
                'account_code' => '02.01.17',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-14',
                'name' => 'Tedy - CRB',
                'account_code' => '02.01.18',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-15',
                'name' => 'Yosep Kusuma',
                'account_code' => '02.01.19',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-16',
                'name' => 'PT Chytiagra Soy/Acun',
                'account_code' => '02.01.20',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-17',
                'name' => 'PT Sentral Multi Agro',
                'account_code' => '02.01.21',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-18',
                'name' => 'PT Teja Berlian',
                'account_code' => '02.01.31',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'code' => 'Sup-19',
                'name' => 'Dipo Star Finance',
                'account_code' => '02.01.32',
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
        ]);
    }
}