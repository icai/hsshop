<template>
  <div id="app" class="clearfix">
    <!-- <span @click="handleDetail">商品详情页</span>
    <span @click="handleStatistic">数据统计页面</span> -->
    <router-view/>
  </div>
</template>

<script>
export default {
  name: 'App',
  methods: {
    handleStatistic() {
      this.$router.replace({
        name: 'flow'
      })
    },
    handleDetail() {
      this.$router.replace({
        name: 'goods-detail'
      })
    }
  }
}
</script>

<style>
#app {
  font-family: 'PingFang-SC-Regular', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  width: 100%;
  height: 100%;
}
</style>
