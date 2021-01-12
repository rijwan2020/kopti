<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreItemUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_item_uploads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('updated_by');
            $table->integer('created_by')->default(1);
            $table->string('code');
            $table->string('name');
            $table->string('harga_beli');
            $table->integer('harga_jual');
            $table->integer('qty_pusat');
            $table->text('qty_gudang');
            $table->integer('suplier_id');
            $table->date('tanggal_beli');
            $table->date('tanggal_kadaluarsa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_item_uploads');
    }
}