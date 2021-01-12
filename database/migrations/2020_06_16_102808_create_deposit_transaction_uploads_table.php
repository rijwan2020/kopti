<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositTransactionUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposit_transaction_uploads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('created_by')->default(1);
            $table->string('no_rekening');
            $table->integer('deposit_id');
            $table->integer('member_id');
            $table->integer('jenis_transaksi')->default(1);
            $table->string('no_ref')->nullable();
            $table->text('keterangan')->nullable();
            $table->decimal('jumlah', 65, 2);
            $table->dateTime('tanggal_transaksi');
            $table->string('akun')->nullable();
            $table->boolean('jurnal')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deposit_transaction_uploads');
    }
}