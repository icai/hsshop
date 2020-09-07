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
import Option from './transLine'
import app from '../../utils/time'
import api from '../../apis/url'

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
      orderCount:[],
      orderPayedCount:[],
      createdAt:[]
    }
  },
  mounted() {
    // this.getTrans(1);
  },
  methods: {
    //获取数据
    getTrans(type){
      let _this = this;
      let paramObj = {
        token: app.getToken(this),
        type: type
      }
      this.$axios.get(app.getHost(this) + api.trans.revenue, {
        params: {
          parameter: paramObj
        }
      }).then((res)=>{
        _this.$parent.count = res.data.count;
        _this.$parent.payCount = res.data.payCount;
        _this.$parent.yesterdayTrans = res.data.yesterday;//昨日统计
        if(res.data.detail.length==7){
          _this.$data.orderCount = res.data.detail.map(v=>v.order_count)
          _this.$data.orderPayedCount = res.data.detail.map(v=>v.order_payed_count)
        }else if(res.data.detail.length==30){
           _this.$data.orderCount = res.data.detail.map(v=>v.order_count).filter((v,i)=>(i+1)%5==0||i==0)
           _this.$data.orderPayedCount = res.data.detail.map(v=>v.order_payed_count).filter((v,i)=>(i+1)%5==0||i==0)
        }else{
           _this.$data.orderCount = res.data.detail.map(v=>v.order_count).filter((v,i)=>(i+1)%15==0||i==0)
           _this.$data.orderPayedCount = res.data.detail.map(v=>v.order_payed_count).filter((v,i)=>(i+1)%15==0||i==0)
        }
        Option.xAxis[0].data[0] = res.data.detail[0].created_at;
        Option.xAxis[0].data[6] = res.data.detail[res.data.detail.length-1].created_at;
        _this.handleDrawLine(_this.$data.orderCount,_this.$data.orderPayedCount);
      })
    },
    /**
     * 点击天数tab的时候
     * @param {Number} index 索引
     */
    handleTabDay(index) {
      this.activeIndex = index
      const data = [8 * (index + 1), 3 * (index + 4), 5 * (index + 9), 8 * (index + 8), 1 * (index + 5), 6 * (index + 3), 10 * (index + 1)]
      // this.handleDrawLine(data)
      this.$emit('handleDayTab', index)
    },
    /**
     * 根据数据画图
    */
    handleDrawLine(orderCount,orderPayedCount) {
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
        name: '下单笔数',
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
            borderColor: '#3197FA',
            color: '#fff'
          }
        },
        data: orderCount
      }, {
        name: '付款订单',
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
        data: orderPayedCount
      },
      // {
      //   name: '发货订单',
      //   type: 'line',
      //   smooth: true,
      //   symbolSize: 3,
      //   lineStyle: {
      //     normal: {
      //       width: 1
      //     }
      //   },
      //   itemStyle: {
      //     normal: {
      //       borderWidth: 3,
      //       borderColor: '#09BB07',
      //       color: '#fff'
      //     }
      //   },
      //   data: [1, 13, 15, 2, 10, 30, 18]}
        ]
      let options = Object.assign(Option, {tooltip}, {series})
      lineEcharts.setOption(options)
    },
    // _loding() {
    //   echarts.dispose(document.getElementById('echart-line'))
    //   let lineEcharts = echarts.init(document.getElementById('echart-line'))
    //   lineEcharts.showLoading({
    //     text: 'loading',
    //     color: '#c23531',
    //     textColor: '#000',
    //     maskColor: 'rgba(255, 255, 255, 0)',
    //     zlevel: 0
    //   });
    // }
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
      color #99A8FF;
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
        background #99A8FF;
      &:before
        position absolute;
        box-sizing border-box;
        left 12px;
        top 15px;
        content '';
        width 3px;
        height 3px;
        border-radius 50%;
        background #99A8FF;
.echarts-main
  width 100%;
  height 175px;
</style>
