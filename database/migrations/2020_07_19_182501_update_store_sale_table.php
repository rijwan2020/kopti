<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStoreSaleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("store_sales", function (Blueprint $table) {
            $table->renameColumn("potongan_simpati", "potongan_simpati1");
            $table->bigInteger('potongan_simpati2')->default(0);
            $table->bigInteger('potongan_simpati3')->default(0);
        });
        Schema::table("store_sale_details", function (Blueprint $table) {
            $table->bigInteger('member_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("store_sales", function (Blueprint $table) {
            $table->renameColumn("potongan_simpati1", "potongan_simpati");
            $table->dropColumn('potongan_simpati2');
            $table->dropColumn('potongan_simpati3');
        });
        Schema::table("store_sale_details", function (Blueprint $table) {
            $table->dropColumn('member_id');
        });
    }
}