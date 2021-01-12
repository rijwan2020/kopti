<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("store_purchase_details", function (Blueprint $table) {
            $table->integer('qty_susut')->default(0);
            $table->decimal('total_susut', 30, 2)->default(0);
        });
        Schema::table("store_item_details", function (Blueprint $table) {
            $table->integer('qty_susut')->default(0);
            $table->decimal('total_susut', 30, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("store_purchase_details", function (Blueprint $table) {
            $table->dropColumn('qty_susut');
            $table->dropColumn('total_susut');
        });
        Schema::table("store_item_details", function (Blueprint $table) {
            $table->dropColumn('qty_susut');
            $table->dropColumn('total_susut');
        });
    }
}