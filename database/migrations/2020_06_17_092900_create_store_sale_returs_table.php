<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreSaleRetursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_sale_returs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('created_by')->default(1);
            $table->integer('updated_by')->default(1);
            $table->integer('sale_id');
            $table->integer('item_id');
            $table->integer('member_id');
            $table->integer('region_id');
            $table->integer('warehouse_id');
            $table->decimal('qty', 10, 2);
            $table->decimal('harga', 65, 2)->default(0);
            $table->decimal('jumlah', 65, 2)->default(0);
            $table->string('no_ref');
            $table->dateTime('tanggal_transaksi');
            $table->text('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_sale_returs');
    }
}