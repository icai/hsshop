<template>
  <div class="transation" style="background:#f7f7f7">
    <common-header :title="title" :left-options="{showBack:true}" :right-options="{type:'text','title':''}"></common-header>
    <div class='shop_message' v-for="(item,idx) in orderList" :key="idx">
      <div class='shop_msg_user'>
        <div class='shop_msg_user_name'>
          <p>{{userName}}</p>
          <p>{{ item.orderTime }}</p>
        </div>
        <div class='shop_msg_user_order'>
          <p>{{showStatus(item.status)}}</p>
          <p>{{item.orderNumber}}</p>
        </div>
      </div>

      <a :href="'orderDetail/'+item.orderId" class='shop_msg'>
        <div class="shop_msg_img">
          <img :src="item.orderImg" alt="">
        </div>
        <div class="shop_msg_name">
          <p>{{item.orderName}}</p>
          <p>{{item.spec}}</p>
        </div>
      </a>
      <div class='shop_money vux-1px-b'>
        共<span>{{item.buyNum}}</span>件，合计：¥<span>{{item.price}}</span> <span class='shop_money_span'>(含运费:¥<span>{{item.freightPrice}}</span>)</span>
      </div>
      <!-- <div class='shop_remarks'>
        <div></div>
        <p>备注</p>
      </div> -->
      <!-- <div class="shop_action">
        <a :href="'logistics/' + item.orderId" class="btn_itm border_right" v-if="item.status == 2">
          <img src="../../assets/images/ic_see_express.png">
          查看物流
        </a>
        <a :href="'sendGood/' + item.orderId" class="btn_itm border_right" v-if="item.status == 1">
          <img src="../../assets/images/ic_deliver_goods.png">
          发货
        </a>
        <a :href="'info/' + item.orderId" class="btn_itm border_right" v-if="item.status == 1 || item.status == 0 || item.status == 4 || item.status == 3 || item.status == 2">
          <img src="../../assets/images/ic_order_remark.png">
          备注
        </a>
        <a :href="'goodGroup/' + item.groupsId + '/' + item.orderId" class="btn_itm border_right" v-if="item.groupsId > 0">
          <img src="../../assets/images/ic_make_grouped.png">
          使成团
        </a>
        <a :href="'changePrice/' + item.orderId + '/' + item.price" class="btn_itm border_right" v-if="item.status == 0">
          <img src="../../assets/images/ic_change_price.png">
          改价
        </a>
        <a :href="'closeOrder/' + item.orderId" class="btn_itm" v-if="item.status == 0">
          <img src="../../assets/images/ic_order_close.png">
          关闭
        </a>
      </div> -->
    </div>
  </div>
</template>
<script>
import commonHeader from '../../components/kefu/header/header'
import { FormPreview } from 'vux'

import store from '../../store/index';
import {mapGetters} from 'vuex'

import axios from 'axios'

// import {host} from '../../config/env'

export default {
  data() {
    return {
      userId:this.$route.query.userId,
      title: '订单列表',
      orderList:[],
      userName:'',
      
    }
  },
  components: {
    commonHeader
  },
  created() {
    const params = {
      crm_token:this.userList.crm_token,
      shopId:this.userList.shopId,
      userId:this.userId,
      type:1
    }
    this.oneTimeREQ(params)
  },
  methods: {
    //获取用户信息
    getUserInfo(id){
      let _this = this;
      let params = {userId:id};
      return axios.get('/list/customer/getUserInfo',{params})
    },
    //获取订单数量
    getOrderMult(params){
      let _this = this;
      return axios.get('/list/order/getUserOrders',{params})
    },
    //并发请求
    oneTimeREQ(params){
      let _this = this
      axios.all([this.getUserInfo(params.userId), this.getOrderMult(params)])
      .then(axios.spread(function (acct, perms) {
        // 两个请求现在都执行完成
        // console.log(acct,perms)
        _this.userName = acct.data.data?acct.data.data.nickname:'(空)'
        _this.orderList = perms.data.data
      }));
    },
    showStatus(status){
      switch(status){
        case 0: return '待付款';break;
        case 1: return '待发货';break;
        case 2: return '已发货';break;
        case 3: return '已完成';break;
        case 4: return '已关闭';break;
        case 7: return '待抽奖';break;
        default:return 'i dont know';break
      }
    }
  },
  computed:{
    ...mapGetters(['userList'])
  }
}
</script>

<style lang="stylus" rel="stylesheet/stylus" scoped>
  .shop_message
    margin-bottom:0.2rem
    background-color #FFFFFF
    .shop_msg_user
      display flex
      justify-content space-between
      padding 0.75rem 0.812rem
      box-sizing border-box
      .shop_msg_user_name
        p
          &:first-child
            position: relative
            font-size 0.875rem
            color #333333
            padding-left 17px
            font-weight bold
            &::after
              content: ''
              position absolute
              width 0.625rem
              height 0.812rem
              background url("../../assets/images/user-alt@2x.png") no-repeat
              background-size cover
              left 0
              top 0.25rem
          &:last-child
            font-size 0.75rem
            color #666666
            padding-top 0.375rem
      .shop_msg_user_order
        p
          &:first-child
            font-size 0.875rem
            color #333333
            font-weight bold
            text-align right
          &:last-child
            font-size 0.75rem
            color #666666
            padding-top 0.375rem
    .shop_msg
      background-color #F7F7F7
      padding 0.5rem 0 0.75rem 0.75rem
      box-sizing border-box
      display flex
      .shop_msg_img
        width 5.625rem
        img
          width 5.625rem
      .shop_msg_name
        padding-left 1.062rem
        padding-right 1.625rem
        p
          &:first-child
            overflow hidden
            text-overflow ellipsis
            display -webkit-box;
            -webkit-box-orient vertical;
            -webkit-line-clamp 2;
            font-size 0.75rem
            color #333333
            font-weight bold
            line-height 1.4rem
          &:last-child
            color #666666
            font-size 0.75rem
            padding-top 0.812rem
    .shop_money
      padding 0.75rem 0.75rem 0.75rem 0
      text-align right
      font-size 0.875rem
      color #333333
      &::after
        border-bottom-color: #E5E5E5;
        color: #E5E5E5;
      .shop_money_span
        color #666666
        font-size 0.75rem
        padding-left 0.25rem
    .shop_remarks
      height 2.625rem
      display flex
      justify-content center
      align-items center
      div
        width 0.875rem
        height 0.875rem
        background url("../../assets/images/edit@2x.png") no-repeat
        background-size cover
        margin-right 0.437rem
      p
        font-size 0.75rem
        color #333333
    .shop_action
      height 2.625rem
      display flex
      justify-content center
      align-items center
      font-size:18px
      .btn_itm
       display: flex
       align-items: center
       justify-content: center
       flex:1
       color:#000
       text-align:center
       img
        height:25px
        padding-right:6px
    .border_right{
      border-right:1px solid #eee
    }
</style>
