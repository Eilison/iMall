<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\WechatFollow;
use App\WechatOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

class HomeController extends BaseController
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::check()){
            // TODO 查询相关公众号报表数据
            $now = Carbon::now();
            $yesterday = Carbon::yesterday();
            //获取订单数据
            //获取今天订单数据
            $Todayorder = WechatOrder::where('pay_status', '=', '已支付')
                                        ->whereBetween('created_at',[date("Y-m-d 00:00:00"),$now])
                                        ->get();
            //获取昨天订单数据
            $Yyday = date("Y-m-d 24:00:00",strtotime("-1 day"));
            $Yesterdayorder = WechatOrder::where('pay_status', '=', '已支付')
                                            ->whereBetween('created_at',[$yesterday,$Yyday])
                                            ->get();
            //获取本月订单数据
            $Monthorder = WechatOrder::where('pay_status', '=', '已支付')
                                        ->whereBetween('created_at',[date("Y-m-01 00:00:00"),$now])
                                        ->get();
            //获取历史订单数据
            $Allorder = WechatOrder::where('pay_status', '=', '已支付')
                                      ->get();

            //获取用户数据
            //获取一周内关注的用户
            $WeekincreaseUser = WechatFollow::where('is_subscribed', '=', '已关注')
                                       ->whereBetween('created_at',[date("Y-m-d 00:00:00",strtotime("-7 day")),$now])
                                       ->get();
            //获取一周内取消关注的用户
            $WeekUnincreaseUser = WechatFollow::where('is_subscribed', '=', '未关注')
                ->whereBetween('created_at',[date("Y-m-d 00:00:00",strtotime("-7 day")),$now])
                ->get();
            //获取一周内净增加的用户
            $NetincreaseUser = ($WeekincreaseUser->count())-($WeekUnincreaseUser->count());
            //获取所有用户
            $Alluser = WechatFollow::all();


            return view('index',compact(
                'Todayorder','Yesterdayorder','Monthorder','Allorder','WeekincreaseUser','WeekUnincreaseUser','$NetincreaseUser','Alluser'));
        }else{
            return Redirect::action('Auth\AuthController@showLoginForm');
        }
    }

}
