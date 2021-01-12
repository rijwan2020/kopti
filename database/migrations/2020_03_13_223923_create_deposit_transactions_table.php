<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposit_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('created_by')->default(1);
            $table->integer('updated_by')->default(1);
            $table->integer('deposit_id');
            $table->integer('member_id');
            $table->integer('region_id');
            $table->dateTime('transaction_date');
            $table->decimal('kredit', 20, 2)->default(0);
            $table->decimal('debit', 20, 2)->default(0);
            $table->tinyInteger('type'); // 1 = setor, 2 = tarik, 3 = jasa, 4 = administrasi, 5 = peny setoran, 6 = peny tarikan
            $table->tinyInteger('deposit_type_id');
            $table->string('reference_number');
            $table->bigInteger('journal_id')->default(0);
            $table->text('note')->nullable();

            $table->integer('deleted_by')->default(0);
            $table->timestamp('deleted_at')->nullable();

            $table->index('member_id');
            $table->index('deposit_id');
            $table->index('region_id');
            $table->index('deposit_type_id');
            $table->index('transaction_date');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deposit_transactions');
    }
}