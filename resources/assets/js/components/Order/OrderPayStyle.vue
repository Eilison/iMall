<template>
    <div id="pay-order-container">
        <p>支付方式：</p>
        <br>
            <label>
                <p><input type="radio" @click="wechatpay" name="wechatpay" value="10">微信支付</p>
                <br>
                <p><input type="radio" @click="moneypay" name="moneypay" value="20">余额支付(￥{{data.money}})</p>
            </label>
        <br>
    </div>
</template>

<script>
    import { Indicator } from 'mint-ui';
    import { Toast } from 'mint-ui';
    export default {
        data(){
            return {
                data:''
            }
        },

        created(){
            this.fetchMoney();
        },

        methods:{
            fetchMoney(){
                let vm = this;
                this.$http.get('/api/money').then(function(response){
                    vm.$set('data',response.data);
                });
            },
            wechatpay:function () {
                let vm = this;
                let itemId = vm.$route.params.hashid;
                vm.$route.router.go({name:'orderpay',params:{'hashid':itemId}});
            },
            moneypay:function () {
                Indicator.open({
                    text: '支付中...'
                });
                let vm = this;
                let itemId = vm.$route.params.hashid;
                vm.$http.post('/api/orderpay/'+itemId).then(response=>{
                    Indicator.close();
                    if(response.data.code == 0){
                        Toast({
                            message: response.data.message
                        });
                    }else{
                        Toast({
                            message: response.data.message
                        });
                    }
                });
            }
        }
    }
</script>
