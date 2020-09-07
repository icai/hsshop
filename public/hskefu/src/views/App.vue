<template>
  <div id="app" class="clearfix">
    <!-- <span @click="handleDetail">商品详情页</span>
    <span @click="handleStatistic">数据统计页面</span> -->
    <transition name="slide-right"> 
　　　<router-view class="Router"></router-view>
　　</transition>
  </div>
</template>
<style type="text/css">
  #app{
    position:relative;
    height:100%;
  }
  .vux-header .vux-header-left .left-arrow:before {
    border: 0.053333rem solid #000;
  }
  .Router {
       position: absolute;
       width: 100%;
       transition: all .4s ease;
  }

.slide-left-enter,
 .slide-right-leave-active {
     opacity: 0;
    -webkit-transform: translate(100%, 0);
    transform: translate(100%, 0);
}

.slide-left-leave-active,
.slide-right-enter {
     opacity: 0;
    -webkit-transform: translate(100%, 0);
    transform: translate(100% 0);
}
</style>
<style lang="less">
@import '~vux/src/styles/reset.less';
@import '~vux/src/styles/1px.less';
@import '~vux/src/styles/center.less';
</style>
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
  },
  destroyed(){
    alert(444)
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
  background-color: #f5f5f5;
}
</style>
