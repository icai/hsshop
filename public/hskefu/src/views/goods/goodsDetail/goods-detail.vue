<template>
  <div class="goods" v-if="pageShow">
    <!-- 顶部轮播 -->
    <div class="swiper-goods">
      <goods-swiper :listData="swiperList"></goods-swiper>
    </div>
    <div class="goods-detail">
      <p class="goods-detail__name">
        <span class='goods-span-title'>{{goodsData.title}}</span>
        <a :href='hrefData' class='goods-fx'></a>
      </p>
      <span class="goods-detail__price">¥{{goodsData.show_price}}</span>
      <div class="goods-detail__number">
        <span>运费：{{goodsData.default_feight}}</span>
        <span>销量：{{goodsData.sold_num}}</span>
        <span>剩余：{{goodsData.stock}}</span>
      </div>
    </div>
    <div class="goods-norms" @click="handleShowSpec">
      选择: &nbsp;<span>规格</span>
      <i class="iconfont hs-icon-you"></i>
    </div>
    <!-- 商品评价 -->
    <div class="goods-evaluate">
      <!--{{evaluateList.length}}-->
      <div class="goods-evaluate__all" :style = "evaluateList.length > 0 ? '':'border:none'">全部评价({{goodsData.evaluate_num}})<span>查看评价<i class="iconfont hs-icon-you"></i></span></div>
      <goods-evaluate :evaluateList="evaluateList"></goods-evaluate>
    </div>
    <!-- 商品详情 -->
    <div class="goods-intro">
      <p class="goods-intro__title">
        <img src="../../../assets/images/goods-detail.png" alt="">
      </p>
      <div class="goods-intro__detail" v-html="detail">
      </div>
    </div>
    <!-- 规格弹窗 -->
    <div class="goods-spec" v-if="specStatus" @click.self="specStatus = false">
      <goods-format :show_price="show_price" :queryNorms="queryNorms" @handleCloseFormat="specStatus = false"></goods-format>
    </div>
  </div>
</template>
<script>
import goodsSwiper from './components/goods-swiper'
import goodsFormat from './components/goods-format'
import app from '../../../utils/time'
export default {
  data() {
    return {
      pageShow:false,
      title: '商品详情',
      // 商品规格弹窗 默认隐藏
      specStatus: false,
      // 商品详情数据
      goodsData: {},
      // 商品评价列表
      evaluateList: [],
      // 轮播图列表
      swiperList: [],
      detail: '',
      // 商品规格
      queryNorms: [],
      //商品价格
      show_price: '0',
      hrefData: 'share&id='
    }
  },
  components: {
    goodsSwiper,
    goodsFormat
  },
  created() {
    // 接口请求的时候 都是按照封装起来的 类似于模版
    this.hrefData = this.hrefData + this.$route.query.id
    this.$axios.get(app.getHost(this) + this.$apis.goods.productDetail, {
      params: {
        parameter: {
          token: app.getToken(this),
          product_id: this.$route.query.id
        }
      }
    }).then((res) => {
      // 商品基本详情对象
      this.goodsData = res.data
      this.detail = JSON.parse(this.goodsData.content)[0].content
      this.show_price = res.data.show_price
      // 商品评价数组
      this.evaluateList = res.data.evaluate_data.slice(0, 1)
      // 轮播图详情
      this.swiperList = res.data.product_img.slice(0)
      // 请求商品规格
      this.handleQueryNorms()
      this.pageShow = true;
    })
  },
  methods: {
    /**
     * 点击规格弹出规格弹窗
    */
    handleShowSpec(p) {
      this.specStatus = true
    },
    /**
     * 请求商品规格
    */
    // c483412dd0ce72cbb32bce4d23f5bc87
    handleQueryNorms() {
      let _this = this
      const parameter = {
        token: app.getToken(this),
        product_id: this.$route.query.id
      }
      this.$axios.get(app.getHost(this) + this.$apis.goods.productRules, {
        params: {
          parameter
        }
      }).then((res) => {
        console.log(res)
        if (res.errCode == 40000) {
          let props = res.data.props
          // props.foreach(function (v, k) {
          //   _this.queryNorms.push(v)
          // })
          
          // for(let val of props){
          //   _this.queryNorms.push(val)
          // }
        }
      })
    }
  }
}
</script>

<style lang="stylus" rel="stylesheet/stylus" scoped>
.goods
  background #f5f5f5;
  height 100%;
.swiper-goods
  width 100%;
  // background #1d94fa;
  position relative;
.goods-detail
  width calc(100% - 26px);
  // height 137px;
  padding 0 12px;
  text-align left;
  font-size 17px;
  background #fff;
  overflow-x hidden;
  &__name
    line-height 26px;
    padding-top 20px;
    display flex
    justify-content space-between
    .goods-span-title
      width 80%
    .goods-fx
      background url("../../../assets/images/FX@2x.png") no-repeat
      background-size cover
      display inline-block
      width 23px
      height 22px
      margin-right 11px
  &__price
    line-height 1;
    color #FF2C40;
    font-weight 500;
    margin 10px 0 10px 0;
    font-size:1rem;
    display inline-block;
  &__number
    width 100%;
    display flex;
    justify-content space-between;
    line-height 44px;
    font-size 14px;
    color #666;
    position relative;
    &::before
      width 107%;
      height 1px;
      background  #DCDCDC;
      content '';
      position absolute;
      left -13px;
.goods-norms
  min-height 44px;
  margin 10px 0;
  padding 0 12px;
  background #fff;
  line-height 20px;
  color #333;
  font-size 15px;
  line-height 60px;
  position relative;
  & > i
    color #666;
    position absolute;
    right 12px;
.goods-evaluate
  background #fff;
  font-size 16px;
  padding 0 12px;
  margin-bottom 10px;
  &__all
    line-height 60px;
    font-size:18px;
    border-bottom 1px solid #DCDCDC;
  span
    font-size 15px;
    color #666;
    float right;
.goods-intro
  background #fff;
  padding 0 12px;
  &__detail
    width:100%;
    overflow:hidden;
  &__title
    line-height 58px;
    text-align center;
    & > img
      width 74px;
      vertical-align middle;
  &__image
    width 100%;
    vertical-align top;
    margin-bottom 20px;
.goods-spec
  position fixed;
  top 0;
  left 0;
  bottom 0;
  right 0;
  background rgba(0, 0, 0, .6);
  z-index 1000;
</style>
<style type="text/css">
  .goods-intro__detail img{
    max-width:100%;
    margin:0 auto;
  }
</style>
