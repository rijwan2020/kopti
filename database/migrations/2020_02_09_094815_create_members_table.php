<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('created_by')->default(1);
            $table->integer('updated_by')->default(1);
            $table->string('code');
            $table->string('name');
            $table->boolean('gender')->default(0);
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('religion')->nullable();
            $table->string('education')->nullable();
            $table->text('address')->nullable();
            $table->bigInteger('village_id')->default(3206192005);
            $table->integer('district_id')->default(3206192);
            $table->integer('regency_id')->default(3206);
            $table->integer('province_id')->default(32);
            $table->string('phone')->nullable();
            $table->integer('region_id')->default(0);
            $table->string('craftman')->nullable();
            $table->integer('soybean_ration')->default(0);
            $table->string('raw_material')->nullable();
            $table->string('adjuvant')->nullable();
            $table->string('extra_material')->nullable();
            $table->string('production_result')->nullable();
            $table->bigInteger('income')->default(0);
            $table->string('marketing')->nullable();
            $table->string('capital')->nullable();
            $table->string('experience')->nullable();
            $table->string('domicile')->nullable();
            $table->string('place_of_business')->nullable();
            $table->string('production_tool')->nullable();
            $table->string('criteria')->nullable();
            $table->boolean('ho_letter')->default(0);
            $table->boolean('license')->default(0);
            $table->boolean('imb_letter')->default(0);
            $table->boolean('pbb_letter')->default(0);
            $table->boolean('extinguisher')->default(0);
            $table->date('join_date')->nullable();
            $table->date('out_date')->nullable();
            $table->text('dependent')->nullable();
            $table->integer('total_dependent')->default(0);
            $table->integer('total_children')->default(0);
            $table->text('image')->nullable();
            $table->smallInteger('status')->default(1);
            $table->integer('user_id')->default(0);
            $table->boolean('promotion')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}