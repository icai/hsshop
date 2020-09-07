<template>
  <div class="error">
    <p>404：页面没找到</p>
  </div>
</template>

<style lang="stylus" rel="stylesheet/stylus" scoped>
  @import "~assets/stylus/variables.styl";

  .error {
    color: $color-secondary;
    text-align: center;

    > img {
      margin-bottom: 50px;
      margin-top: 100px;
    }
  }
</style>