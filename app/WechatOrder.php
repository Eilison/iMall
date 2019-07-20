<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WechatOrder extends Model
{
    protected $table = 'wechat_orders';

    protected $guarded = [];

    const STATUS_1 = 10;//正常
    const STATUS_2 = 20;//取消订单
    const STATUS_3 = 30;//退款中
    const STATUS_4 = 40;//已退款
    const STATUS_5 = 50;//售后中
    const STATUS_6 = 60;//已售后

    public function getOrderStatus($ind = null)
    {
        $arr = [
            self::STATUS_1 => '正常',
            self::STATUS_2 => '取消订单',
            self::STATUS_3 => '退款中',
            self::STATUS_4 => '已退款',
            self::STATUS_5 => '售后中',
            self::STATUS_6 => '已售后',
        ];

        if ($ind !== null){
            return array_key_exists($ind,$arr) ? $arr[$ind] : $arr[self::STATUS_1];
        }

        return $arr;
    }

    public function details(){
        return $this->hasMany('App\WechatOrderDetail','order_id','id');
    }

    public function follow(){
        return $this->belongsTo('App\WechatFollow','openid','openid');
    }
}
