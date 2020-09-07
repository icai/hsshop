<template>
  <div class="flow">
    <div class="statistic-data">
      <div class="echarts-box">
        <line-flow @handleDayTab="handleDayTab"></line-flow>
      </div>
      <div class="total-man">
        <p class="total-man__num">
          <span>{{ uv }}</span>
          <span><i class="total-icon icon-three"></i>访客数</span>
        </p>
        <p class="total-man__skan">
          <span>{{ pv }}</span>
          <span><i class="total-icon icon-two"></i>浏览量</span>
        </p>
      </div>
    </div>
    <div class="statistic-navigator">
      <!-- <p class="income" @click="hanldeFlowDay"> -->
      <p class="income">
        <!-- <span>每日流量统计</span>
        <span><i class="iconfont hs-icon-you icon-throw"></i></span> -->
        <span>昨日访客</span>
        <span>{{ yesterdayUV }}</span>
      </p>
      <!-- <p class="income" @click="hanldeFlowMonth"> -->
      <p class="income">
        <!-- <span>每月流量统计</span>
        <span><i class="iconfont hs-icon-you icon-throw"></i></span> -->
        <span>昨日浏览量</span>
        <span>{{ yesterdayPV }}</span>
      </p>
      <p class="income">
        <span>昨日商品访客数</span>
        <span>{{ yesterdayPUV }}</span>
      </p>
      <p class="income">
        <span>昨日商品浏览量</span>
        <span>{{ yesterdayPPV }}</span>
      </p>
    </div>
    <!-- <div class="statistic-navigator"> -->
      <!-- <p class="income" @click="handleFlowLook"> -->
      <!-- <p class="income">
        <span>页面浏览排行</span>
        <span><i class="iconfont hs-icon-you icon-throw"></i></span>
      </p>
    </div> -->
    <!-- <div class="statistic-navigator">
      <p class="income">
        <span>商品统计</span>
        <span><i class="iconfont hs-icon-you icon-throw"></i></span>
      </p>
      <p class="income">
        <span>流量扇形图</span>
        <span><i class="iconfont hs-icon-you icon-throw"></i></span>
      </p>
    </div> -->
    <static-bottom></static-bottom>
  </div>
</template>
<script>
import LineFlow from '../../../components/flowLine/index.vue'

export default {
  data() {
    return {
      dayText: '7天',
      uv:0,
      pv:0,
      yesterdayUV:0,
      yesterdayPV:0,
      yesterdayPUV:0,
      yesterdayPPV:0
    }
  },
  components: {
    LineFlow
  },
  methods: {
    // 点击tab的时候
    handleDayTab(index) {
      // this.dayText = index === 0 ? '7天' : index === 1 ? '30天' : '90天'

    },
    // 每日流量统计
    hanldeFlowDay() {
      this.$router.replace({
        name: 'flow-day'
      })
    },
    // 每月流量统计
    hanldeFlowMonth() {
      this.$router.replace({
        name: 'flow-month'
      })
    },
    // 页面浏览排行
    handleFlowLook() {
      this.$router.replace({
        name: 'look-page'
      })
    }
  }
}
</script>

<style lang="stylus" rel="stylesheet/stylus" scoped>
.flow
  width 100%;
  height 100%;
  background #F5F5F5;
.statistic-data
  height 305px;
  width 100%;
  background -linear-gradient(top, #2CE4FF 40%, #2C8FFF);
  background -webkit-linear-gradient(top, #2CE4FF 40%, #2C8FFF);
  .echarts-box
    height 230px;
.total-man
  height 74px;
  display flex;
  align-items center;
  justify-content space-around;
  padding 0 12px;
  border-top 1px solid rgba(255, 255, 255, .5);
  color #fff;
  position relative;
  &:after
    position absolute;
    content '';
    height 24px;
    width 1px;
    background #fff;
    left 50%;
  & > p
    width calc(100% / 2);
    display flex;
    flex-direction column;
    text-align center;
    & > span:nth-child(1)
      font-size 21px;
      font-weight 500;
    & > span:nth-child(2)
      font-size 12px;
      margin-top: 3px;
      position relative;
      .total-icon
        width: 4px;
        height 4px;
        border 1px solid #fff;
        border-radius: 50%;
        position absolute;
        top: 8px;
        left: 48px;
        &:after
          position: absolute;
          content: '';
          height 1px;
          width: 24px;
          right: -5px;
          top 1px;
          background: #fff;
        &:before
          position: absolute;
          content: '';
          height 1px;
          width: 24px;
          left: -4px;
          top 1px;
          background: #fff;
      .icon-two
        background #FF2C40
      .icon-three
        background #09BB07
</style>
