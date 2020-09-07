<template>
  <div class="revenue">
    <div class="statistic-data">
      <div class="echarts-box">
        <line-statis ref="lineSta" @handleDayTab="handleDayTab"></line-statis>
      </div>
      <div class="total-data">
        <p class="total-data__all">
          <span>¥{{_num(setAmount)}}</span>
          <span>{{dayText}}收入</span>
        </p>
        <p class="total-data__maxday">
          <span>¥{{_num(setMax)}}</span>
          <span>单日最高</span>
        </p>
        <p class="total-data__midday">
          <span>¥{{_num(setPerAmount)}}</span>
          <span>日均</span>
        </p>
      </div>
    </div>
    <div class="statistic-navigator">
      <p class="income">
        <span>总收入</span>
        <span class="income__price">{{_num(setAmountAll)}}元</span>
      </p>
      <!-- <p class="income" @click="hanldeIncomeDetail"> -->
      <p class="income">
        <span>收支明细</span>
        <!-- <span><i class="iconfont hs-icon-you icon-throw"></i></span> -->
      </p>
    </div>
    <div class="statistic-navigator">
      <p class="income" v-for="(item,index) in revenueDetail" :key="index">
        <span>
            {{item['created_at']}}
        </span>
        <span>
          收支:
        </span>
        <span>
          {{item['income']}}
        </span>
      </p>
    </div>
    <static-bottom></static-bottom>
  </div>
</template>
<script>
import app from '../../../utils/time'
import api from '../../../apis/url'
import Option from '../../../components/line/lineOption'
export default {
  data() {
    return {
      dayText: '7天',
      paramsType: '1',
      setAmountAll: '0',
      setAmount: '0',
      setMax: '0',
      setPerAmount: '0',
      revenue: {},
      revenueDetail:[]
    }
  },
  mounted() {
    this._getcustomer(this.paramsType);
  },
  methods: {
    // 点击tab的时候
    handleDayTab(index) {
      this._getcustomer(index + 1)
    },
    // 跳转到收支明细
    hanldeIncomeDetail() {
      this.$router.replace({
        name: 'income-detail'
      })
    },
    _getcustomer(type) {
      let _this = this
      let paramObj = {
        token: app.getToken(this),
        type: type
      }
      _this.$axios.get(app.getHost(this) + api.hsStatistic.revenue, {
        params: {
          parameter: paramObj
        }
      }).then((res) => {
        if (res.errCode === 40000) {
          let data = res.data
          _this.setAmountAll = data.amountAll
          _this.setAmount = data.amount
          _this.setMax = data.max
          _this.setPerAmount = data.perAmount
          _this.revenueDetail = data.detail;
          console.log(_this.revenueDetail)
          _this.revenue.setIncome = []
          _this.revenue.setCreated = []
          _this.dayText = (type - 1) === 0 ? '7天' : (type - 1) === 1 ? '30天' : '90天'
          let lists = data.detail
          for (let i = 0; i < lists.length; i++) {
            _this.revenue.setIncome.push(lists[i].income)
            _this.revenue.setCreated.push(lists[i].created_at)
          }
          Option.xAxis[0].data[0] = _this.revenue.setCreated[0]
          Option.xAxis[0].data[Option.xAxis[0].data.length - 1] = _this.revenue.setCreated[_this.revenue.setCreated.length - 1]
          _this.$refs.lineSta.handleDrawLine(_this.revenue)
        }
      })
    },
    _num(num) {
      let money = 0
      if(num > 10000){
        console.log(num / 10000);
        let sum = (num / 10000) + ''
        money = sum.substring(0,sum.indexOf(".") + 3) + '万'
      }else{
        money = num
      }
      return money
    }
  },
}
</script>

<style lang="stylus" rel="stylesheet/stylus" scoped>
.revenue
  width 100%;
  height 100%;
  background #F5F5F5;
.statistic-data
  height 305px;
  width 100%;
  background -linear-gradient(top, #FF5E5B 40%, #FF403D);
  background -webkit-linear-gradient(top, #FF5E5B 40%, #FF403D);
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
    span
      width :30%;
      text-align:center
</style>
