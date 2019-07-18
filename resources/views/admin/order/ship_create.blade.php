@extends('layouts.app')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>订单管理</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="#">订单管理</a>
                </li>
                <li class="active">
                    <strong>增加快递公司及单号</strong>
                </li>
            </ol>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }};</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="wrapper wrapper-content">
        <div class="ibox">
            <div class="ibox-title">
                <h5>增加快递公司及单号</h5>
            </div>
            <div class="ibox-content">
                <form action="{{url('admin/ship/store')}}/{{$id}}" method="post" enctype="multipart/form-data" id="{{$id}}">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label>快递公司：</label>
                        <input name="ship_name" type="text" class="form-control" placeholder="请输入快递公司" required>
                    </div>
                    <div class="form-group">
                        <label>快递单号：</label>
                        <input name="ship_number" type="text" class="form-control" placeholder="请输入快递单号" required>
                    </div>
                    <button type="submit" class="btn btn-primary">保存</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scriptTag')
    <script src="{{asset('js/admin/product/categoryCreate.js')}}"></script>
@endsection