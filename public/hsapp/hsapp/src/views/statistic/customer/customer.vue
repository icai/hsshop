<template>
  <div class="customer">
    <div class="statistic-data">
      <div class="echarts-box">
        <line-customer ref='customer' @handleDayTab="handleDayTab" ></line-customer>
      </div>
      <div class="total-data">
        <p class="total-data__all">
          <span>{{newUsersNum}}</span>
          <span><i class="total-icon icon-one"></i>新增粉丝</span>
        </p>
        <p class="total-data__maxday">
          <span>{{cancelUsersNum}}</span>
          <span><i class="total-icon icon-two"></i>跑路粉丝</span>
        </p>
        <p class="total-data__midday">
          <span>{{growthUserNum}}</span>
          <span><i class="total-icon icon-three"></i>净增粉丝</span>
        </p>
      </div>
    </div>
    <div class="statistic-navigator">
      <!-- <p class="income" @click="hanldeIncomeDetail"> -->
      <p class="income">
        <span>累计粉丝</span>
        <span>{{totalFans}}</span>
      </p>
    </div>
    <static-bottom></static-bottom>
  </div>
</template>
<script>
import LineCustomer from '../../../components/custoLine/index.vue'
import app from '../../../utils/time'
import api from '../../../apis/url'
import Option from '../../../components/custoLine/custoLine'

export default {
  data() {
    return {
      // dayText: '7天'
      getData: {},
      totalFans: '0',
      newUsersNum: '0',
      cancelUsersNum: '0',
      growthUserNum: '0',
      paramType: '1'
    }
  },
  components: {
    LineCustomer
  },
  mounted() {
    this._getcustomer(this.paramType)
  },
  methods: {
    // 点击tab的时候
    handleDayTab(index) {
      // this.dayText = index === 0 ? '7天' : index === 1 ? '30天' : '90天'
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
      _this.$axios.get(app.getHost(this) + api.customer.revenue, {
        params: {
          parameter: paramObj
        }
      }).then((res) => {
        if (res.errCode === 40000) {
          let data = res.data
          _this.totalFans = data.totalFans
          _this.newUsersNum = data.newUsersNum
          _this.cancelUsersNum = data.cancelUsersNum
          _this.growthUserNum = data.growthUserNum
          _this.getData.setNewUsersNum = []
          _this.getData.setCancelUsersNum = []
          _this.getData.setGrowthUserNum = []
          _this.getData.setCreatetime = []
          let lists = data.list
          for (let i = 0; i < lists.length; i++) {
            _this.getData.setNewUsersNum.push(lists[i].newUsersNum)
            _this.getData.setCancelUsersNum.push(lists[i].cancelUsersNum)
            _this.getData.setGrowthUserNum.push(lists[i].growthUserNum)
            _this.getData.setCreatetime.push(lists[i].createtime)
          }
          Option.xAxis[0].data[0] = _this.getData.setCreatetime[0]
          Option.xAxis[0].data[Option.xAxis[0].data.length - 1] = _this.getData.setCreatetime[_this.getData.setCreatetime.length - 1]
          _this.$refs.customer.handleDrawLine(_this.getData)
        }
      })
    }
  }
}
</script>

<style lang="stylus" rel="stylesheet/stylus" scoped>
.customer
  width 100%;
  height 100%;
  background #F5F5F5;
.statistic-data
  height 305px;
  width 100%;
  background -linear-gradient(top, #FF8B4D 40%, #FF5533);
  background -webkit-linear-gradient(top, #FF8B4D 40%, #FF5533);
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
