<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductReturn extends Model
{
    protected $table = 'product_return';

    const TYPE_1 = 10;//拍错了/不想要了
    const TYPE_2 = 20;//商品无法使用
    const TYPE_3 = 30;//与卖家描述不符
    const TYPE_4 = 40;//卖家发货问题
    const TYPE_5 = 50;//物流问题
    const TYPE_6 = 60;//未按约定时间发货
    const TYPE_7 = 70;//其他

    public function getReasonReturn($ind = null)
    {
        $arr = [
            self::TYPE_1 => '拍错了/不想要了',
            self::TYPE_2 => '商品无法使用',
            self::TYPE_3 => '与卖家描述不符',
            self::TYPE_4 => '卖家发货问题',
            self::TYPE_5 => '物流问题',
            self::TYPE_6 => '未按约定时间发货',
            self::TYPE_7 => '其他',
        ];
        if ($ind !== null){
            return array_key_exists($ind,$arr) ? $arr[$ind] : $arr[self::TYPE_7];
        }
        return $arr;
    }

    public function orders(){
        return $this->belongsTo('App\WechatOrder','order_id','id');
    }

}
