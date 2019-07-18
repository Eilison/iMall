<template>
        推荐
        <div id="list-data-container">
            <div v-for="product in products" class="list-data-wrapper" v-link="{name:'commodity',params:{hashid:product.id}}">
                <img :src="product.commodity_img" alt="product.commodity_name"/>
                <div class="data-info-wrapper">
                    <p class="title">
                        {{product.commodity_name}}
                    </p>
                    <p class="price">
                        &yen;{{product.commodity_current_price}}&emsp;<del>&yen;{{product.commodity_original_price}}</del>
                        <span>剩余：{{product.commodity_stock_number}}件</span>
                    </p>
                </div>
            </div>
        </div>

</template>

<script>
  export default {
      data(){
          return{
              products:[]
          }
      },
      created(){
          this.fetchProduct();
      },
      methods:{
          fetchProduct:function(){
              let vm = this;
              this.$http.get('/api/products').then(function(response){
                  vm.$set('products',response.data);
              });
          }
      },

  }
</script>