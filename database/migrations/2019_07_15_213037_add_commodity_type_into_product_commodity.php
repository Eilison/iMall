<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommodityTypeIntoProductCommodity extends Migration
{
    /**
     * Run the migrations.
     * 向表product_commodity增加commodity_type商品属性字段
     * @return void
     */
    public function up()
    {
        Schema::table('product_commodity',function (Blueprint $table) {
            $table->integer('commodity_type')->after('commodity_disabled')->nullable();
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
        Schema::table('product_commodity', function (Blueprint $table) {
            $table->dropColumn('commodity_type');
        });
    }
}
