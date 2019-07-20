<template>
    <div id="order-detail-part">
        <div class="order-detail-wrapper">
            <div class="detail-container underline">
                <p class="info">感谢您在iMall购物，欢迎您再次光临！</p>
            </div>
            <div class="detail-container">
                <div v-if="order.order_status === 10">
                    <p v-show="order.pay_status === '未支付'"><span class="title">状  态：</span>{{order.pay_status}}</p>
                    <p v-show="order.pay_status === '已支付'"><span class="title">状  态：</span>{{order.ship_status}}</p>
                </div>
                <div v-else>
                    <p><span class="title">状  态：</span>{{orderStatus(order.order_status)}}</p>
                </div>
                <p><span class="title">订单编号：</span>{{order.order_number}}</p>
                <p><span class="title">下单时间：</span>{{order.created_at}}</p>
            </div>
        </div>
        <div class="order-detail-wrapper">
            <div class="detail-container">
                <p>
                    <span class="title">收货地址：</span>
                </p>
                <p class="address">
                    {{order.province}}{{order.city}}{{order.district}}{{order.address}}
                </p>
                <p class="price"><span class="title">商品金额：</span>&yen;{{order.commodity_amount}}</p>
                <p><span class="title">收货人：</span>{{order.name}} {{order.phone | transformPhone}}</p>
                <p><span class="title">配送方式：</span>{{order.ship_name}} {{order.ship_number}}</p>

            </div>
        </div>
        <div class="order-detail-wrapper">
            <div class="detail-container">
                <div class="commodity-list"
                     v-for="detail in order.details"
                     v-link="{name:'commodity',params:{'hashid':detail.commodity_id}}">
                    <img :src="detail.commodity_img" alt="{{detail.commodity_name}}"/>
                    <div class="commodity-name">
                        <p>{{detail.commodity_name}}</p>
                        <ul>
                            <li class="price">&yen;{{detail.commodity_current_price | transformPrice}}</li>
                            <li class="number">{{detail.buy_number}}件</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="order-detail-wrapper">
            <div class="detail-container underline">
                <div class="order-result">
                    <ul>
                        <li class="result-name">
                          <p>商品总额</p>
                          <p>运费</p>
                        </li>
                        <li class="result-value">
                            <p class="price">&yen;{{order.commodity_amount | transformPrice}}</p>
                            <p class="price">&#43;&yen;{{order.freight_amount | transformPrice}}</p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="detail-container">
                <p class="result-amount">实付金额：<span class="price">&yen;{{order.order_amount | transformPrice}}</span></p>
            </div>
        </div>
    </div>
    <div v-if="order.order_status != 10">

    </div>
    <div v-else>
        <div v-if="order.pay_status == '已支付'">
            <div id="btn-groups-container" v-if="order.ship_status == '已收货'">
                <div class="add-cart-btn" @click="afterSale">售后</div>
                <div class="to-pay-btn" @click="evaluation" >评价</div>
            </div>
            <div id="btn-groups-container" v-else>
                <div class="add-cart-btn" @click="refund">退款</div>
                <div class="to-pay-btn" @click="toReceive">确认收货</div>
            </div>
        </div>
        <div id="btn-groups-container" v-else>
            <div class="add-cart-btn" @click="cancelOrder">取消订单</div>
            <div class="to-pay-btn" @click="toPay">支付</div>
        </div>
    </div>
</template>

s
<script>
    import { Indicator, Toast } from 'mint-ui';
    export default{
        data(){
            return {
                order:{},
            }
        },
        components:{
            Indicator, Toast
        },
        created(){
            this.fetchDetails();
        },
        methods:{
            fetchDetails:function(){
                let vm = this;
                let itemId = this.$route.params.hashid;
                Indicator.open();
                vm.$http.get('/api/orderdetail/'+itemId).then(response=>{
                    Indicator.close();
                    if(response.data.code == -1){
                        Toast({
                            message:response.data.message
                        });
                    }
                    if(response.data.code == 0){
                        vm.$set('order',response.data.message);
                    }
                });
            },
            //确认收货
            toReceive:function () {
                let vm = this;
                let itemId = this.$route.params.hashid;
                Indicator.open();
                vm.$http.post('/api/orderreceive/'+itemId).then(response=>{
                    if(response.data.code == -1){
                        Toast({
                            message:response.data.message
                        });
                    }
                    Indicator.close();
                    if(response.data.code == 0){
                        Toast({
                            message:response.data.message
                        });
                    }
                });
            },
            //支付订单
            toPay:function () {
                Indicator.open({
                    text: '跳转支付...'
                });
                let vm = this;
                let itemId = this.$route.params.hashid;
                Indicator.close();
                vm.$route.router.go({name:'order-paystyle',params:{'hashid':itemId}});
            },
            //取消订单
            cancelOrder:function () {
                let vm = this;
                let itemId = this.$route.params.hashid;
                Indicator.open();
                vm.$http.post('/api/ordercancel/'+itemId).then(response=>{
                    if(response.data.code == -1){
                        Toast({
                            message:response.data.message
                        });
                    }
                    Indicator.close();
                    if(response.data.code == 0){
                        Toast({
                            message:response.data.message
                        });
                    }
                });
            },
            //退款
            refund:function () {
                let vm = this;
                let itemId = this.$route.params.hashid;
                Indicator.open();
                vm.$http.post('/api/orderrefunding/'+itemId).then(response=>{
                    if(response.data.code == -1){
                        Toast({
                            message:response.data.message
                        });
                    }
                    Indicator.close();
                    if(response.data.code == 0){
                        Toast({
                            message:response.data.message
                        });
                    }
                });
            },
            //评价
            evaluation:function () {
                let vm = this;
                let itemId = this.$route.params.hashid;
                Indicator.open();
                vm.$http.get('/api/testcomment/'+itemId).then(response=>{
                    if(response.data.code == -1){
                        Toast({
                            message:'你已评价过该订单'
                        });
                    }
                    Indicator.close();
                    if(response.data.code == 0){
                        vm.$route.router.go({name:'comment',params:{'hashid':itemId}});
                    }
                });
            },
            //售后
            afterSale:function () {

            },

            orderStatus:function (status) {
                switch (status) {
                    case 20:
                        return '已取消';
                        break;
                    case 30:
                        return '退款中';
                        break;
                    case 40:
                        return '已退款';
                        break;
                    case 50:
                        return '售后中';
                        break;
                    case 60:
                        return '已售后';
                        break;
                }
            }
        }
    }
</script>