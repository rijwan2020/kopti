<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            LevelsTableSeeder::class,
            PositionsTableSeeder::class,
            AssetCategorySeeder::class,
            RegionsTableSeeder::class,
            AccountsTableSeeder::class,
            DepositTypesSeeder::class,
            AccounGroupSeeder::class,
            ShuConfigSeeder::class,
            SuplierSeeder::class,
            WarehouseSeeder::class,
            ExampleSeeder::class
        ]);
    }
}