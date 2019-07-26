<?php

namespace App\Http\Controllers\Api;

use App\Merch;
use App\MerchDetail;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;

class MerchController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'user_name' => 'required',
            'user_mail' => 'required',
            'user_password' => 'required',
        ];
        $messages = [
            'user_name.required' => '商家名称还未填写',
            'user_mail.required' => '商家账号还未填写',
            'user_password.required' => '密码还未填写',

        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(
                [
                    'code' => -1,
                    'message' => $validator->errors()->first(),
                ]
            );
        }else{
            $merch = new Merch();
            $merch->name = $request->user_name;
            $merch->email = $request->user_mail;
            $merch->password = bcrypt($request->user_password);
            if ($merch->save()){
                $id = $merch->id;
                return response()->json([
                    'code' => 0,
                    'message' => $id,
                ]);
            }else{
                return response()->json([
                    'code' => -1,
                ]);
            }
        }

    }

    public function detail(Request $request,$id)
    {
        $rules = [
            'merch_name' => 'required',
            'merch_contact' => 'required',
            'merch_phone' => 'required',
            'merch_phone' => array('regex:/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/'),
            'merch_content' => 'required',
        ];
        $messages = [
            'merch_name.required' => '店铺名称还未填写',
            'merch_contact.required' => '店铺联系人还未填写',
            'merch_phone.required' => '手机电话还未填写',
            'merch_phone.regex' => '手机号码不合法',
            'merch_content.required' => '店铺详细描述还未填写',

        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(
                [
                    'code' => -1,
                    'message' => $validator->errors()->first(),
                ]
            );
        }else{
            $merchdetail = new MerchDetail();
            $merchdetail->merch_id = $id;
            $merchdetail->merch_name = $request->merch_name;
            $merchdetail->merch_contacts = $request->merch_contact;
            $merchdetail->merch_phone = $request->merch_phone;
            $merchdetail->merch_content = $request->merch_content;
            if ($merchdetail->save()){
                return response()->json([
                    'code' => 0,
                ]);
            }else{
                return response()->json([
                    'code' => -1,
                    'message' => '操作失败'
                ]);
            }
        }
    }
}
