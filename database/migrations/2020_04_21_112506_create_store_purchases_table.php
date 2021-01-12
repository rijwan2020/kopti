<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStorePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('created_by')->default(1);
            $table->integer('updated_by')->default(1);
            $table->string('no_faktur');
            $table->string('ref_number');
            $table->dateTime('tanggal_beli');
            $table->integer('suplier_id')->default(0);
            $table->text('note')->nullable();
            $table->decimal('total', 20, 2)->default(0);
            $table->decimal('diskon', 20, 2)->default(0);
            $table->decimal('total_bayar', 20, 2)->default(0);
            $table->bigInteger('journal_id')->default(0);
            $table->boolean('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_purchases');
    }
}