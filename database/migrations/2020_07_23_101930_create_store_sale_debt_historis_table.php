<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreSaleDebtHistorisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_sale_debt_historis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('created_by')->default(1);
            $table->integer('updated_by')->default(1);
            $table->integer('member_id');
            $table->integer('region_id');
            $table->tinyInteger('member_stat')->default(1);
            $table->dateTime('trxdate');
            $table->string('no_ref');
            $table->text('note');
            $table->boolean('tipe');
            $table->decimal('total', 50, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_sale_debt_historis');
    }
}