<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdjustingJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adjusting_journals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('created_by')->default(1);
            $table->integer('updated_by')->default(1);
            $table->dateTime('transaction_date');
            $table->string('reference_number');
            $table->string('name');
            $table->tinyInteger('type');
            $table->boolean('edited')->default(0);
            $table->tinyInteger('unit')->default(0);
            $table->integer('close_monthly_book_id')->default(0);
            $table->integer('close_yearly_book_id')->default(0);
            $table->integer('member_id')->default(0);
            $table->integer('warehouse_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adjusting_journals');
    }
}