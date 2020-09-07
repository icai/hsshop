<template>
  <!--列表区域-->
        <div id="list_big_box" style="">
            <common-header title="客服转接" @right-click="rightClick" :left-options="{showBack:true}" :right-options="{type:'text','title':'确定'}" />
            <div class="elist publictop"  style="display: none">
                <div class="namelist active_fixed"><span id="pubtext">A</span></div>
            </div>
            <ul id="ul">
                <div class="elist">
                    <ul class="listbox">
                        <li v-for="(ite,index) in tranList" :key="index">
                            <div class="list_wrap">
                                <img v-if='ite.custid != userList.custId && ite.custid != chooseObj.custId' class="select" @click="is_select(ite)" src="../../assets/images/wxz@2x.png" alt="" />
                                <img v-if='ite.custid == userList.custId' class="select" src="../../assets/images/hui-xz@2x.png" alt="" />
                                <img v-if='ite.custid == chooseObj.custId' class="select" @click="is_select(ite)" src="../../assets/images/xuanzhong@2x.png" alt=""/>
                                <img :src="ite.headurl" alt="" class="head_img"/>
                                <span :class="ite.custserverstatus != 'online' ? (ite.custserverstatus == 'busy' ?  'state_light_2':'state_light_0'):'state_light_1'"></span>
                                <div class="list_right">
                                    <p>{{ite.custname}}</p>
                                    <span>当前接待人数：{{ite.joinusernum}}人</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </ul>
        </div>
</template>
<script>
import commonHeader from '../../components/kefu/header/header'
import { XSwitch,Group,Cell } from 'vux'

import store from '../../store/index';
import {mapState,mapGetters} from 'vuex'

export default {
  store,
  data() {
    return {
        tranList:[],
        xz:false,
        custIdsarr:[],
        data2:[],
        userId:'',
        chooseObj:{
          choosed:0,
          custId:""
        },
        xzcustId:''
    }
  },
  computed:{
    ...mapGetters(['user']),
    ...mapGetters(['userList'])
  },
  components:{
    commonHeader,
    Group,
    Cell,
    XSwitch
  },
  created(){
        this.userId = this.$route.query.userId
        console.log(this)
        var that = this;
        this.$axios.get('/list/customer/shopAllCust',{params:{
          shopId:this.userList.shopId,
          crm_token:this.userList.crm_token,
        }}).then((res)=>{
            if(res.data.code == 100){
                this.tranList = res.data.data;
                // that.$axios.get('/list/customer/listJoinCust',{params:{
                //     userId:that.userId,
                //   shopId:that.userList.shopId,
                //     crm_token:that.userList.crm_token,
                // }}).then((res)=>{
                //     console.log(res)
                //     if(res.data.code == 100){
                //         var dataArr = res.data.data
                //         for(let i=0; i<custlist.length; i++){
                //             custlist[i]["is_xz"] = false
                //             for(let j = 0; j < dataArr.length; j++){
                //                 if(custlist[i].custid == dataArr[j].custId){
                //                     custlist[i]["xz"] = true
                //                 }
                //             }
                //         }
                //         that.custlist = custlist
                //         console.log(that.custlist)
                //     }
                // })
                // this.data2=[];
              // res.data.data.forEach((item, index) => {
              //   if(item.custid==localStorage.custId){
              //     this.data2.push({
              //       label: item.custname,
              //       key  : item.custid,
              //       name : res.data.data[index].custname,
              //       disabled : true
              //     })
              //   }else{
              //     this.data2.push({
              //       label: item.custname,
              //       key  : item.custid,
              //       name : res.data.data[index].custname
              //     })
              //   }
              // })
            }
        })
  },
  mounted(){
    console.log(this.xzcustId,333333)
  },
  methods: {
    rightClick:function(){
        //进行客服的转接,删除前台的对应的数据
        if (this.chooseObj.custId != "" ){
          var userInfo = JSON.parse(localStorage.getItem('userInfo'));
          //进行数据的添加和对应的修改操作
          this.$axios.get("/list/customer/custTransfer",{params:{
            userId: userInfo.userId,
            shopId: userInfo.shopId,
            custId:this.chooseObj.custId,
            joinWay:localStorage.custJoinWay,
            crm_token:this.userList.crm_token,
            custJoinway:localStorage.custJoinWay,
          }}).then((res)=>{
            if (res.data.code == "100"){
              //删除成功后数量减一
              this.TranVisible = false
              this.$vux.toast.show({
                  text: '转接成功！',
              });
              this.$router.push({
                path: '/kefu/list'
              })
            }else{
              this.TranVisible = false
              this.$vux.toast.show({
                text: '转接失败！',
                type:'warn',
              })
            }
          })
        }    
    },
    is_select: function(ite) {
      if(ite.custserverstatus== 'offline'){
        this.$vux.toast.show({
          text: '当前客服不在线！',
          type:'warn',
        })
        return
      }
      if (ite.custid != this.chooseObj.custId){
        this.chooseObj.custId = ite.custid;
      }else{
        this.chooseObj.choosed = 0;
        this.chooseObj.custId = '';
      }
    },
  }
}
</script>

<style lang="stylus" rel="stylesheet/stylus" scoped>
        * {
          margin: 0;
          padding: 0;
          font-family: "microsoft yahei !important";
        }
        li {
          list-style: none;
        }
        #bigbox {
          margin-top: 0.525rem;
        }
        .search {
          width: 100%;
          display: block;
          height: 1.425rem;
          box-sizing: border-box;
          background: #f7f7f7;
          border-radius: 5px;
          border: 0;
        }
        #searchbox {
          position: relative;
          width: 17.55rem;
          font-size: 0.65rem;
          margin: 0 auto;
          height: 1.425rem;
          margin-bottom: 0.6rem;
        }
        #searchbox img {
          position: absolute;
          display: inline-block;
          width: 0.875rem;
          height: 0.875rem;
          top: 50%;
          margin-top: -0.4375rem;
          margin-left: 0.625rem;
        }
        #searchbox span {
          color: #bbbbbb;
          position: absolute;
          top: 50%;
          margin-top: -0.4375rem;
          margin-left: 1.75rem;
        }
        .namelist {
          display: block;
          height: 1.8rem;
          line-height: 1.8rem;
          background: #f5f5f5;
          color: #333333;
          font-size: 1rem;
          border-top: 1px solid #e6e6e6;
        }
        .namelist span {
          display: block;
          width: 17.55rem;
          margin-left: 1rem;
        }
        .listbox {
          display: block;
          width: 100%;
          background: #fff;
        }
        .listbox li {
          width: 100%;
          font-size: 0.8rem;
          height:4rem;
          margin: 0 auto;
          color: #333333;
          border-bottom: 1px solid #e6e6e6;
        }
        .listbox li .list_wrap{
            height: 4rem;
            padding: 0 1rem;
            position:relative
        }
        .listbox li .list_right{
            height: 4rem;
        }
        .listbox li div .head_img{
            width: 3rem;
            height: 3rem;
            border-radius:100%;            
            float: left;
            margin: .45rem 1rem 0 0;
        }
        .listbox li div .state_light_1{
          position: absolute;
          left: 120px;
          bottom: 13px;
          width: 8px;
          height: 8px;
          border: 1px solid #fff;
          border-radius: 50%;
          background: #3ebd00;
        }
        .listbox li div .state_light_0{
          position: absolute;
          left: 120px;
          bottom: 13px;
          width: 8px;
          height: 8px;
          border: 1px solid #fff;
          border-radius: 50%;
          background: #c0c4cc;
        }
        .listbox li p{
            display: inline-block;
            margin: .5rem 0 .1rem 0;
            color: #333333;
            font-size: 1rem;
        }
        .listbox li span{
            display: block;
            color: #666666;
        }
        .listbox li .select{
            width: 1.5rem;
            float: left;
            margin: 1.1rem 1rem 0 .5rem;
        }
        li:last-child {
          border: none ;
        }
        .right_nav {
          position: fixed;
          right: 0.6rem;
          top: 25%;
        }
        .right_nav li {
          font-size: 0.5rem;
        }
        .right_nav img {
          width: 0.45rem;
        }
        .active_fixed {
          position: fixed;
          display: block;
          top: 0;
          width: 100%;
        }
        .active_top {
          margin-top: 1.2rem;
        }
        .active_hide {
          display: none;
        }
        .overbox {
          width: 100%;
        }
        .overbox .overlistbox {
          display: block;
          width: 18.15rem;
          margin-left: 0.6rem;
        }
        .overbox .overlistbox li {
          font-size: 0.7rem;
          border-bottom: 1px solid #e6e6e6;
          height: 2.25rem;
          line-height: 2.25rem;
        }
        input {
          outline: none;
          /*清除选中效果的默认蓝色边框 */
 
          -webkit-appearance: none;
          /*清除浏览器默认的样式 */
 
          line-height: normal;
        }
</style>
