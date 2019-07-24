<?php

namespace App\Http\Controllers\Admin;

use App\ProductReturn;
use App\WechatFollow;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\WechatOrder;
use App\WechatOrderDetail;
use Redrain\Express\LaravelExpress;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        // unpay unship shiped received ''closed''
        $status = empty($request->input('status')) ? 'unship' : $request->input('status');
        // 封装查询条件
        $data = WechatOrder::with('follow')
            ->where(function ($query) use ($status) {
                switch ($status) {
                    case 'unpay':
                        $query->where('order_status', '=', 10)
                            ->where('pay_status', '=', '未支付');
                        break;
                    case 'shiped':
                        $query->where('order_status', '=', 10)
                            ->where('pay_status', '=', '已支付')
                            ->where('ship_status', '=', '已发货');
                        break;
                    case 'received':
                        $query->where('order_status', '=', 10)
                            ->where('pay_status', '=', '已支付')
                            ->where('ship_status', '=', '已收货');
                        break;
                    case 'refunding':
                        $query->where('order_status', '=', 30);
                        break;
                    case 'refunded':
                        $query->where('order_status', '=', 40);
                        break;
                    case 'closed':
                        // TODO 暂未实现关闭订单功能
                        break;
                    default:
                        $query->where('order_status', '=', 10)
                            ->where('pay_status', '=', '已支付')
                            ->where('ship_status', '=', '未发货');
                        break;
                }
            })->paginate(5);
        return view('admin.order.index')->with([
            'data' => $data,
            'status' => $status,
        ]);
    }

    public function show($id)
    {
        $orderInfo = WechatOrder:: where('id', '=', $id)
            ->with('follow')
            ->with('details')
            ->first();
        $order = WechatOrder::findOrFail($id);
        $ship_number = $order->ship_number;
        $express = new LaravelExpress();
        $laravelexpress = $express->getExpressInfoByNo($ship_number);

        $detail_html = '<td colspan="3">';
        foreach ($orderInfo->details as $detail) {
            $detail_html .= '<img class="commodity-img-url" src="' . $detail->commodity_img . '"/>';
            $detail_html .= '【'.$detail->commodity_number . '】'. $detail->commodity_name . '【'. $detail->buy_number.'件】';
            $detail_html .= '<br><br>';
        }
        $detail_html .= '</td>';

        print $html = <<<EOF
<tr>
    <t.h>订单号：</t.
    h>
    <td class="modal-data-1">{$orderInfo->order_number}</td>
    <th>下单时间：</th>
    <td class="modal-data-2">{$orderInfo->created_at}</td>
</tr>
<tr>
    <th>订单总价：</th>
    <td class="modal-data-3">&yen; {$orderInfo->order_amount}</td>
    <th>商品总价：</th>
    <td class="modal-data-4">&yen; {$orderInfo->commodity_amount}</td>
</tr>
<tr>
    <th>支付状态：</th>
    <td class="modal-data-5">{$orderInfo->pay_status}</td>
    <th>配送状态：</th>
    <td class="modal-data-6">{$orderInfo->ship_status}</td>
</tr>
<tr>
    <th>快递公司：</th>
    <td class="modal-data-7">{$orderInfo->ship_name}</td>
    <th>物流单号：</th>
    <td class="modal-data-8">{$orderInfo->ship_number}</td>
</tr>
<tr>
    <th>收货人姓名：</th>
    <td class="modal-data-9">{$orderInfo->name}</td>
    <th>收货人电话：</th>
    <td class="modal-data-2">{$orderInfo->phone}</td>
</tr>
<tr>
    <th>收货人地址：</th>
    <td class="modal-data-2" colspan="3">{$orderInfo->province} {$orderInfo->city} {$orderInfo->district} {$orderInfo->address}</td>
</tr>
<tr>
    <th>购物清单：</th>
    {$detail_html}
</tr>
<tr>
    <th物流信息：</th>
    <td class="modal-data-2" colspan="3">{$laravelexpress}</td>
</tr>
EOF;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function refunded($id)
    {
        $order = WechatOrder::findOrFail($id);
        $order_amount = $order->order_amount;
        $openid = $order->openid;
        if ($order->order_status = 30){
            $order->order_status = 40;
            if ($order->save()){
                $follow = WechatFollow::where('openid','=',$openid)->first();
                $money = $follow->money;
                $follow->money = $money + $order_amount;
                $follow->save();
                return redirect()->back()->withSuccess('退款成功！');
            }
        }

    }

    public function getreturn(Request $request)
    {
        $return = new ProductReturn();
        $status = empty($request->input('status')) ? 'returning' : $request->input('status');
        $data = WechatOrder::with('follow')
            ->with('returns')
            ->where(function ($query) use ($status) {
                switch ($status) {
                    case 'returning':
                        $query->where('order_status', '=', 50);
                        break;
                    case 'returned':
                        $query->where('order_status', '=', 60);
                        break;
                }
            })->paginate(5);
        return view('admin.order.return')->with([
            'data' => $data,
            'status' => $status,
            'return' => $return
        ]);

    }

    public function toreturn($id)
    {
        $order = WechatOrder::findOrFail($id);
        if (empty($order)){
            return redirect()->back()->withErrors('订单不存在！');
        }else{
            $order->order_status = 60;
            $follow = WechatFollow::where('openid','=',$order->openid)->first();
            $money = $follow->money;
            $follow->money = $money + $order->order_amount;
            if ($order->save() and $follow->save()){
                return redirect()->back()->withSuccess('已同意退货退款');
            }
        }
    }

}
