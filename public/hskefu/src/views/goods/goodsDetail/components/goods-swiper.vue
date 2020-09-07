<template>
  <div class="goods-swiper">
    <div class="swiper-container" v-if="listData.length > 1">
      <div class="swiper-wrapper">
        <div v-for="(item, index) in listData" :key="index" class="swiper-slide goods-slide">
          <img :src="item" alt="">
        </div>
      </div>
      <div class="swiper-pagination"></div>
    </div>
    <div class="goods-swiper__single" v-if="listData.length === 1">
      <img :src="listData[0]" width="100%" height="100%" alt="">
    </div>
  </div>
</template>
<script>
import 'swiper/dist/css/swiper.min.css'
import Swiper from 'swiper/dist/js/swiper.js'
// const swiperList = [{
//   value: 'https://ss1.bdstatic.com/70cFvXSh_Q1YnxGkpoWK1HF6hhy/it/u=269782574,3043844483&fm=27&gp=0.jpg',
//   key: 0
// }, {
//   value: 'https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=3798983832,1862568820&fm=27&gp=0.jpg',
//   key: 1
// }]
/* eslint-disable */
let globalSwiper = null
/* eslint-enable */

export default {
  props: {
    listData: {
      type: Array
    }
  },
  data() {
    return {
      // swiperList
    }
  },
  mounted() {
    globalSwiper = new Swiper('.swiper-container', {
      direction: 'horizontal',
      loop: true,
      autoplay: {
        delay: 1500,
        disableOnInteraction: false
      },
      // 如果需要分页器
      pagination: {
        el: '.swiper-pagination'
      }
    })
  }
}
</script>
<style lang="stylus" scoped>
.goods-swiper {
  min-height: 200px;
  margin-left: auto;
  margin-right: auto;
  position: relative;
  overflow: hidden;
  z-index: 1;
  &__single {
    width 100%;
    height 100%;
    & img{
      max-width: 100%;
    }
  }
}
.swiper-container {
  width: 100%;
  height: 375px;
}
.goods-slide {
  position relative;
  img {
    position absolute;
    left 0;
  }
}
</style>
