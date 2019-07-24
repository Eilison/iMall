<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductReturnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_return', function (Blueprint $table) {
            $table->increments('id');
            $table->string('openid');
            // 订单ID
            $table->integer('order_id');
            // 订单总价
            $table->float('order_amount')->default(0.00);
            // 退货原因
            $table->integer('reason_return');
            // 退货说明
            $table->string('content');
            // 快递公司
            $table->string('ship_name');
            // 快递单号
            $table->string('ship_number');
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
        Schema::drop('product_return');
    }
}
