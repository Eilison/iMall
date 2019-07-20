<?php

namespace App\Http\Controllers\Admin;

use App\WechatOrder;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

use Redrain\Express\LaravelExpress;
use Wythe\Logistics\Logistics;



class ShipController extends Controller
{
    public function create($id)
    {
        return view('admin.order.ship_create')->with(['id' => $id]);
    }

    public function store(Request $request, $id)
    {
        $order = WechatOrder::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'ship_name' => 'required',

        ],[
            'required' => ':attribute为必填项',
        ],[
            'ship_name' => '快递公司',

        ]);
        if ($validator->fails())
        {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }else{
            $order -> ship_name = $request -> input('ship_name');
            $order -> ship_number = $request -> input('ship_number');
            $order -> ship_status = '已发货';
            $order -> save();
            return redirect('admin/order');
        }

    }

    public function edit($id)
    {
        $order = WechatOrder::findOrFail($id);
        return view('admin.order.ship_edit',compact('order'));
    }

    public function update(Request $request,$id)
    {
        $order = WechatOrder::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'ship_name' => 'required',
            'ship_number' => 'required|integer',
        ],[
            'required' => ':attribute为必填项',
            'integer' => ':attribute请填写整数',
        ],[
            'ship_name' => '快递公司',
            'ship_number' => '快递单号',
        ]);
        if ($validator->fails())
        {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }else{
            $order -> ship_name = $request -> input('ship_name');
            $order -> ship_number = $request -> input('ship_number');
            $order -> save();
            return redirect('admin/order');
        }
    }


    public function getship($id)
    {
        $order = WechatOrder::findOrFail($id);
        $ship_number = $order->ship_number;
        $logistics = new Logistics();
        $getship = $logistics->query($ship_number);

        return $getship;

    }

    public function getshipping($id)
    {
        $order = WechatOrder::findOrFail($id);
        $ship_number = $order->ship_number;
        $express = new LaravelExpress();
        $ex = $express->getExpressInfoByNo($ship_number);

        return $ex;
    }


    /**
     * 获取实时获取订单物流 什么时候打开的..就什么时候返回数据
     */
    public function allTimeOrder($id)
    {
        $order = WechatOrder::findOrFail($id);
        $post_data = array();
        $post_data["customer"] = env('JaSFqmtj8147');//平台的客户编码
        $key = env('wx168c88f044bc6384');//平台key
        $post_data["param"] = '{"com":"' . 'shentong' . '","num":"' . $order->ship_number .
            '","to":"' . $order->province .$order->city.$order->district. $order->address.'" }';
        $url = 'http://poll.kuaidi100.com/poll/query.do';
        $post_data["sign"] = md5($post_data["param"] . $key . $post_data["customer"]);
        $post_data["sign"] = strtoupper($post_data["sign"]);
        $o = "";
        foreach ($post_data as $k => $v) {
            $o .= "$k=" . urlencode($v) . "&";        //默认UTF-8编码格式
        }
        $post_data = substr($o, 0, -1);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);//地址
        curl_setopt($ch, CURLOPT_POST, 1);//请求方式为post
        curl_setopt($ch, CURLOPT_HEADER, 0);//不打印header信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//返回结果转成字符串
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);//post传输的数据。
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;

    }

    public function ship($id)
    {
        $order = WechatOrder::findOrFail($id);
        $waybill = new \Kuaidi\Waybill(
            $order->ship_number,
            'shentong'
        );
        (new \Kuaidi\Trackers\Kuaidi100)->track($waybill);
        $ex = $waybill->getTraces();
        return $ex;
    }

}
