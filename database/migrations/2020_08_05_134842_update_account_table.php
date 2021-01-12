<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("accounts", function (Blueprint $table) {
            $table->decimal('saldo_tahun_lalu', 30, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table("account_groups", function (Blueprint $table) {
            $table->dropColumn('saldo_tahun_lalu');
        });
    }
}