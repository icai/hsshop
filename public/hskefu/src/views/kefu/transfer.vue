<template>
  <div></div>
</template>

<script>
import {mapMutations} from 'vuex'
export default {
  name:'transfer',
  methods:{
    ...mapMutations([
       'LOGIN'
    ]),
    typeMobile(){
      var UA = window.navigator.userAgent.toLowerCase();
      var isAndroid = (UA && UA.indexOf('android') > 0);
      var isIOS = (UA && /iphone|ipad|ipod|ios/.test(UA));
      if(isAndroid){
        return '2';
      }else if(isIOS){
        return '3';
      }else{
        return '';
      }
    }
  },//初始化的时候获取到对应的信息，写到localstorage中，然后跳转页面
  mounted(){
    //发送消息验证
  var that = this;
  localStorage.custJoinWay = 'APP';
  this.$axios.get("/list/authority/login",{params:{
      custId: this.$route.query.custId,
      shopId: this.$route.query.shopId,
      sign: this.$route.query.sign,
      equip:this.typeMobile(),
      custJoinway:localStorage.custJoinWay,
    }}).then((res)=>{
      if (res.data.code =='100'){
        localStorage.custJoinWay = this.$route.query.custJoinWay;
        localStorage.custId = res.data.data.custId;
        localStorage.custname = res.data.data.custname;
        localStorage.headurl = res.data.data.headurl != "" ? res.data.data.headurl : 'https://upx.cdn.huisou.cn/wscphp/res/home/image/huisouyun_120.png';
        localStorage.shopId = res.data.data.shopId;
        localStorage.isheader = res.data.data.isheader;
        localStorage.crm_token = res.data.data.crm_token;
        localStorage.setItem('shop_name',"会搜云客服-" + res.data.data.shopname);
        localStorage.setItem('shop_logo',res.data.data.logo);
        // console.log(data.data.shopData.shop_logo)
        // document.getElementsByTagName('title')[0].innerHTML ="汇搜云客服-" + res.data.data.shopname;
        // document.getElementsByTagName('link')[0].innerHTML = res.data.data.logo;
         that.LOGIN({
            custId:res.data.data.custId,
            custname:res.data.data.custname,
            headurl:res.data.data.headurl != "" ? res.data.data.headurl : 'https://upx.cdn.huisou.cn/wscphp/res/home/image/huisouyun_120.png',
            shopId:res.data.data.shopId,
            isheader:res.data.data.isheader,
            crm_token:res.data.data.crm_token,
            shop_name:res.data.data.shopname,
            shop_logo:res.data.data.logo,
         })
//        this.$axios.get('https://hsshop.huisou.cn/api/getChatData',{params:{
//          shopId:res.data.data.shopId
//        }}).then(function(data){
//          console.log('商户数据--',data);
//          console.log('商户名称--',data.data.data.shopData.shop_name);
//          localStorage.setItem('shop_name',"汇搜云客服-" + data.data.data.shopData.shop_name);
//          localStorage.setItem('shop_logo','http://d.hiphotos.baidu.com/image/h%3D300/sign=428337b7c9cec3fd943ea175e689d4b6/1f178a82b9014a900d928cc6a3773912b31bee1a.jpg');
//          // console.log(data.data.shopData.shop_logo)
//          document.getElementsByTagName('title')[0].innerHTML ="汇搜云客服-" + data.data.data.shopData.shop_name;
//          document.getElementsByTagName('link')[0].innerHTML = data.data.data.shopData.shop_logo;
//        })
        that.$router.push({ path: 'kefu/list'})
        console.log(localStorage.shopId +"---------" + localStorage.custId)
      }else if(res.data.code == 404){
        this.$vux.alert.show({
          title: '提示',
          content: res.data.data,
          onShow () {
            console.log('Plugin: I\'m showing')
          },
          onHide () {
            console.log('Plugin: I\'m hiding')
          }
        })
      }
    })

  }
}
  
</script>
<style>
</style>