<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposit_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('created_by')->default(1);
            $table->integer('updated_by')->default(1);
            $table->string('name');
            $table->string('code');
            $table->text('description')->nullable();
            $table->string('account_code');
            $table->tinyInteger('type')->default(0);
            $table->boolean('term_type')->default(0);
            $table->integer('term')->default(0);
            $table->integer('next_code')->default(1);
            $table->longText('contract')->nullable();

            $table->index('code');
            $table->index('account_code');
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
        Schema::dropIfExists('deposit_types');
    }
}