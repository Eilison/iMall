<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderStatusIntoWechatOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('wechat_orders',function (Blueprint $table) {
            $table->integer('order_status')->after('order_number')->default(10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('wechat_orders', function (Blueprint $table) {
            $table->dropColumn('order_status');
        });
    }
}
