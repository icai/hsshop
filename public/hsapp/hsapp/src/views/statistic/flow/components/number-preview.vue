<template>
  <div class="preview-people">
    <p>
      <span>浏览人数</span>
      <span>12345</span>
    </p>
    <p>
      <span>浏览次数</span>
      <span>3245</span>
    </p>
    <p>
      <span>人均浏览次数</span>
      <span>2</span>
    </p>
  </div>
</template>
<style lang="stylus" rel="stylesheet/stylus" scoped>
.preview-people
  height 83px;
  padding 0 12px;
  display flex;
  align-items center;
  justify-content space-between;
  background #fff;
  & > p
    width calc(100% / 3);
    display flex;
    flex-direction column;
    font-size 12px;
    color #999;
    text-align center;
    position relative;
    & > span:nth-child(2)
      font-size 21px;
      color #333;
      font-weight bold;
  & > p:nth-child(2)
    &:after
      position absolute;
      width 1px;
      height 30px;
      background #E5E5E5;
      content '';
      right 0px;
      top 5px;
    &:before
      position absolute;
      width 1px;
      height 30px;
      background #E5E5E5;
      content '';
      left 0px;
      top 5px;
</style>
