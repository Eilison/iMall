<template>
    <form id="suggest-form" @submit.prevent="submitMerch()">
        <div class="order-detail-wrapper" style="margin: 8px">
            注册成为入驻商家
        </div>
        <div class="order-detail-wrapper">
            <div class="uc-address-part">
                <mt-field label="商家名称：" placeholder="请输入商家名称" :value.sync="user_name"></mt-field>
            </div>
            <div class="uc-address-part">
                <mt-field label="商家账号(邮箱)：" placeholder="请输入商家账号" :value.sync="user_mail"></mt-field>
            </div>
            <div class="uc-address-part">
                <mt-field label="密码：" type="password" placeholder="请输入密码" :value.sync="user_password"></mt-field>
            </div>
            <div class="uc-address-part">
                <mt-field label="确认密码：" type="password" placeholder="再次输入密码" :value.sync="user_psd"></mt-field>
            </div>
        </div>
        <div class="order-detail-wrapper" style="margin: 8px">
            <div class="uc-address-part">
                <input type="checkbox" @click="checkboxOnclick(this)"/>我已阅读微信商城mall入驻协议
            </div>
        </div>
        <div class="form-wrapper" style="margin-bottom: 0px">
            <mt-button :disabled="form_disabled" type="primary" size="large">下一步</mt-button>
        </div>
    </form>
</template>

<script>
    import { Field } from 'mint-ui';
    import { Button } from 'mint-ui';
    import { Indicator } from 'mint-ui';
    import { Toast } from 'mint-ui';
    export default{
        data(){
            return {
                form_disabled:true,
                user_name:'',
                user_mail:'',
                user_password:'',
                user_psd:'',
                id:{}
            }
        },
        components:{
            Field, Button, Indicator, Toast
        },
        watch:{
            'user_name':{
                handler:function(val){
                    val !== '' ? this.$set('form_disabled', false)
                        : this.$set('form_disabled', true);
                }
            },
            'user_mail':{
                handler:function(val){
                    val !== '' ? this.$set('form_disabled', false)
                        : this.$set('form_disabled', true);
                }
            },
            'user_password':{
                handler:function(val){
                    val !== '' ? this.$set('form_disabled', false)
                        : this.$set('form_disabled', true);
                }
            },
            'user_psd':{
                handler:function(val){
                    val !== '' ? this.$set('form_disabled', false)
                        : this.$set('form_disabled', true);
                }
            },
        },
        methods:{
            submitMerch:function () {
                Indicator.open();
                let vm = this;
                if (vm.user_password !== vm.user_psd){
                    Toast({
                        message: '密码不一致'
                    });
                    Indicator.close();
                }else {
                    Indicator.open();
                    vm.$http.post('/api/merch/register',
                        {
                            user_name:vm.user_name,
                            user_mail:vm.user_mail,
                            user_password:vm.user_password

                        }).then(function(response){
                        if(response.data.code === 0){
                            vm.$route.router.go({name:'merch-detail',params:{'hashid':response.data.message}});
                        }else{
                            Toast({
                                message: '操作失败'
                            });
                        }
                        Indicator.close();
                    });
                }
            }
        }
    }
</script>
