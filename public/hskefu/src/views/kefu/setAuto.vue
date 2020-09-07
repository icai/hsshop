<template>
  <div class="transation">
    <common-header title="设置自动接入" :left-options="{showBack:true}" :right-options="{showMore:false}" />
    <group>
        <radio :options="data" v-model="value" @radio-checked-icon-color="checkedColor" @on-change="change"></radio>
    </group>
  </div>
</template>
<script>
import commonHeader from '../../components/kefu/header/header'
import { mapGetters} from 'vuex'
import { Radio,Group } from 'vux'
export default {
  data() {
    return {
        data: [
            {key: 0, value: '关闭'},
            {key: 20, value: '20人'},
            {key: 50, value: '50人'},
            {key: 100, value: '100人'},
        ],
        isFirst:true,
        value: '20',
        checkedColor: '#3197FA'
    }
  },
  components:{
    commonHeader,
    Group,
    Radio  
  },
  computed: {
    ...mapGetters({
      'userList': 'userList'
    })
  },
  created(){
     this.$axios.get('/list/customer/getShopCustPo', {
      params: {
        shopId: this.userList.shopId,
        custId: this.userList.custId,
        crm_token: this.userList.crm_token
      }
    }, {emulateJSON: true}
    ).then((res) => {
      // console.log(res);
      if(res.data.code == '100'){
        this.value = res.data.data.maxusernum
      }else{
        console.log("异常2" + res.data.code + "-" + res.data.msg);
      }
    })
  },
  methods: {
    change(index){
      if(this.isFirst){
        this.isFirst = !this.isFirst;
        return 
      }
      if (index == 0) {
        this.$router.back();
        return;
      }
      this.$axios.get('/list/customer/changeCustMaxusernum',{
        params:{
          shopId: this.userList.shopId,
          custId: this.userList.custId,
          crm_token: this.userList.crm_token,
          maxusernum:index,
          custJoinway:localStorage.custJoinWay,
        }
    	},{emulateJSON:true}
    	).then((res)=>{
    		 if(res.data.code == '100'){
             this.$router.push({
              path: '/kefu/set'
            });
    		 }else{
    		 		console.log("异常4" + res.data.code + "-" + res.data.msg);
    		 }
    	})
    }
  }
}
</script>

<style lang="less" scoped>
  @base: 32rem;
 

</style>