<template>
  <div class="static-bottom">
    <div class="tab" :class="activeIndex === index ? 'tab-active' : '' " v-for="(item, index) in tabList" :key="index" @click="handleStatistic(index)">
      <i class="iconfont" :class="item.icon"></i>
      <span>{{item.tabTitle}}</span>
    </div>
  </div>
</template>
<script>
const tabList = [{
  tabTitle: '营收',
  key: 0,
  icon: 'hs-icon-yingshou'
}, {
  tabTitle: '流量',
  key: 1,
  icon: 'hs-icon-flow'
}, {
  tabTitle: '交易',
  key: 2,
  icon: 'hs-icon-transation'
}, {
  tabTitle: '用户',
  key: 3,
  icon: 'hs-icon-customer'
}]
export default {
  data() {
    return {
      tabList,
      activeIndex: 0
    }
  },
  created() {
    this.activeIndex = this.$route.query.tabIndex || 0
  },
  methods: {
    /**
     * 点击底部栏的时候
    */
    handleStatistic(index) {
      this.activeIndex = index
      switch (index) {
        case 0:
          this.$router.replace({
            name: 'revenue',
            query: {
              tabIndex: index
            }
          })
          break
        case 1:
          this.$router.replace({
            name: 'flow',
            query: {
              tabIndex: index
            }
          })
          break
        case 2:
          this.$router.replace({
            name: 'transation',
            query: {
              tabIndex: index
            }
          })
          break
        case 3:
          this.$router.replace({
            name: 'customer',
            query: {
              tabIndex: index
            }
          })
          break
      }
    }
  }
}
</script>

<style lang="stylus" rel="stylesheet/stylus" scoped>
.static-bottom
  height 49px;
  position fixed;
  left 0;
  right 0;
  bottom 0;
  padding 0 14px;
  background rgba(255, 255, 255, 1)
  display flex;
  justify-content space-between;
  align-items center;

  font-size 16px;
  color #999;
  .tab
    padding 0 15px;
    display flex;
    flex-direction column;
    justify-content center;
    cursor pointer;
    & > i
      text-align center;
      display inline-block;
      font-size 16px;
  .tab-active
    color #3197FA;
</style>
