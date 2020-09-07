<template>
  <div>
    <common-header title="我的客户" @right-click="rightClick" :left-options="{showBack:false,type:'popover'}"  :right-options="{type:'text','title':'设置'}"></common-header>
    <div v-if='off_line == 1' class="transation" v-cloak>
      <!--<div class='clien-inform vux-1px-b'>-->
        <!--<div class='dn-img'></div>-->
        <!--<div>桌面客服已登录（手机通知已开启）</div>-->
      <!--</div>-->
      <tab :line-width="1" custom-bar-width="60px" v-model="index01" prevent-default @on-before-index-change="switchTabItem">
        <tab-item selected>接待中</tab-item>
        <tab-item>历史客户</tab-item>
      </tab>
      <div class='clien-num' v-if='maxusernum && index01 ==0'>当前自动接入最多<span>{{maxusernum}}</span>人</div>
      <ul class='clien-list'>
        <li class='vux-1px-b' @click='getChat(item,index)' v-for='(item, index) in clienList'>
          <div class='clien-list-left'>
            <div class='clien-list-img'>
              <img :src="item.headimgurl" alt="">
            </div>
            <div class='clien-list-user'>
              <p class='username'>
                <span class="username-nick">{{item.nickname}}</span>
                <span v-if='item.joinway == "phone"' class='username-flag phone'>手机</span>
                <span v-if='item.joinway == "weixin"' class='username-flag wechat'>微信</span>
                <span v-if='item.joinway == "small"' class='username-flag applet'>小程序</span>
              </p>
              <p class='last-message'>{{item.message}}</p>
            </div>
          </div>
          <div class='clien-list-right'>
            <p>{{item.time}}</p>
            <badge class='msg' :text="item.msgcount" v-if="item.msgcount > 0"></badge>
          </div>
        </li>
      </ul>
    </div>
    <div v-if='off_line == 0' class='off_line'>
      <div class='clien-close'>自动接入已关闭</div>
      <div class='close-tip'>
        <div class='close-tip-img'></div>
        <div>您必须在线才能接客~</div>
      </div>
    </div>
  </div>
</template>
<script>
import commonHeader from '../../components/kefu/header/header'
import { Badge,Tab,TabItem } from 'vux'
import {mapState, mapGetters} from 'vuex'
export default {
  data() {
    return {
      off_line: 1,
      clienList: [],
      maxusernum: 0,
      kefuStatus: 'online',
      msgcount: 0,
      lastMsg: '',
      index01: 0,
    }
  },
  components: {
    commonHeader,
    Badge,
    Tab,
    TabItem
  },
  computed: {
    ...mapState([
      'status'
    ]),
    ...mapGetters({
      userList: 'userList'
    })
  },
  watch: {
    status: {
      handler(newVal, oldVal) {
        if (newVal === 'offline') {
          this.getOff(0, newVal, oldVal)
        }
        if (newVal === 'online') {
          this.getOff(1, newVal, oldVal)
        }
        if (newVal === 'busy') {
          this.getOff(1, newVal, oldVal)
        }
      },
      deep: true,
      immediate: true
    }
  },
  methods: {
    switchTabItem (index) {
      console.log(this.$vux)
      console.log('on-before-index-change', index)
      this.$vux.loading.show({
        text: '加载中'
      })
      // setTimeout(() => {
      //   this.$vux.loading.hide()
      //   this.index01 = index
      // }, 1000)
      if(index == 0){
        this.getClienData(index);
      }else{
        this.getHistoryData(index);
      }
    },
    rightClick() {
      this.$router.push({
        path: '/kefu/set'
      })
    },
    getChat(item, index) {
      let _this = this
      this.$axios.get('/list/customer/selectedUser', {params: {
        userId: item.userId,
        shopId: this.userList.shopId ? this.userList.shopId : localStorage.setItem('shopId'),
        custId: this.userList.custId ? this.userList.custId : localStorage.setItem('custId'),
        crm_token: this.userList.crm_token ? this.userList.crm_token : localStorage.setItem('crm_token'),
        custJoinway:localStorage.custJoinWay,
      }}).then((res) => {
        if (res.data.code === '100') {
          item.msgcount = 0
          _this.$set(_this.clienList, index, item)
          localStorage.setItem('userInfo',JSON.stringify(item));
          _this.$router.push({
            path: !item.history ? '/kefu/chat?userId=' + item.userId + '&weiuserid=' + item.weiuserid + '&nickname=' + item.nickname : '/kefu/chat?userId=' + item.userId + '&weiuserid=' + item.weiuserid + '&nickname=' + item.nickname + '&history=true'
          })
        }
      })

    },
    getOff(num, val, oldVal) {
      this.off_line = num
      this.kefuStatus = val
      let _this = this
      this.$axios.get('/list/customer/updateCustStatus', {
        params: {
          shopId: this.userList.shopId ? this.userList.shopId : localStorage.setItem('shopId'),
          custId: this.userList.custId ? this.userList.custId : localStorage.setItem('custId'),
          custServerStatus: val,
          crm_token: this.userList.crm_token ? this.userList.crm_token : localStorage.setItem('crm_token'),
          custJoinway:localStorage.custJoinWay,
        }
      }).then((res) => {
        if (res.data.code === '100') {
          if ((_this.kefuStatus === 'online' && oldVal === 'offline') || (_this.kefuStatus === 'busy' && oldVal === 'offline')) {
            _this.getClienData()
          }
        }
      }).catch((err) => {
        console.log(err)
      })
    },
    getHistoryData(index){
      var that = this;
      this.$axios.get('/list/customer/userHistoryList',{
          params:{
            "shopId":this.userList.shopId,
            "custId":this.userList.custId,
            "crm_token":this.userList.crm_token
          }}, {emulateJSON:true}
        ).then(function(res){
          console.log(res);
          that.$vux.loading.hide();
          that.index01 = index
          if (res.data.code =='100' && res.data.data.length > 0){
            // this.memberList=res.body.data;
            that.clienList = res.data.data
            if(that.clienList && that.clienList.length){
              for(var i = 0; i< that.clienList.length; i++){
                that.clienList[i]['history'] = true;
              }
            }
//              console.log(this.memberList)
//              this.waitPerson = res.body.data.userHistory.length;
          }else{
            console.log("历史记录异常" + res.body.code + "-" + res.body.msg);
          }
      })
    },
    getClienData(index) {
      let _this = this
      console.log(_this.userList)
      let params = {
        'shopId': '',
        'custId': '',
        'status': 'online',
        'crm_token': '',
        'custJoinWay':'',
      }
      if (!_this.userList) {
        params.shopId = localStorage.getItem('custId');
        params.custId = localStorage.getItem('custId');
        params.status = this.kefuStatus
        params.crm_token = localStorage.getItem('crm_token')
        params.custJoinWay = localStorage.getItem('custJoinWay');
      } else {
        params.shopId = _this.userList.shopId
        params.custId = _this.userList.custId
        params.status = this.kefuStatus
        params.crm_token = _this.userList.crm_token
        params.custJoinWay = localStorage.getItem('custJoinWay');
      }
      this.$axios.get('/list/customer/init', {
        params: params
      }).then((res) => {
        console.log(res)
        this.$vux.loading.hide()
        this.index01 = index
        if (res.data.code === '100') {
          _this.maxusernum = res.data.data.maxusernum
          _this.clienList = res.data.data.userInfoVos
        }
      }).catch((err) => {
        console.log(err)
      })
    }
  },
  created() {
    this.getClienData(0)
    let _this = this
    this.socket.on('message', (res) => {
      console.log(res, '消息')
      var RegUrl = new RegExp()
      RegUrl.compile('<a style')
      if (res != null && res != '') {
        _this.msgcount = res.msgcount
        _this.lastMsg = res.message
        for (var i = 0; i < _this.clienList.length; i++) {
          var user = _this.clienList[i]
          if (user.userId == parseInt(res.userid)) {
            if (res.msgtype == 'image') {
              user.message = '[图片]'
            } else if (res.msgtype == 'goods') {
              user.message = '[商品]'
            } else if (RegUrl.test(res.message)) {
              user.message = '[链接]'
            } else if (res.msgtype == 'miniprogrampage') {
              user.message = '[小程序卡片]'
            } else {
              user.message = res.message
            }
            if (res.userid != localStorage.serUserId) {
              user.msgcount = res.msgcount
            }
            _this.$set(_this.clienList, i, user)
          }
        }
      }
    })
    this.socket.on('currentDialog', (res) => {
      let data =  JSON.parse(res)
      if (data.code == 100) {
        _this.clienList = data.data
      }
    })
  }
}
</script>
<style lang="stylus" rel="stylesheet/stylus" scoped>
  .transation
    background-color #f5f5f5
    .clien-inform
      height 47px
      display flex
      align-items center
      justify-content center
      font-size 15px
      color #666666
      &:after
        border-bottom-color #e5e5e5
        color #e5e5e5
      .dn-img
        width 24px
        height 20px
        background url("../../assets/images/DN@2x.png") no-repeat
        background-size cover
        margin-right 12px
        position: relative
        top -1px
    .clien-num
      color #999999
      font-size 0.875rem
      padding-left 0.687rem
      margin 0.625rem 0
    .clien-list
      background-color #ffff
      li
        width 100%
        height 4rem
        display flex
        justify-content: space-between
        padding 0.625rem
        box-sizing border-box
        &::after
          border-bottom-color: #e5e5e5
          color #e5e5e5
        .clien-list-left
          width 16.473rem
          display flex
          .clien-list-img
            width 2.812rem
            height 2.812rem
            border-radius 0.25rem
            overflow hidden
            img
              width 100%
              height 100%
          .clien-list-user
            width 12.75rem
            padding-left 0.625rem
            .username
              display flex
              font-size 0.8rem
              color #333333
              font-weight bold
              padding:0 0 5px 0
              line-height 1.562rem
              .username-nick
                max-width 8.125rem
                white-space nowrap
                overflow hidden
                text-overflow ellipsis
                display block
              .username-flag
                display block
                font-size 0.65rem
                padding 0.05rem 0.312rem 0.05rem 0.312rem
                font-weight normal
                position relative
                top -0.125rem
                margin-left 0.3rem
                border-radius 0.25rem
              .phone
                color #FFD10C
                border 1px solid #FFD10C
              .wechat
                color #01C40B
                border 1px solid #01C40B
              .applet
                color #01C4FE
                border 1px solid #01C4FE
            .last-message
              height 1.2rem
              line-height 1.2rem
              font-size 0.875rem
              color #666666
              white-space nowrap
              overflow hidden
              text-overflow ellipsis
              width 100%
        .clien-list-right
          font-size 15px
          color #999999
          .msg
            float right
            margin-top 0.8rem
  .off_line
    padding-top 12px
    .clien-close
      color #999999
      font-size 15px
      padding-left 12px
      margin-bottom 12px
    .close-tip
      padding-top 120px
      text-align center
      .close-tip-img
        width 114px
        height 92px
        background url("../../assets/images/lx@2x.png") no-repeat
        background-size cover
        margin 0 auto
        margin-bottom 40px
</style>
