/**
 * 支出明细
 */
<template>
  <div class="income-detail">
    <div class="income-list">
      <p class="income-list__detail">
        <span>下单人数</span>
        <span>{{ msg.orderUserCnt }}</span>
      </p>
      <p class="income-list__detail">
        <span>较前一天</span>
        <span>{{ msg.orderUserCnRate/100 }}%</span>
      </p>
    </div>

    <div class="income-list">
      <p class="income-list__detail">
        <span>下单笔数</span>
        <span>{{ msg.orderCnt }}</span>
      </p>
      <p class="income-list__detail">
        <span>较前一天</span>
        <span>{{ msg.orderCnRate/100 }}%</span>
      </p>
    </div>

    <div class="income-list">
      <p class="income-list__detail">
        <span>下单金额</span>
        <span>{{ msg.orderAmount }}</span>
      </p>
      <p class="income-list__detail">
        <span>较前一天</span>
        <span>{{ msg.orderAmountRate/100 }}%</span>
      </p>
    </div>

    <div class="income-list">
      <p class="income-list__detail">
        <span>付款人数</span>
        <span>{{ msg.payedOrderUserCnt }}</span>
      </p>
      <p class="income-list__detail">
        <span>较前一天</span>
        <span>{{ msg.payedUserCnRate/100 }}%</span>
      </p>
    </div>

    <div class="income-list">
      <p class="income-list__detail">
        <span>付款订单数</span>
        <span>{{ msg.payedOrderCnt }}</span>
      </p>
      <p class="income-list__detail">
        <span>较前一天</span>
        <span>{{ msg.payedOrderCnRate/100 }}%</span>
      </p>
    </div>

    <div class="income-list">
      <p class="income-list__detail">
        <span>付款金额</span>
        <span>{{ msg.payedAmount }}</span>
      </p>
      <p class="income-list__detail">
        <span>较前一天</span>
        <span>{{ msg.payAmountRate/100 }}%</span>
      </p>
    </div>

    <div class="income-list">
      <p class="income-list__detail">
        <span>付款件数</span>
        <span>{{ msg.payedGoodsCnt }}</span>
      </p>
      <p class="income-list__detail">
        <span>较前一天</span>
        <span>{{ msg.goodsRate/100 }}%</span>
      </p>
    </div>

    <div class="income-list">
      <p class="income-list__detail">
        <span>客单价</span>
        <span>{{ msg.payPerUser }}</span>
      </p>
      <p class="income-list__detail">
        <span>较前一天</span>
        <span>{{ msg.payPerUserRate/100 }}%</span>
      </p>
    </div>

  </div>
</template>
<script>

import app from '../../../utils/time'
import api from '../../../apis/url'

const incomeList = [{
  name: '订单结算',
  type: '-',
  num: '0.01',
  time: '17/02/28',
  text: '微信支付'
}, {
  name: '退款',
  type: '+',
  num: '10.01',
  time: '17/02/30',
  text: '微信支付'
}]
export default {
  data() {
    return {
      incomeList,
      msg:{}
    }
  },
  created(){
    this.getParams()
  },
  methods:{
    getParams(){
      let routerParams = this.$route.params;
      this.msg = routerParams;
      console.log(routerParams)
    }
  }
}
</script>

<style lang="stylus" rel="stylesheet/stylus" scoped>
.income-detail
  width 100%;
  height 100%;
  background #f5f5f5;
  padding-top 10px;
  .income-list
    font-size 13px;
    background #fff;
    color #333;
    border-bottom 1px solid #f5f5f5;
    display flex;
    flex-direction column;
    &__detail
      padding 5px 13px 0;
      box-sizing border-box;
      line-height 30px;
      display flex;
      justify-content space-between;
      align-items center;
      .income-list__detail__time
        color #999;
      .red-income
        color #FF2C40;
        font-size 16px;
      .green-income
        color #09BB07;
        font-size 16px;
      span
        width:50%;
        text-align:center
</style>
