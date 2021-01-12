<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('created_by')->default(1);
            $table->integer('updated_by')->default(1);
            $table->string('code');
            $table->string('name');
            $table->boolean('type');
            $table->tinyInteger('level');
            $table->tinyInteger('parent_id')->default(0);
            $table->boolean('linked')->default(0);
            $table->decimal('beginning_balance', 30, 2)->default(0);
            $table->decimal('debit', 30, 2)->default(0);
            $table->decimal('kredit', 30, 2)->default(0);
            $table->decimal('ending_balance', 30, 2)->default(0);
            $table->decimal('adjusting_balance', 30, 2)->default(0);
            $table->integer('group_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}