<template>
  <div class="transation">
    <div class="statistic-data">
      <div class="echarts-box">
        <line-trans @handleDayTab="handleDayTab"></line-trans>
      </div>
      <div class="total-data">
        <p class="total-data__all">
          <span>{{ count }}</span>
          <span><i class="total-icon icon-one"></i>下单笔数</span>
        </p>
        <p class="total-data__maxday">
          <span>{{payCount}}</span>
          <span><i class="total-icon icon-two"></i>付款订单</span>
        </p>
        <p class="total-data__midday">
          <span>1000</span>
          <span><i class="total-icon icon-three"></i>发货订单</span>
        </p>
      </div>
    </div>
    <div class="statistic-navigator" style="display:none">
      <!-- <p class="income" @click="hanldeIncomeDetail"> -->
      <a href="orderList" class="income">
        <span>订单管理</span>
        <span><i class="iconfont hs-icon-you icon-throw"></i></span>
      </a>
    </div>
    <static-bottom></static-bottom>
  </div>
</template>
<script>

export default {
  data() {
    return {
      dayText: '7天',
      count:0,
      payCount:0,
      yesterdayTrans:{}
    }
  },
  mounted(){
    this.$children[0].getTrans(1)
  },
  methods: {
    // 点击tab的时候
    handleDayTab(index) {
      this.dayText = index === 0 ? '7天' : index === 1 ? '30天' : '90天';
      this.$children[0].getTrans(index+1)
    },
    // 跳转到收支明细
    hanldeIncomeDetail() {
      var _this = this;
      this.$router.push({
        name: 'income-detail',
        params : _this.yesterdayTrans
      })
    }
  }
}
</script>

<style lang="stylus" rel="stylesheet/stylus" scoped>
.transation
  width 100%;
  height 100%;
  background #F5F5F5;
.statistic-data
  height 305px;
  width 100%;
  background -linear-gradient(top, #9BAAFF 40%, #777AFF);
  background -webkit-linear-gradient(top, #9BAAFF 40%, #777AFF);
  .echarts-box
    height 230px;
.statistic-navigator
  margin-top: 10px;
  color #333;
  font-size 16px;
  padding 0 13px;
  background #fff;
  .income
    display flex;
    justify-content space-between;
    align-items center;
    height 44px;
    border-top 1px solid #F5F5F5;
    &__price
      color #FF2C40;
    .icon-throw
      color #999;
</style>
