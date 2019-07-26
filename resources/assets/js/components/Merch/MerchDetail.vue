<template>
    <form id="suggest-form" @submit.prevent="submitMerchDetail()">
        <div class="order-detail-wrapper" style="margin: 8px">
            店铺信息填写
        </div>
        <div class="order-detail-wrapper">
            <div class="uc-address-part">
                <mt-field label="店铺名称：" placeholder="请输入店铺名称" :value.sync="merch_name"></mt-field>
            </div>
            <div class="uc-address-part">
                <mt-field label="店铺联系人：" placeholder="请输入店铺联系人" :value.sync="merch_contact"></mt-field>
            </div>
            <div class="uc-address-part">
                <mt-field label="店铺电话：" placeholder="请输入店铺电话" :value.sync="merch_phone"></mt-field>
            </div>
        </div>
        <div class="form-wrapper">
            <div style="margin: 10px">
                店铺详细描述
            </div>
            <mt-field
                    placeholder="店铺详细描述"
                    slot="suggestion"
                    type="textarea"
                    rows="6"
                    :value.sync="merch_content">
            </mt-field>
        </div>
        <div class="form-wrapper">
            <mt-button :disabled="form_disabled" type="primary" size="large">提交</mt-button>
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
                merch_name:'',
                merch_contact:'',
                merch_phone:'',
                merch_content:'',
                id:{}
            }
        },
        components:{
            Field, Button, Indicator, Toast
        },
        watch:{
            'merch_name':{
                handler:function(val){
                    val !== '' ? this.$set('form_disabled', false)
                        : this.$set('form_disabled', true);
                }
            },
            'merch_contact':{
                handler:function(val){
                    val !== '' ? this.$set('form_disabled', false)
                        : this.$set('form_disabled', true);
                }
            },
            'merch_phone':{
                handler:function(val){
                    val !== '' ? this.$set('form_disabled', false)
                        : this.$set('form_disabled', true);
                }
            },
            'merch_content':{
                handler:function(val){
                    val !== '' ? this.$set('form_disabled', false)
                        : this.$set('form_disabled', true);
                }
            },
        },
        methods:{
            submitMerchDetail:function () {
                Indicator.open();
                let vm = this;
                let itemId = this.$route.params.hashid;
                Indicator.open();
                vm.$http.post('/api/merch/detail/'+itemId,
                    {
                        merch_name:vm.merch_name,
                        merch_contact:vm.merch_contact,
                        merch_phone:vm.merch_phone,
                        merch_content:vm.merch_content,

                    }).then(function(response){
                    if(response.data.code === 0){
                        Toast({
                            message: '你已成为微信商城mall的入驻商家'
                        });
                    }else{
                        Toast({
                            message: response.data.message
                        });
                    }
                    Indicator.close();
                });
            }
        }
    }
</script>
