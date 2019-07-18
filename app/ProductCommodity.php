<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCommodity extends Model
{
    protected $table = 'product_commodity';

    protected $guarded = [];

    const TYPE_1 = 10;//推荐
    const TYPE_2 = 20;//热销
    const TYPE_3 = 30;//最新
    const TYPE_4 = 40;//包邮

    public function topic(){
        return $this->belongsTo('App\ProductTopic','topic_id');
    }

    public function plate(){
        return $this->belongsTo('App\ProductPlate','plate_id');
    }
    public function category(){
        return $this->belongsTo('App\ProductCategory','category_id');
    }

    public function getProductType($ind = null)
    {
        $arr = [
            self::TYPE_1 => '推荐',
            self::TYPE_2 => '热销',
            self::TYPE_3 => '最新',
            self::TYPE_4 => '包邮',
        ];

        if ($ind !== null){
            return array_key_exists($ind,$arr) ? $arr[$ind] : $arr[self::TYPE_4];
        }

        return $arr;
    }

}
