<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoneyIntoWechatFollow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //向表WeChat_follow增加字段money
        Schema::table('wechat_follow',function (Blueprint $table) {
            $table->integer('money')->after('groupid')->nullable();
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
        Schema::table('wechat_follow', function (Blueprint $table) {
            $table->dropColumn('money');
        });
    }
}
