<template>
    <div id="pay-order-container">
        <p>支付方式：</p>
        <br>
            <label>
                <p><input type="checkbox" @click="wechatpay" name="wechatpay" value="10">微信支付</p>
                <br>
                <br>
                <br>
                <br>
                <p><input type="checkbox" @change="checkOnclick(this)" name="moneypay" value="20">余额支付(￥{{data.money}})</p>
            </label>
        <br>
        <div class="form-wrapper" style="bottom: 0px" v-if="form_disabled == true">
            <mt-button type="primary" size="large" @click="moneypay">提交</mt-button>
        </div>
    </div>
</template>

<script>
    import { Indicator } from 'mint-ui';
    import { Toast } from 'mint-ui';
    import { Button } from 'mint-ui';
    export default {
        data(){
            return {
                data:'',
                form_disabled:false
            }
        },

        created(){
            this.fetchMoney();
        },

        methods:{
            checkOnclick:function(checkbox){
                let vm = this;
                if (vm.form_disabled === false) {
                    vm.$set('form_disabled', true);
                }else{
                    vm.$set('form_disabled', false);
                }
            },
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
                        vm.$route.router.go({name:'order-detail',params:{'hashid':itemId}});
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
