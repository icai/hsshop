<template>
  <div>
    <div class="top-product" v-for="(item, index) in topList" :key="index">
      <div class="top-product__image">
        <img :src="item.imgUrl" alt="">
        <span class="better-three" v-if="index < 3">{{index}}</span>
      </div>
      <div class="top-product__detail">
        <div>{{item.name}}</div>
        <div>
          <p class="clearfix">
            <b>浏览人数:</b>
            <span class="blue-span">{{item.skanNum}}</span>
          </p>
          <p class="clearfix">
            <b>付款人数:</b>
            <span class="red-span">{{item.payNum}}</span>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
const topList = [{
  name: '我是商品1111111我不是张锋啊',
  imgUrl: '../../../../assets/images/logo.png',
  skanNum: '200000',
  payNum: '1234567'
}, {
  name: '我是商品1111111我不是张锋啊',
  imgUrl: '../../../../assets/images/logo.png',
  skanNum: '200000',
  payNum: '1234567'
}, {
  name: '我是商品1111111我不是张锋啊',
  imgUrl: '../../../../assets/images/logo.png',
  skanNum: '200000',
  payNum: '1234567'
}, {
  name: '我是商品1111111我不是张锋啊',
  imgUrl: '../../../../assets/images/logo.png',
  skanNum: '200000',
  payNum: '1234567'
}]
export default {
  data() {
    return {
      topList
    }
  }
}
</script>

<style lang="stylus" rel="stylesheet/stylus" scoped>
.top-product
  height 80px
  display flex;
  padding 8px 12px 0;
  background #fff;
  &__image
    width 65px;
    height 65px;
    border-radius 1px;
    margin-right 10px;
    position relative;
    img
      width 100%;
      height 100%;
  &__detail
    width calc(100% - 65px)
    border-bottom 1px solid #E5E5E5;
    font-size 12px;
    & > div:nth-child(1)
      width 160px;
      float left;
      text-overflow ellipsis;
      overflow hidden;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
    & > div:nth-child(2)
      float right;
      width 130px;
      & > p
        line-height 30px;
        b
          font-weight normal;
          color #999;
          float left;
        span
          font-size 14px;
          float right;
        .blue-span
          color #3197FA;
        .red-span
          color #FF2C40;
  .better-three
    width 20px;
    height 20px;
    position absolute;
    color #fff;
    background url('../../../../assets/images/triangle.png') no-repeat 0 0;
    background-size cover;
    font-size 12px;
    padding-left 1px;
    line-height 13px;
</style>
