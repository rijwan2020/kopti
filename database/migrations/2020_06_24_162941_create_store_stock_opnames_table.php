<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreStockOpnamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_stock_opnames', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('item_detail_id');
            $table->integer('item_id');
            $table->string('code');
            $table->string('name');
            $table->integer('warehouse_id')->default(0);
            $table->decimal('harga_beli', 65, 2)->default(0);
            $table->decimal('qty', 65, 2)->default(0);
            $table->decimal('total_persediaan', 65, 2)->default(0);
            $table->decimal('qty_susut', 65, 2)->default(0);
            $table->decimal('total_susut', 65, 2)->default(0);
            $table->date('tanggal_so');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_stock_opnames');
    }
}