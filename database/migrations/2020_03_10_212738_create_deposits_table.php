<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('created_by')->default(1);
            $table->integer('updated_by')->default(1);
            $table->string('account_number');
            $table->integer('member_id');
            $table->integer('region_id');
            $table->integer('deposit_type_id');
            $table->date('registration_date');
            $table->decimal('beginning_balance', 65, 2)->default(0);
            $table->decimal('balance', 65, 2)->default(0);
            $table->timestamp('last_transaction')->nullable();

            $table->integer('deleted_by')->default(0);
            $table->timestamp('deleted_at')->nullable();

            $table->index('account_number');
            $table->index('member_id');
            $table->index('region_id');
            $table->index('deposit_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deposits');
    }
}