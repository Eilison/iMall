<?php

namespace App\Http\Controllers\Api;

use App\WechatFollow;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\WechatAddress;
use App\ProductCommodity;
use App\WechatOrder;
use App\WechatOrderDetail;
use App\ShopConfig;
use App\WechatCart;
use Validator;

class OrderController extends Controller
{
    protected $follow;

    public function __construct()
    {
        $this->follow = session('wechat.oauth_user');
    }

    /**
     * 生成订单号
     * @return string
     */
    private function build_order_no()
    {
        return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    public function store(Request $request)
    {
        // TODO validator
        $openid = $this->follow->id;
        $from = $request->from;
        $order = new WechatOrder;
        $order->openid = $openid;
        $order->name = $request->name;
        $order->phone = $request->phone;
        $order->province = $request->province;
        $order->city = $request->city;
        $order->district = $request->district;
        $order->address = $request->address;
        $goods = $request->commodity;
        // 查询商品基础运费设置信息
        $shop_config = ShopConfig::first();
        // 计算商品总价
        $commodity_amount = 0.00;
        foreach ($goods as $item) {
            $commodity_amount += $item['commodity_current_price'] * $item['cart_num'];
        }
        // 若不满足包邮价格，计算所需邮费
        $freight_amount = 0.00;
        if ($shop_config && $commodity_amount < $shop_config->config_free) {
            $freight_amount = $shop_config->config_freight;
        }
        // 计算订单总价
        $order_amount = $commodity_amount + $freight_amount;
        $order->commodity_amount = $commodity_amount;
        $order->freight_amount = $freight_amount;
        $order->order_amount = $order_amount;
        $order->order_number = $this->build_order_no();

        // 记录订单表，并插入订单明细表
        if ($order->save()) {
            foreach ($goods as $item) {
                $detail = new WechatOrderDetail;
                $detail->order_id = $order->id;
                $detail->openid = $openid;
                $detail->commodity_id = $item['id'];
                $detail->commodity_name = $item['commodity_name'];
                $detail->commodity_img = $item['commodity_img'];
                $detail->commodity_number = $item['commodity_number'];
                $detail->commodity_original_price = $item['commodity_original_price'];
                $detail->commodity_current_price = $item['commodity_current_price'];
                $detail->buy_number = $item['cart_num'];
                $detail->save();
                // 若为购物车订单来源，则删除对应购物车记录
                if ($from == 'cart') {
                    WechatCart::where('openid', '=', $openid)
                        ->where('commodity_id', '=', $item['id'])
                        ->delete();
                }
            }
            return response()->json([
                'code' => 0,
                'message' => $order->id
            ]);
        } else {
            return response()->json([
                'code' => -1,
                'message' => '订单创建失败！'
            ]);
        }
    }

    public function show($id)
    {
        $openid = $this->follow->id;
        $order = WechatOrder::where('id', '=', $id)
            ->where('openid', '=', $openid)->first();
        if ($order) {
            return response()->json([
                'code' => 0,
                'message' => $order
            ]);
        } else {
            return response()->json([
                'code' => -1,
                'message' => '该订单不存在！'
            ]);
        }
    }

    public function index($type)
    {
        $openid = $this->follow->id;
        $page_size = 5;
        switch ($type) {
            case 'all':
                $orders = WechatOrder::with('details')
                    ->where('openid', '=', $openid)
                    ->orderBy('id', 'desc')
                    ->paginate($page_size);
                break;
            case 'unpay':
                $orders = WechatOrder::with('details')
                    ->where('openid', '=', $openid)
                    ->where('order_status','=',10)
                    ->where('pay_status', '=', '未支付')
                    ->orderBy('id', 'desc')
                    ->paginate($page_size);
                break;
            case 'unreceived':
                $orders = WechatOrder::with('details')
                    ->where('openid', '=', $openid)
                    ->where('order_status','=',10)
                    ->where('pay_status', '=', '已支付')
                    ->whereIn('ship_status', ['未发货', '已发货'])
                    ->orderBy('id', 'desc')
                    ->paginate($page_size);
                break;
            case 'received':
                $orders = WechatOrder::with('details')
                    ->where('openid', '=', $openid)
                    ->where('order_status','=',10)
                    ->where('pay_status', '=', '已支付')
                    ->whereIn('ship_status', ['已收货'])
                    ->orderBy('id', 'desc')
                    ->paginate($page_size);
                break;
            default:
                break;
        }
        return response()->json([
            'code' => 0,
            'message' => $orders
        ]);
    }

    public function detail($id)
    {
        $order = WechatOrder::find($id);
        if ($order) {
            $order = WechatOrder::with('details')->find($id);
            return response()->json([
                'code' => 0,
                'message' => $order
            ]);
        } else {
            return response()->json([
                'code' => -1,
                'message' => '订单不存在'
            ]);
        }
    }

    //订单支付（余额）
    public function orderpay($id)
    {
        $order = WechatOrder::findOrFail($id);
        $order_amount = $order->order_amount;
        $follow = session()->get('wechat.oauth_user');
        $wecaht_follow = WechatFollow::where('openid','=',$follow->id)->first();
        $follow_money = $wecaht_follow->money;
        if ($order_amount > $follow_money){
            return response()->json([
                'code' => -1,
                'message' => '你的余额不足'
            ]);
        }else{
            $wecaht_follow->money = $follow_money - $order_amount;
            if ($wecaht_follow->save()){
                $order->pay_status = '已支付';
                $order->save();
                return response()->json([
                    'code' => 0,
                    'message' => '支付成功'
                ]);
            }else{
                return response()->json([
                    'code' => -1,
                    'message' => '支付失败'
                ]);
            }

        }


    }

    //确认收货
    public function receive($id)
    {
        $order = WechatOrder::findOrFail($id);
        if ($order->ship_status = '未收货'){
            $order->ship_status = '已收货';
            $order->save();
            return response()->json([
                'code' => 0,
                'message' => '已经确认收货'
            ]);
        }else{
            return response()->json([
                'code' => -1,
                'message' => '该订单已经收货'
            ]);
        }
    }
    //取消订单
    public function cancel($id)
    {
        $order = WechatOrder::findOrFail($id);
        $order->order_status = 20;
        if ($order->save()){
            return response()->json([
                'code' => 0,
                'message' => '取消订单成功'
            ]);
        }else{
            return response()->json([
                'code' => -1,
                'message' => '取消订单失败'
            ]);
        }
    }
    //退款
    public function refunding($id)
    {
        $order = WechatOrder::findOrFail($id);
        if ($order->order_status = 30){
            return response()->json([
                'code' => -1,
                'message' => '你已经申请过退款了'
            ]);
        }else{
            $order->order_status = 30;
            if ($order->save()){
                return response()->json([
                    'code' => 0,
                    'message' => '订单申请退款成功'
                ]);
            }else{
                return response()->json([
                    'code' => -1,
                    'message' => '订单申请退款失败'
                ]);
            }
        }
    }

}
