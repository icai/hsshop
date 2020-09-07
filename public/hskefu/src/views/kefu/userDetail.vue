<template>
  <div>
     <common-header title="客户资料" :left-options="{showBack:true}" :right-options="{showMore:false}" />
     <panel :list="list"></panel>
     <group>
       <cell title="订单" v-model="value" is-link :link="target">
        <img slot="icon" width="12" height="14" style="display:block;margin-right:5px;" src="../../assets/images/order.png">
      </cell>
     </group>
  </div>
</template>
<script>
import commonHeader from '../../components/kefu/header/header'
import { Panel,Group,Cell } from 'vux'

import store from '../../store/index';
import {mapGetters} from 'vuex'


//引入UI组件
const components = {
  commonHeader,
  Panel,
  Group,
  Cell,
}

export default {
  components:components,
  store,
  data() {
    return {
      userId:this.$route.query.userId,
      value: '',
      list: [
          {
            src: 'https://hsshop-image-cs.huisou.cn/hsshop/image/2018/07/24/1543142815247511.jpg',
            title: ' '
          }
      ]
    }
  },
  methods: {
    //获取用户信息
    getUserInfo(id){
      let _this = this;
      let params = {userId:id};
      return this.$axios.get('/list/customer/getUserInfo',{params})
    },
    //获取订单数量
    getOrderMult(params){
      let _this = this;
      return this.$axios.get('/list/order/getUserOrders',{params})
    },
    //并发请求
    oneTimeREQ(params){
      let that = this
      this.$axios.all([this.getUserInfo(params.userId), this.getOrderMult(params)])
      .then(this.$axios.spread(function (acct, perms) {
        // 两个请求现在都执行完成
        // console.log(acct,perms)
        if(acct.data.data){
          
          that.list = [{
            src:acct.data.data.headimgurl,
            title:acct.data.data.nickname
          }]
        }
        that.value = perms.data.data.length
      }));
    }
  },
  computed:{
    ...mapGetters(['userList']),
    target(){
      return './orderList?userId='+this.userId
    }
  },
  created(){
    const params = {
      userId:this.userId,
      crm_token:this.userList.crm_token,
      shopId:this.userList.shopId,
      type:1
    }
    this.oneTimeREQ(params)
  },
}
</script>

<style lang="less" scoped>

</style>