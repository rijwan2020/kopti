<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreItemCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_item_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('created_by')->default(1);
            $table->integer('updated_by')->default(1);
            $table->integer('item_id');
            $table->integer('warehouse_id');
            $table->date('tanggal_transaksi');
            $table->text('no_ref');
            $table->decimal('qty', 10, 2);
            $table->text('keterangan')->nullable();
            $table->boolean('tipe')->default(0); // 0 = masuk && 1 = keluar
            $table->decimal('masuk', 65, 2)->default(0);
            $table->decimal('keluar', 65, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_item_cards');
    }
}