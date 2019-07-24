<?php

namespace App\Http\Controllers\Api;

use App\ProductReturn;
use App\WechatOrder;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReturnController extends Controller
{
    public function testreturn($id)
    {
        $return = ProductReturn::where('order_id','=',$id)->first();
        if (!empty($return)){
            return response()->json(
                [
                    'code' => -1,
                ]
            );
        }else{
            return response()->json(
                [
                    'code' => 0,
                ]
            );
        }
    }
    public function storeReturn(Request $request,$id)
    {
        $ship_name = $request->ship_name;
        if (empty($ship_name)){
            $rules = [
                'reason_return' => 'required',
                'cause' => 'required',
            ];
            $messages = [
                'reason_return.required' => '请选择退货原因',
                'cause.required' => '请描述退货原因',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if($validator->fails()){
                return response()->json(
                    [
                        'code' => -1,
                        'message' => $validator->errors()->first(),
                    ]
                );
            }else{
                $follow = session()->get('wechat.oauth_user');
                $order = WechatOrder::findOrFail($id);
                $order->order_status = 50;
                $openid = $follow->id;
                $reason_return = new ProductReturn();
                $reason_return->openid = $openid;
                $reason_return->order_id = $id;
                $reason_return->order_amount = $order->order_amount;
                $reason_return->reason_return = $request->reason_return;
                $reason_return->content = $request->cause;
                $reason_return->ship_name = $order->ship_name;
                $reason_return->ship_number = $order->ship_number;
                if ($reason_return->save() and $order->save()){
                    return response()->json([
                        'code' => 0,
                        'message' => '申请售后成功！'
                    ]);
                }else{
                    return response()->json([
                        'code' => -1,
                        'message' => '申请售后失败！'
                    ]);
                }
            }
        }else{
            $rules = [
                'reason_return' => 'required',
                'cause' => 'required',
                'ship_name' =>'required',
                'ship_number' => 'required',
            ];
            $messages = [
                'reason_return.required' => '请选择退货原因',
                'cause.required' => '请描述退货原因',
                'ship_name.required' => '请填写快递公司名',
                'ship_number.required' => '请填写快递单号',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if($validator->fails()){
                return response()->json(
                    [
                        'code' => -1,
                        'message' => $validator->errors()->first(),
                    ]
                );
            }else{
                $follow = session()->get('wechat.oauth_user');
                $order = WechatOrder::findOrFail($id);
                $order->order_status = 50;
                $openid = $follow->id;
                $reason_return = new ProductReturn();
                $reason_return->openid = $openid;
                $reason_return->order_id = $id;
                $reason_return->order_amount = $order->order_amount;
                $reason_return->reason_return = $request->reason_return;
                $reason_return->content = $request->cause;
                $reason_return->ship_name = $request->ship_name;
                $reason_return->ship_number = $request->ship_number;
                if ($reason_return->save() and $order->save()){
                    return response()->json([
                        'code' => 0,
                        'message' => '申请售后成功！'
                    ]);
                }else{
                    return response()->json([
                        'code' => -1,
                        'message' => '申请售后失败！'
                    ]);
                }
            }
        }


    }
}
