<template>
  <div class="format">
    <div class="format-topper">
      <img class="format-topper__image" src="../../../../assets/images/logo.png" alt="">
      <p>
        <span v-show='queryNorms.length'>请选择规则属性</span>
        <span v-show='!queryNorms.length'>此商品暂无规格</span>
        <span class="format-topper__price">价格：{{show_price}}</span>
      </p>
      <i class="iconfont hs-icon-cha format-close" @click="handleCloseFormat"></i>
    </div>
    <div class="format-box">
      <div v-if='queryNorms.length' class="format-rules" v-for="(item, index) in queryNorms">
        <p class="format-rules__title" :data-id="item.props.id">{{item.props.title}}</p>
        <p class="format-list">
        <span class="format-rules__list" @click="valuesClick(index,item.props.id)"
              :class="{'format-rules__active' : index === propsId[item.props.sort].values_id && item.props.id == propsId[item.props.sort].props_id}"
              v-for="(v, index) in item.values">{{v.title}}</span>
        </p>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      valuesId: 0,
      propsId: [
        {
          props_id:0,
          values_id:0
        },
        {
          props_id:0,
          values_id:0
        },
        {
          props_id:0,
          values_id:0
        }
      ]
    }
  },
  props:{
    queryNorms:{
      type: Array,
      required: true
    },
    show_price:{
      type: String,
      required: true
    }
  },
  created(){
    if(this.queryNorms.length){
      this.propsId[0].props_id = this.queryNorms[0].props.id
      this.propsId[1].props_id = this.queryNorms[1].props.id
      this.propsId[2].props_id = this.queryNorms[2].props.id
    }
  },
  methods: {
    handleCloseFormat() {
      this.$emit('handleCloseFormat')
    },
    valuesClick(index,id){
      for(let i = 0; i < this.propsId.length; i++){
        if(this.propsId[i].props_id == id){
          this.propsId[i].values_id = index
        }
      }
    },
  }
}
</script>

<style lang="stylus" rel="stylesheet/stylus" scoped>
.format
  position fixed;
  bottom 0;
  left 0;
  right 0;
  // width 100%;
  // min-height 355px;
  background #fff;
  padding 22px 13px 20px 13px;
.format-topper
    color #333;
    display flex;
    position relative;
    margin-bottom 3px;
    &__image
      width 89px;
      height 89px;
      margin-right 9px;
    & > p
      font-size 14px;
      display flex;
      flex-direction column-reverse;
    .format-topper__price
      color #FF2C40
      margin-bottom 20px
    .format-close
      position absolute;
      top 0;
      right 10px;
      font-size 20px;
.format-box
  max-height 300px
  overflow auto
  .format-rules
    font-size 14px;
    &__title
      line-height 42px;
    .format-list
      .format-rules__list
        display inline-block;
        background-color #e5e5e5;
        padding 8px 12px;
        margin 0 10px 10px 0;
        border-radius 4px;
      .format-rules__active
        background-color  #3197FA;
        color #fff;
  .format-span
    text-align center
    padding-top 20px
    margin-top 5px
</style>
