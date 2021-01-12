<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStoreSaleDebtHistorisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("store_sale_debt_historis", function (Blueprint $table) {
            $table->bigInteger('debt_id')->default(0);
            $table->bigInteger('sale_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("store_sale_debt_historis", function (Blueprint $table) {
            $table->dropColumn('debt_id');
            $table->dropColumn('sale_id');
        });
    }
}