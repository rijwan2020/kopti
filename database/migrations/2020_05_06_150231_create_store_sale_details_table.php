<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreSaleDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_sale_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('created_by')->default(1);
            $table->integer('updated_by')->default(1);
            $table->integer('sale_id');
            $table->integer('item_id');
            $table->decimal('qty', 10, 2);
            $table->decimal('qty_retur', 10, 2)->default(0);
            $table->decimal('harga_jual', 30, 2)->default(0);
            $table->decimal('harga_total_satuan', 30, 2)->default(0);
            $table->json('stocks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_sale_details');
    }
}