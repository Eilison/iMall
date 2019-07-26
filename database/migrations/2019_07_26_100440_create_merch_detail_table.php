<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merch_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('merch_id');
            $table->string('company_name')->nullable();
            $table->string('company_img')->nullable();
            $table->string('merch_name');
            $table->string('merch_contacts');
            $table->string('merch_phone');
            $table->string('merch_content');
            $table->string('merch_img')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('merch_detail');
    }
}
