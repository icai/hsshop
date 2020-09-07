<template>
  <div class="echarts-line">
    <div class="echarts-line__topper">
      <span v-for="(item, index) in dayTab" :key="index"
            :class="activeIndex === item.key ? 'tab-active' : ''"
            @click="handleTabDay(index)">
          {{item.text}}
      </span>
    </div>
    <div class="echarts-main" id="echart-line">
    </div>
  </div>
</template>
<script>
// 引入图标插件
import echarts from 'echarts'
import Option from './lineOption'
// import TimeDay from './time'

const dayTab = [{
  text: '7天',
  key: 0
}, {
  text: '30天',
  key: 1
}, {
  text: '90天',
  key: 2
}]
export default {
  data() {
    return {
      dayTab,
      // 当前点击的tab
      activeIndex: 0
    }
  },
  methods: {
    /**
     * 点击天数tab的时候
     * @param {Number} index 索引
    */
    handleTabDay(index) {
      this.activeIndex = index
      this.$emit('handleDayTab', index)
    },
    /**
     * 根据数据画图标
    */
    handleDrawLine(seriesData) {
      echarts.dispose(document.getElementById('echart-line'))
      let lineEcharts = echarts.init(document.getElementById('echart-line'))
      const tooltip = {
        trigger: 'axis',
        confine: false,
        formatter: function(params) {
          return `营收统计:${params[0].value}`
        }
      }
      const series = [{
        name: '营收统计',
        type: 'line',
        smooth: true,
        symbolSize: 0,
        label: {
          show: true
        },
        data: seriesData.setIncome
      }]
      // console.log(Option.xAxis[0].data)
      let options = Object.assign(Option, {tooltip}, {series})
      lineEcharts.setOption(options)
    },
    /*
    * 动画显示
    * */
  }
}
</script>

<style lang="stylus" rel="stylesheet/stylus" scoped>
.echarts-line
  color #fff;
  width 95%;
  padding-top 11px;
  margin:0 auto;
  &__topper
    width 234px;
    height 30px;
    margin 0px auto 17px;
    display flex;
    justify-content space-between;
    align-items center;
    background rgba(255, 255, 255, .3)
    color rgba(255, 255, 255, .5)
    border-radius 30px;
    & >  span
      padding 2px 20px;
    & > .tab-active
      background rgba(255, 255, 255, 1)
      color #FF5C59;
      opacity 1;
      border-radius 30px;
      position relative;
      &:after
        position absolute;
        box-sizing border-box;
        right 12px;
        top 15px;
        content '';
        width 3px;
        height 3px;
        border-radius 50%;
        background #FF5C59;
      &:before
        position absolute;
        box-sizing border-box;
        left 12px;
        top 15px;
        content '';
        width 3px;
        height 3px;
        border-radius 50%;
        background #FF5C59;
.echarts-main
  width 100%;
  height 175px;
</style>
