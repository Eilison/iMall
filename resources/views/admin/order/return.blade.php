@extends('layouts.app')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>订单管理</h2>
        </div>
    </div>

    <div class="wrapper wrapper-content">
        <div class="ibox">
            <div class="ibox-title">
                <h5>售后服务</h5>
            </div>
            <div class="ibox-content">
                <a href="{{url('admin/order/getreturn?status=returning')}}"
                   class="btn {{ $status == 'returning' ? 'btn-primary' : 'btn-default' }} ">
                    售后中
                </a>
                <a href="{{url('admin/order/getreturn?status=returned')}}"
                   class="btn {{ $status == 'returned' ? 'btn-primary' : 'btn-default' }}">
                    已售后
                </a>
                <table class="table table-responsive table-hover">
                    <thead>
                    <tr>
                        <th>头像</th>
                        <th>订单号</th>
                        <th>订单总价</th>
                        <th>订单状态</th>
                        <th>退货原因</th>
                        <th>详细退货原因</th>
                        <th>退货快递公司</th>
                        <th>退货快递单号</th>
                        <th>售后申请时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>

                    @if(!empty($data->items()))
                        @foreach($data->items() as $order)
                            <tr>
                                <td><img class="head-img-url" src="{{$order->follow->headimgurl}}"/></td>
                                <td>{{$order->order_number}}</td>
                                <td>{{$order->order_amount}}</td>
                                <td>{{$order->getOrderStatus($order->order_status)}}</td>
                                <td>{{$return->getReasonReturn($order->returns->reason_return)}}</td>
                                <td>{{$order->returns->content}}</td>
                                <td>{{$order->returns->ship_name}}</td>
                                <td>{{$order->returns->ship_number}}</td>
                                <td>{{$order->returns->created_at}}</td>
                                <td>
                                    <a href="{{url('admin/order/toreturn')}}/{{$order->id}}" id="{{$order->id}}">同意退货</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
                <nav class="text-center">
                    {!! $data->appends(['status'=>$status])->render() !!}
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('scriptTag')
    <script src="{{asset('js/admin/order/orderInfo.js')}}"></script>
@endsection