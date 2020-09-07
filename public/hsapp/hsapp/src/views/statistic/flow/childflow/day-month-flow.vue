<template>
  <div class="flow-preview">
    <div class="statistic-data">
      <number-bar></number-bar>
    </div>
    <number-preview></number-preview>
    <p class="top-title">TOP可浏览商品榜</p>
    <top-product></top-product>
  </div>
</template>
<script>
import NumberPreview from '../components/number-preview.vue'
import TopProduct from '../components/top-product.vue'
import NumberBar from '../components/number-bar.vue'

export default {
  components: {
    NumberPreview,
    TopProduct,
    NumberBar
  }
}
</script>
<style lang="stylus" rel="stylesheet/stylus" scoped>
.statistic-data
  height 213;
  width 100%;
  background -linear-gradient(top, #2CE4FF 40%, #2C8FFF);
  background -webkit-linear-gradient(top, #2CE4FF 40%, #2C8FFF);
.flow-preview
  width 100%;
  height 100%;
  background #f5f5f5;
  position relative;
  .top-title
    margin 20px 0px 10px 12px;
</style>
