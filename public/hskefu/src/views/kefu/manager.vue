<template>
  <div class="transation">
    <common-header title="店铺管理员" :left-options="{showBack:true}" :right-options="{showMore:false}" />
    <panel :list="list"></panel>
    <div class="actor-box">
        <div>账号管理员</div>
        <div>核销员</div>
    </div>
  </div>
</template>
<script>
import commonHeader from '../../components/kefu/header/header'
import { Panel } from 'vux'
export default {
  data() {
    return {
      list: [
          {
            src: 'http://somedomain.somdomain/x.jpg',
            title: '管理员12',
            desc: '13888888888'
          }
      ]
    }
  },
  components:{
    commonHeader,
    Panel
  },
  created(){
    
  },
  methods: {
   
  }
}
</script>

<style lang="less" scoped>
    @base: 32rem;
    .actor-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #fff;
        margin-top: 21/@base;
        font-size: 28/@base;
        color: #333;
        height: 88/@base;
        padding: 0 26/@base 0 24/@base;
    }
</style>