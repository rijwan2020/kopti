<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreItemDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_item_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('created_by')->default(1);
            $table->integer('updated_by')->default(1);
            $table->integer('item_id');
            $table->integer('warehouse_id')->default(0);
            $table->integer('suplier_id')->default(0);
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->integer('purchase_id')->default(0);
            $table->decimal('harga_beli', 20, 2)->default(0);
            $table->integer('qty_awal')->default(0);
            $table->decimal('total', 20, 2)->default(0);
            $table->decimal('qty', 20, 2)->default(0);
            $table->boolean('so')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_item_details');
    }
}