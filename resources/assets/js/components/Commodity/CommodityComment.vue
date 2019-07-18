<template>
    <form id="suggest-form" @submit.prevent="submitComment()">
        <div class="form-wrapper">
            <title>商品评价</title>
            <mt-field
                    placeholder="商品评价"
                    slot="suggestion"
                    type="textarea"
                    rows="6"
                    :value.sync="comment">
            </mt-field>
        </div>
        <div class="form-wrapper">
            <mt-button :disabled="form_disabled" type="primary" size="large">评价</mt-button>
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
            return{
                comment:'',
                form_disabled:true
            }
        },
        components:{
            Field, Button, Indicator, Toast
        },
        watch:{
            'comment':{
                handler:function(val){
                    val !== '' ? this.$set('form_disabled', false)
                        : this.$set('form_disabled', true);
                }
            }
        },
        methods:{
            submitComment:function(){
                Indicator.open();
                let vm = this;
                let itemId = vm.$route.params.hashid;
                vm.$http.post('/api/comment/'+itemId,{comment:vm.comment}).then(function(response){
                    if(response.data.code === 0){
                        Toast({
                            message: response.data.message
                        });
                        vm.$router.go('/order-list');
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