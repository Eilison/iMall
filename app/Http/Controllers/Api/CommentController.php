<?php

namespace App\Http\Controllers\Api;

use App\ProductComment;
use App\WechatOrderDetail;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function comment(Request $request,$id)
    {
        $follow = session()->get('wechat.oauth_user');
        $commodity = WechatOrderDetail::where('order_id','=',$id)->first();
            $comment = $request->input('comment');
            $commodity_comment = new ProductComment();
            $commodity_comment->openid = $follow->id;
            $commodity_comment->order_id = $id;
            $commodity_comment->content = $comment;
            $commodity_comment->nickname = $follow->name;
            $commodity_comment->commodity_id = $commodity->commodity_id;
            if ($commodity_comment->save()) {
                return response()->json([
                    'code' => 0,
                    'message' => '评价成功'
                ]);
            } else {
                return response()->json([
                    'code' => -1,
                    'message' => '评价失败'
                ]);
            }

    }

    public function testComment($id)
    {
        $data = ProductComment::where('order_id','=',$id)->first();
        if (!empty($data)){
            return response()->json([
                'code' => -1,
            ]);
        }else{
            return response()->json([
                'code' => 0,
            ]);
        }
    }
}
