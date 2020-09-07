<template>
  <div class="echarts-line">
    <div class="echarts-title">
      <p>浏览人数</p>
      <p>12345</p>
    </div>
    <div class="echarts-main" id="echart-line"></div>
    <p class="bar-text">{{day}}</p>
  </div>
</template>
<script>
// 引入图标插件
import echarts from 'echarts'
import Option from './barLine'
import TimeDay from '../../../../utils/time'

let lineEcharts
let dayData = new TimeDay()
export default {
  data() {
    return {
      // 当前点击的tab
      activeIndex: 0,
      seriesData: [10, 120, 301, 120, 300, 400, 30],
      dayArray: [],
      day: 0
    }
  },
  mounted() {
    // 点击的时候获取最近七天的数组
    this.dayArray = dayData.laterSenvenDay(7).slice(0)
    this.day = this.dayArray[3]
    this.handleDrawLine(this.seriesData)
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
      lineEcharts = echarts.init(document.getElementById('echart-line'))
      const tooltip = {
        trigger: 'none'
      }
      const series = [{
        name: '下单笔数',
        type: 'bar',
        barWidth: '7px',
        itemStyle: {
          background: '#fff',
          opacity: 0.5,
          barBorderRadius: [7, 7, 7, 7]
        },
        data: seriesData
      }]
      Option.xAxis[0].data = this.dayArray.slice(0)
      let options = Object.assign(Option, {tooltip}, {series})
      lineEcharts.setOption(options)
      lineEcharts.on('click', (params) => {
        // 缺少点击变色的逻辑
        this.day = params.name
      })
    }
  }
}
</script>

<style lang="stylus" rel="stylesheet/stylus" scoped>
.echarts-line
  color #fff;
  width 100%;
  padding-top 11px;
  position relative;
.echarts-title
  padding 0 13px;
  font-size 14px;
  & > p:nth-child(2)
    font-size 21px;
    font-weight bold;
.echarts-main
  width 100%;
  height 175px;
.bar-text
  width 100%;
  position absolute;
  bottom 0;
  text-align center;
</style>
