<template>
  <!--列表区域-->
		<div id="list_big_box" style="">
			<common-header title="客服消息设置" @right-click="rightClick" :left-options="{showBack:true}" :right-options="{type:'text','title':'确定'}" />
			<div class="elist publictop"  style="display: none">
				<div class="namelist active_fixed"><span id="pubtext">A</span></div>
			</div>
			<ul id="ul">
				<div class="elist">
					<ul class="listbox">
						<li v-for="(ite,index) in custlist" :key="index">
							<div class="list_wrap">
								<img v-if='!ite.is_xz && !ite.xz' class="select" @click="is_select(index)" src="../../assets/images/wxz@2x.png" alt="" />
								<img v-if='ite.xz' class="select" src="../../assets/images/hui-xz@2x.png" alt="" />
								<img v-if='ite.is_xz && !ite.xz' class="select" @click="is_select(index)" src="../../assets/images/xuanzhong@2x.png" alt=""/>
								<img :src="ite.headurl" alt="" class="head_img"/>
								<div class="list_right">
									<p>{{ite.custname}}</p>
									<span v-if="ite.custserverstatus == 'offline'">离线</span>
									<span v-if="ite.custserverstatus == 'online'">在线</span>
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
    	custlist:[],
    	xz:false,
    	custIdsarr:[],
    	userId:'',
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
		console.log(this.userId)
		var that = this;
		this.$axios.get('/list/customer/shopAllCust',{params:{
		  shopId:this.userList.shopId,
			crm_token:this.userList.crm_token,
		}}).then((res)=>{
			if(res.data.code == 100){
				let custlist = res.data.data
				that.$axios.get('/list/customer/listJoinCust',{params:{
					userId:that.userId,
				  shopId:that.userList.shopId,
					crm_token:that.userList.crm_token,
				}}).then((res)=>{
					console.log(res)
					if(res.data.code == 100){
						var dataArr = res.data.data
						for(let i=0; i<custlist.length; i++){
							custlist[i]["is_xz"] = false
							if(dataArr){
								for(let j = 0; j < dataArr.length; j++){
									if(custlist[i].custid == dataArr[j].custId){
										custlist[i]["xz"] = true
									}
								}
							}
						}
						that.custlist = custlist
						console.log(that.custlist)
					}
				})
			}
		})
  },
  mounted(){
  	console.log(this.xzcustId,333333)
  },
  methods: {
  	rightClick:function(){
  		var that = this;
  		for(var i=0; i<this.custlist.length; i++){
  			if(this.custlist[i].is_xz == true){
  				that.custIds = this.custlist[i].custid
  				that.custIdsarr.push(that.custIds)
  			}
  		}
  		console.log(that.custIdsarr)
			if(that.custIdsarr.length){
				this.$axios.get('/list/customer/custListJoinTalk',{params:{
					userId:this.userId,
			  	shopId:this.userList.shopId,
			  	crm_token:this.userList.crm_token,
					custIds:this.custIdsarr,
				}}).then((res)=>{
					console.log(res)
					if(res.data.code == 100){	
						this.$router.push({
		          path: '/kefu/manyCustomer', 
		          query: { 
		              userId: this.$route.query.userId, 
		          }
		      	})
					}
				})
			}else{
				this.$router.push({
          path: '/kefu/manyCustomer', 
          query: { 
              userId: this.$route.query.userId, 
          }
      	})
			}
		
  	},
		is_select: function(index) {
			for(var i=0; i<this.custlist.length; i++){
				if(i == index){
					if(this.custlist[i].is_xz){
						this.custlist[i].is_xz = false
					}else{
						this.custlist[i].is_xz = true
					}
					this.$set(this.custlist,i,this.custlist[i])
				}
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
		}
		.listbox li .list_right{
			height: 4rem;
		}
		.listbox li div .head_img{
			width: 3rem;
			height: 3rem;
			border: 1px #000000 solid;
			border-radius:.3rem ;
			float: left;
			margin: .45rem 1rem 0 0;
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
