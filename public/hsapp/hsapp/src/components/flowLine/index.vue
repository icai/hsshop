<template>
  <div class="echarts-line">
    <div class="echarts-line__topper">
      <span v-for="(item, index) in dayTab" :key="index"
            :class="activeIndex === item.key ? 'tab-active' : ''"
            @click="handleTabDay(index)">{{item.text}}</span>
    </div>
    <div class="echarts-main" id="echart-line"></div>
  </div>
</template>
<script>
// 引入图标插件
import echarts from 'echarts'
import Option from './flowLine'
import app from '../../utils/time'
import api from '../../apis/url'
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
      activeIndex: 0,
      seriesData: {
        data1: [],
        data2: []
      }
    }
  },
  mounted() {
    this.getFlowData(1)
  },
  methods: {
    //获取数据
    getFlowData(type){
      let _this = this;
      let paramObj = {
        token: app.getToken(this),
        type: type
      }
      this.$axios.get(app.getHost(this) + api.flow.revenue, {
        params: {
          parameter: paramObj
        }
      }).then((res)=>{
        _this.$parent.pv = res.data.pv;
        _this.$parent.uv = res.data.uv;
        _this.$parent.yesterdayUV = res.data.yesterday.uv;
        _this.$parent.yesterdayPV = res.data.yesterday.pv;
        _this.$parent.yesterdayPUV = res.data.yesterday.visitProductUv;
        _this.$parent.yesterdayPPV = res.data.yesterday.visitProductPv;
        if(res.data.detail.length==7){
          _this.seriesData.data1 = res.data.detail.map(v=>v.uv);
          _this.seriesData.data2 = res.data.detail.map(v=>v.pv);
        }else if(res.data.detail.length==30){
          _this.seriesData.data1 = res.data.detail.map(v=>v.uv).filter((v,i)=>(i+1)%5==0||i==0);
          _this.seriesData.data2 = res.data.detail.map(v=>v.pv).filter((v,i)=>(i+1)%5==0||i==0);
        }else{
            _this.seriesData.data1 = res.data.detail.map(v=>v.uv).filter((v,i)=>(i+1)%15==0||i==0);
            _this.seriesData.data2 = res.data.detail.map(v=>v.pv).filter((v,i)=>(i+1)%15==0||i==0);
        }
        Option.xAxis[0].data[0] = res.data.detail[0].date;
        Option.xAxis[0].data[6] = res.data.detail[res.data.detail.length-1].date;
        this.handleDrawLine( _this.seriesData)
      })
    },
    /**
     * 点击天数tab的时候
     * @param {Number} index 索引
    */
    handleTabDay(index) {
      this.activeIndex = index;
      this.getFlowData(this.activeIndex+1)
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
        confine: true,
        textStyle: {
          fontSize: 12
        }
      }
      const series = [{
        name: '访客数',
        type: 'line',
        smooth: true,
        symbolSize: 3,
        lineStyle: {
          normal: {
            width: 1
          }
        },
        itemStyle: {
          normal: {
            borderWidth: 3,
            borderColor: '#09BB07',
            color: '#fff'
          }
        },
        data: seriesData.data1
      }, {
        name: '浏览量',
        type: 'line',
        smooth: true,
        symbolSize: 3,
        lineStyle: {
          normal: {
            width: 1
          }
        },
        itemStyle: {
          normal: {
            borderWidth: 3,
            borderColor: '#FF2C40',
            color: '#fff'
          }
        },
        data: seriesData.data2
      }]
      let yAxis = {
        min: 0,
        max: 500,
        axisLine: {
          lineStyle: {
            color: '#fff'
          }
        },
        axisTick: {
          show: false
        },
        axisLabel: {
          show: false,
          formatter: '{value}'
        },
        splitLine: {
          show: false
        }
      }
      let options = Object.assign(Option, {tooltip}, {series}, {yAxis})
      lineEcharts.setOption(options)
    },
  }
}
</script>

<style lang="stylus" rel="stylesheet/stylus" scoped>
.echarts-line
  color #fff;
  width 100%;
  padding-top 11px;
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
      color #2CE0FF;
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
        background #2CE0FF;
      &:before
        position absolute;
        box-sizing border-box;
        left 12px;
        top 15px;
        content '';
        width 3px;
        height 3px;
        border-radius 50%;
        background #2CE0FF;
.echarts-main
  width 100%;
  height 175px;
</style>
