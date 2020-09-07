<template>
  <div id="app">
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  name: 'app',
};
</script>

<style lang="less">
@import '~vux/src/styles/reset.less';
.fonColor(@color, @font) {
  color:@color;
  font-size: @font;
}
html,body,#app {
  min-height:100%;
  background:#fff;
}
body {
  background-color: #fbf9fe;
}
.weui-dialog__title {
  font-size: 20px;
  color:#333;
}
.weui-dialog__hd {
  position: relative;
  top: -4px;
}
.weui-dialog__bd {
  .fonColor(#666,16px)
}
.weui-dialog__ft .weui-dialog__btn_default {
  .fonColor(#666666,18px)
}
.weui-dialog__ft .weui-dialog__btn_primary {
  .fonColor(#3197FA,18px)
}
.weui-dialog {
  border-radius: 10px;
} 
</style>
