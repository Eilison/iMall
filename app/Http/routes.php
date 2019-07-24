<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

Route::auth();

// 控制台
Route::get('/', 'Admin\HomeController@index');

Route::group(['prefix' => 'admin', 'middleware' => 'auth', 'namespace' => 'Admin'], function () {
    // 公众号管理
    Route::group(['prefix' => 'wechat'], function () {
        Route::resource('info', 'WechatInfoController', ['except' => ['create', 'edit']]);
        Route::resource('menu', 'WechatMenuController');
        Route::post('pushMenu', 'WechatMenuController@pushMenu');
        Route::resource('follow', 'WechatFollowController', ['except' => ['create', 'edit', 'show', 'destroy','store']]);
        Route::put('refresh', 'WechatFollowController@refresh');
    });
    // 店铺管理
    Route::group(['prefix' => 'shop'], function () {
        Route::resource('config', 'ShopConfigController', ['except' => ['create', 'edit', 'show', 'destroy']]);
        Route::resource('banner', 'ShopBannerController');
    });
    // 商品管理
    Route::group(['prefix' => 'product'], function () {
        Route::resource('topic', 'ProductTopicController');
        Route::resource('plate', 'ProductPlateController');
        Route::resource('category', 'ProductCategoryController');
        Route::resource('commodity', 'ProductCommodityController');
        // Ajax Get Tree & Table Data
        Route::get('getTreeData', 'ProductCategoryController@treeData');
        Route::get('getTableData', 'ProductCommodityController@tableData');
        // 富文本编辑器上传图片
        Route::post('editorUpload', 'ProductCommodityController@editorUpload');
    });
    // 售后订单
    Route::get('order/getreturn', 'OrderController@getreturn');
    // 同意售后
    Route::get('order/toreturn/{id}', 'OrderController@toreturn');
    // 订单管理
    Route::resource('order', 'OrderController', ['except' => ['create']]);
    // 确认订单退款
    Route::get('order/refunded/{id}', 'OrderController@refunded');

    // 增加快递公司及单号
    Route::get('ship/create/{id}', 'ShipController@create');
    Route::post('ship/store/{id}', 'ShipController@store');
    Route::get('ship/edit/{id}', 'ShipController@edit');
    Route::post('ship/update/{id}', 'ShipController@update');
    Route::any('ship/getshipping/{id}', 'ShipController@ship');
});

// DEBUG
Route::get('/wechat/debug', 'WechatController@debug');

// Wechat http main route
Route::any('/wechat', 'WechatController@serve');

// 微信商城
Route::group(['prefix' => 'mall', 'middleware' => ['web', 'wechat.oauth'], 'namespace' => 'Mall'], function () {
    // Wechat OAuth2.0 (type=snsapi_userinfo)
    Route::get('/user', 'IndexController@oauth');
    // 首页
    Route::get('/', 'IndexController@index');
});

Route::group(['prefix' => 'api', 'middleware' => 'web', 'namespace' => 'Api'], function () {
    // 获取用户信息
    Route::get('userinfo', 'UserController@userinfo');
    // 商铺配置
    Route::get('shopconfig', 'ShopController@shopconfig');
    // 获取首页轮播图、专题、板块数据和推荐商品
    Route::get('banners', 'ShopController@getBanners');
    Route::get('topics', 'ShopController@getTopics');
    Route::get('plates', 'ShopController@getPlates');
    Route::get('products', 'ShopController@getProduct');
    // 数据分类数据
    Route::get('categories', 'ShopController@getCategories');
    // 根据不同条件获取商品数据集合
    Route::post('commodities/topic', 'ShopController@getCommodityByTopic');
    Route::post('commodities/plate', 'ShopController@getCommodityByPlate');
    Route::post('commodities/category', 'ShopController@getCommodityByCategory');
    // 根据商品ID查询商品详情数据
    Route::get('commodity/{commodity}', 'ShopController@getCommodity');
    Route::get('commodities/{commodity}', 'ShopController@getCommodities');
    // 购物车
    Route::resource('cart', 'CartController', ['except' => ['create', 'edit', 'show']]);
    // 获取购物车数据总条数
    Route::get('cart/count', 'CartController@calculateTotal');
    Route::post('cart/empty', 'CartController@emptyCart');
    // 创建订单
    Route::post('order', 'OrderController@store');
    // 获取订单数据
    Route::get('order/{order}', 'OrderController@show');
    // 获取订单列表（all,unpay,unreceived）
    Route::get('orderlist/{type}', 'OrderController@index');
    // 获取订单详情
    Route::get('orderdetail/{order}', 'OrderController@detail');
    // 订单支付（余额）
    Route::post('orderpay/{order}', 'OrderController@orderpay');
    // 确认收货
    Route::post('orderreceive/{order}', 'OrderController@receive');
    // 取消订单
    Route::post('ordercancel/{order}', 'OrderController@cancel');
    // 订单退款（用户）
    Route::get('orderrefunding/{order}', 'OrderController@refunding');
    // 判断该订单是否被评价过
    Route::get('testcomment/{id}', 'CommentController@testcomment');
    // 订单评价
    Route::post('comment/{id}', 'CommentController@comment');
    // 获取商品评价
    Route::get('getcomment/{id}', 'CommentController@getcomment');
    // 意见建议
    Route::post('suggestion', 'UserController@suggestion');
    // 地址管理
    Route::get('address', 'UserController@indexAddress');
    Route::post('address', 'UserController@storeAddress');
    Route::get('address/{address}', 'UserController@showAddress');
    Route::put('address/{address}', 'UserController@updateAddress');
    Route::get('default/address', 'UserController@defaultAddress');
    Route::delete('address/{address}', 'UserController@deleteAddress');
    // 获取余额
    Route::get('money', 'UserController@money');
    // 判断是否申请了售后
    Route::get('testreturn/{id}', 'ReturnController@testreturn');
    // 保存退货原因
    Route::post('return/{id}', 'ReturnController@storeReturn');
});