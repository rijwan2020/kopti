<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreSaleDebtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_sale_debts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('created_by')->default(1);
            $table->integer('updated_by')->default(1);
            $table->integer('sale_id');
            $table->integer('warehouse_id')->default(0);
            $table->integer('member_id')->default(0);
            $table->decimal('total', 30, 2)->default(0);
            $table->decimal('pay', 30, 2)->default(0);
            $table->dateTime('tanggal_transaksi');
            $table->date('jatuh_tempo');
            $table->boolean('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_sale_debts');
    }
}