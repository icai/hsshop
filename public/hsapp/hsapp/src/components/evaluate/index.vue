<template>
  <div>
    <div class="evaluate" v-for="(item, index) in evaluateList" :key="index">
      <div class="user">
        <p class="user__name">
          <img :src="item.headimgurl" alt="" class="user__header">
          <span>{{item.nickname}}</span>
        </p>
        <p class="user__time">{{item.created_at}}</p>
      </div>
      <div class="eva-content">
        <p>{{item.content}}</p>
        <img v-for="(img, index) in item.img" :key="index" :src="img" alt="" class="eva-images">
      </div>
      <p class="eva-choosed">{{item.spes}}</p>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    evaluateList: {
      type: Array,
      default: () => {}
    }
  }
}
</script>

<style lang="stylus" rel="stylesheet/stylus" scoped>
.evaluate
  width 100%;
  .user
    display flex
    justify-content space-between;
    align-items center;
    margin 16px 0 10px 0;
    color #666;
    font-size 14px;
    &__name
      display flex;
      align-items center;
    .user__header
      width 33px;
      height 33px;
      margin-right 13px;
      border-radius 50%;
  .eva-content
    font-size 14px;
    .eva-images
      width 79px;
      height 79px;
      margin 0 15px 5px 0;
      vertical-align top;
  .eva-choosed
    font-size 12px;
    color #666;
    margin 5px 0 15px 0;
    padding-bottom 5px;
</style>
