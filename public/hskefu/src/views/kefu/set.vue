<template>
  <div class="transation">
    <common-header title="客服消息设置" :left-options="{showBack:true}" :right-options="{showMore:false}" />
    <group>
    <cell title="自动接入数量" v-model="people" is-link link="/kefu/setAuto"></cell>
   </group>
   <!-- <group>
     <x-switch title="未接待消息通知" @on-change="changeModel" v-model="value"></x-switch>
   </group> -->
   <!-- <div class="tips">若关闭，当收到未接待客户的消息后，仅在未读消息中提醒，但不进行手机通知</div> -->
  </div>
</template>
<script>
import commonHeader from '../../components/kefu/header/header'
import { mapGetters} from 'vuex'
import { XSwitch,Group,Cell } from 'vux'
export default {
  data() {
    return {
      people: '',
      value: true,
    }
  },
  components:{
    commonHeader,
    Group,
    Cell,
    XSwitch
  },
  computed: {
    ...mapGetters({
      'userList': 'userList'
    })
  },
  created() {
    this.$axios.get('/list/customer/getShopCustPo', {
      params: {
        shopId: this.userList.shopId,
        custId: this.userList.custId,
        crm_token: this.userList.crm_token
      }
    }, {emulateJSON: true}
    ).then((res) => {
      if(res.data.code == '100'){
        this.people = res.data.data.maxusernum
      }else{
        console.log("异常2" + res.data.code + "-" + res.data.msg);
      }
    })
  },
  methods: {
    changeModel:(val)=>{
      console.log(val) // true  false
    }
  }
}
</script>

<style lang="less" scoped>
  @base: 32rem;
  .tips {
    color: #666;
    padding: 21/@base 31/@base;
    font-size: 24/@base;
  }

</style>
