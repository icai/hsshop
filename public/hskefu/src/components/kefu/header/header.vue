<template>
  <div class="transation">
    <x-header :left-options="{backText: '',showBack: leftOptions.showBack}" :title="title" class="header">
      <div slot="left">
         <a v-if="leftOptions.type=='popover'" class="left_text" @click.preventDefault @click="showPopover">
          <span class="arrow_text" v-text="resoveType"></span>
          <img v-if="!showpover" class="arrow_icon" src="../../../assets/images/JT-X@2x.png">
          <img v-else class="arrow_icon" src="../../../assets/images/JT-s@2x.png">
        </a>
        <div class="popover popover-links modal-in" style="display: block; top: 36px; left: 5px;" v-show="showpover && leftOptions.type=='popover'">
          <div class="popover-angle on-top" style="left: 37px;"></div>
          <div class="popover-inner">
            <div class="list-block">
              <ul>
                <li @click.stop="changeState('online')">
                  <a href="#" class="list-button item-link">
                    	在线
                    <img v-if="status == 'online'" src="../../../assets/images/DH@2x.png">
                  </a>
                </li>
                <li  @click.stop="changeState('busy')">
                  <a href="#" class="list-button item-link">
                  	忙碌
                  <img v-if="status == 'busy'" src="../../../assets/images/DH@2x.png">
                  </a>
                </li>
                <li  @click.stop="changeState('offline')">
                  <a href="#" class="list-button item-link">
                  	离线
                  <img v-if="status == 'offline'" src="../../../assets/images/DH@2x.png">
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <a v-if="rightOptions.type=='text'" class="right_text" slot="right" @click.preventDefault @click="$emit('right-click')">{{rightOptions.title}}</a>

      <a v-if="rightOptions.type=='icon'" slot="right" @click.preventDefault @click="$emit('right-click')">
        <img class="icon_image" src="../../../assets/images/wd@2x.png">
      </a>
    </x-header>
  </div>
</template>
<script>
import { XHeader } from 'vux'
import {mapState,mapMutations} from 'vuex'
export default {
  name:'commonHeader',
  data() {
    return {
      showpover:false,
    }
  },
  computed: {
      ...mapState([
          'status'
      ]),
      resoveType: function () {
        // `this` points to the vm instance
        if(this.status== 'online'){
          return '在线'
        }else if(this.status== 'busy'){
          return '忙碌'
        }else if(this.status == 'offline'){
          return '离线'
        }
      }
  },
  components: {
    XHeader,
  },
  props: {
    title: String,
    leftOptions:{
      type: Object,
      default () {
        return {
          showBack: 'true',
        }
      }
    },
    rightOptions: {
      type: Object,
      default () {
        return {
          type: 'icon',
          title:'客服'
        }
      }
    }
  },
  created(){
    if (localStorage.getItem("kefuStatus")) {
      this.CHANGE_STATUS(localStorage.getItem("kefuStatus"));
    }
    
    this.socket.on('custStateSwitch',(res)=>{
        console.log("res============",res);
		  	if(localStorage.kefuStatus=='offline'){
		  		return;
		  	}
		  	this.CHANGE_STATUS(res);
    })
  },
  methods: {
    ...mapMutations([
       'CHANGE_STATUS'
    ]),
    /**
     * [showPopover description]
     * @author wdd
     * @desc   显示隐藏接待中弹窗
     * @date   2018-08-13T16:33:36+0800
     * @return {[type]}                 [description]
     */
   showPopover(){
    this.showpover = !this.showpover;
   },
   changeState(val){
      if(val == 'offline'){
        
      }else if(val == 'online'){

      }else if(val == 'busy'){

      }
      this.CHANGE_STATUS(val);
      this.showpover = !this.showpover;
      // this.stateType = val;
      // this.onlineShow = !this.onlineShow;
      // //修改对象的对应的状态
      // this.$axios.get("list/customer/updateCustStatus",{params:{
      //     shopId: localStorage.shopId,
      //     custId: localStorage.custId,
      //     custServerStatus: this.stateType,
      //     crm_token:localStorage.crm_token
      // }}).then((res)=>{
      //     console.log(res)
      //     if (res.data.code =='100'){
      //         //this.custJoinNum = res.data.data;
      //     }else{
      //         console.log("异常")
      //     }
      // })
    },
  }
}
</script>
<style lang="less" scoped>
.header{
  border-bottom:1px solid #eee;
  font-size:18px;
  z-index:1;
}
.arrow_text{
  font-size:16px;
}
.icon_image{
  width:20px;
}
.arrow_icon{
  width:18px;
}
.right_text{
  font-size:16px;
}
.left_text{
  position:relative;
}
.popover {
    width: 150px;
    background: rgba(0,0,0,.95);
    z-index: 11000;
    margin: 0;
    top: 0;
    opacity: 0;
    left: 0;
    border-radius: 7px;
    position: absolute;
    display: none;
    -webkit-transform: none;
    transform: none;
    -webkit-transition-property: opacity;
    -moz-transition-property: opacity;
    -ms-transition-property: opacity;
    -o-transition-property: opacity;
    transition-property: opacity;
}
.popover {
    width: 150px;
}
.popover.modal-in {
    -webkit-transition-duration: 300ms;
    transition-duration: 300ms;
    opacity: 1;
}
.popover-angle {
    width: 26px;
    height: 26px;
    position: absolute;
    left: -26px;
    top: 0;
    z-index: 100;
    overflow: hidden;
}
.popover-angle.on-top {
    left: 0;
    top: -26px;
}
.popover-angle:after {
    content: ' ';
    background: rgba(0,0,0,.95);
    width: 20px;
    height: 20px;
    position: absolute;
    left: 0;
    top: 0;
    border-radius: 3px;
    -webkit-transform: rotate(45deg);
    transform: rotate(45deg);
}
.popover-angle.on-top:after {
    left: 0;
    top: 20px;
}
.popover-inner {
    overflow: auto;
    -webkit-overflow-scrolling: touch;
}
.list-block {
    margin: 35px 0;
    font-size: 17px;
}
.popover .list-block {
    margin: 0;
}
.list-block ul {
    background: #fff;
    list-style: none;
    padding: 0;
    margin: 0;
    position: relative;
}
.popover .list-block ul {
    background: 0 0;
}
.popover .list-block:first-child:last-child li:first-child:last-child a, .popover .list-block:first-child:last-child ul:first-child:last-child {
    border-radius: 7px;
}
.list-block li {
    box-sizing: border-box;
    position: relative;
}
.list-block .item-link.list-button {
    padding: 0 15px;
    text-align: left;
    color: #fff;
    display: block;
    line-height: 43px;
    width:100%;
    box-sizing: border-box;
}
.list-block ul li img{
  width:20px;
  float: right;
  margin-right: 26px;
  margin-top: 13px;
}
.popover .list-block:first-child li:first-child a {
    border-radius: 7px 7px 0 0;
}
</style>
