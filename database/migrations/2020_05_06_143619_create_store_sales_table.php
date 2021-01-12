<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('created_by')->default(1);
            $table->integer('updated_by')->default(1);
            $table->string('no_faktur');
            $table->string('ref_number');
            $table->integer('member_id')->default(0);
            $table->integer('warehouse_id')->default(0);
            $table->dateTime('tanggal_jual');
            $table->string('note')->nullable();
            $table->decimal('total_belanja', 30, 2)->default(0);
            $table->decimal('potongan_simpati', 30, 2)->default(0);
            $table->decimal('total_bayar', 30, 2)->default(0);
            $table->decimal('utang')->default(0);
            $table->boolean('status')->default(1);
            $table->boolean('status_pembayaran')->default(1);
            $table->integer('journal_id')->default(0);
            $table->integer('region_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_sales');
    }
}