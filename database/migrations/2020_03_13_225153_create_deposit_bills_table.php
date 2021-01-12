<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposit_bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('created_by')->default(1);
            $table->integer('updated_by')->default(1);
            $table->integer('deposit_id');
            $table->integer('member_id');
            $table->integer('region_id');
            $table->integer('deposit_type_id');
            $table->smallInteger('billing_date');
            $table->date('next_bill');
            $table->dateTime('last_transaction');
            $table->decimal('principal_balance', 65, 2)->default(0);
            $table->decimal('obligatory_balance', 65, 2)->default(0);

            $table->integer('deleted_by')->default(0);
            $table->timestamp('deleted_at')->nullable();

            $table->index('member_id');
            $table->index('deposit_id');
            $table->index('region_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deposit_bills');
    }
}