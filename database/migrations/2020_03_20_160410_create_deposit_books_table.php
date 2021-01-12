<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposit_books', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('created_by')->default(1);
            $table->integer('updated_by')->default(1);
            $table->integer('deposit_id');
            $table->integer('deposit_type_id');
            $table->integer('member_id');
            $table->integer('region_id');
            $table->date('transaction_date');
            $table->decimal('debit', 65, 2)->default(0);
            $table->decimal('kredit', 65, 2)->default(0);
            $table->decimal('balance', 65, 2)->default(0);
            $table->boolean('print')->default(0);
            $table->smallInteger('type_transaction');

            $table->integer('deleted_by')->default(0);
            $table->timestamp('deleted_at')->nullable();

            $table->index('member_id');
            $table->index('region_id');
            $table->index('deposit_type_id');
            $table->index('deposit_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deposit_books');
    }
}