<template>
    <div>
        <common-header :title="title" :left-options="{showBack:true}" :right-options="{type:'text','title':''}"></common-header>
        <a class="userName" @click="goUserInfo">
            <div class="userIcon">
                <img :src="headimgurl" alt="">
            </div>
            <div class="user_name">{{nickname}}</div>
            <img class="arrow_icon" src="../../assets/images/yjt@2x.png" alt="">
        </a>
        <div class="list-title">接待客服</div>
        <div class="user-list">
            <ul>
                <li v-for="item in list">
                    <div>
                        <img :src="item.headurl" alt="">
                    </div>
                    <span>{{item.custname}}</span>
                </li>
                <li>
                    <a class="add-img" @click="addKefu">
                        <img src="../../assets/images/tianjia@2x.png" alt="">
                    </a>
                </li>
            </ul>
            
        </div>
        <button class="quit_btn" @click="quitBtn">
            退出接待
        </button>
        <div class="layer" v-if="show">
            <div class="layer-box">
                <div class="layer-head">
                    是否要退出接待？
                </div>
                <div class="foot">
                    <div class="btn-cancel" @click="cancel">取消</div>
                    <div class="btn-sure" @click="sure">确认</div>
                </div>
            </div>
            
        </div>
    </div>
</template>
<script>
import commonHeader from '../../components/kefu/header/header'


import store from '../../store/index'
import {mapState,mapGetters} from 'vuex'

export default {
    store,
    data(){
        return{
            userId:'', //用户id
            title:'接待信息',
            show:false,
            nickname:'',
            headimgurl:'',
            list:[]
        }
    },
    computed:{
        ...mapGetters(['user']),
        ...mapGetters(['userList'])
    },
    components:{
        commonHeader
    },
    methods:{
        quitBtn(){
            this.show = true
        },
        //取消退出接待
        cancel(){
            this.show = false
        },
        //确认退出接待
        sure(){
            this.$axios.get('/list/customer/closeDialog', {
                params: {
                    userIds: this.userId,
                    shopId: this.userList.shopId,
                    custId: this.userList.custId,
                    crm_token: this.userList.crm_token,
                    custJoinway:localStorage.custJoinWay,
                }
            }).then((res) => {
                console.log(res)
                if(res.data.code == 100){
                    this.$router.push({
                    path: '/'
                })
                }
            })
            
        },
        addKefu(){
            this.$router.push({
                path: '/kefu/kefuList', 
                query: { 
                    userId: this.$route.query.userId, 
                }
            })
        },
        goUserInfo(){
            this.$router.push({
                path: '/kefu/userDetail', 
                query: { 
                    userId: this.$route.query.userId,
                    weiuserid:this.$route.query.weiuserid
                }
            })
        }
    },
    created(){
       this.userId = this.$route.query.userId
       var _this = this
       //获取客户信息
        _this.$axios.get('/list/customer/getUserInfo', {
            params: {
                userId: _this.userId
            }
        }).then((res) => {
            console.log(res)
            if(res.data.code == 100){
                _this.nickname = res.data.data.nickname,
                _this.headimgurl = res.data.data.headimgurl
            }
        })
        //获取接待客服列表
        _this.$axios.get('/list/customer/listJoinCust', {
            params: {
                userId: _this.userId,
                shopId: this.userList.shopId,
                crm_token: this.userList.crm_token
            }
        }).then((res) => {
            console.log(res.data)
            if(res.data.code == 100){
                if(res.data.data && res.data.data.length == 0){
                    _this.list = []
                        
                }else{
                    _this.list = res.data.data
                }
            }
        })
        //获取客服数量
        _this.$axios.get('/list/customer/countJoinCust', {
            params: {
                userId: _this.userId,
                shopId: this.userList.shopId,
                crm_token: this.userList.crm_token
            }
        }).then((res) => {
            console.log(res)
            if(res.data.code == 100){
                _this.title = '接待信息 (' + res.data.data + ')人'
                
            }
        })
    },
    mounted() {
  },
}
</script>
<style lang="stylus" rel="stylesheet/stylus" scoped>
    body{
        background-color rgb(245,245,245)
    }
    a{
        color #333333
    }
    .userName
       padding 0.625rem 0.78rem
       background-color #ffffff
       margin-top 0.94rem
       box-sizing border-box
       -webkit-box-sizing border-box
       display flex
       position relative
       & .userIcon
            width 3.75rem
            height 3.75rem
            & img
                width 3.75rem
       & .arrow_icon
            width 0.44rem
            height 0.75rem
            position absolute
            top 50%
            right 1.56rem
            margin-top -0.38rem
       & .user_name
            line-height 3.75rem
            margin-left 0.78rem
    .list-title
        line-height 1.56rem
        font-size 0.75rem
        padding-left 0.78rem
    .user-list
        padding 0.5rem 0
        background-color #ffffff
        & ul
            overflow hidden
            & li
                float left
                width 3.75rem
                height 5.4rem
                margin-left 0.78rem
                font-size 0.875rem
                text-align center
                & div 
                    width 3.75rem
                    height 3.75rem
                    & img 
                        width 3.75rem
                        height 3.75rem
                & span 
                    width 3.75rem
                    display inline-block
                    overflow hidden
                    text-overflow ellipsis
                    white-space nowrap
            & li:last-child
                height 3.75rem
    
    .add-img 
        & img
            width 3.75rem
            height 3.75rem                
    .quit_btn
        display block
        width 94%
        height 2.75rem
        background-color #3197fb
        text-align center
        line-height 2.75rem
        margin 1rem auto
        border 0
        border-radius 4px
        color #ffffff
    .layer
        width 100%
        height 100%
        background-color rgba(0,0,0,0.5)
        position fixed
        top 0
        left 0
        bottom 0
        right 0
        z-index 1000
    .layer-box
        width 70%
        position fixed
        top 35%
        left 15%
        background-color #ffffff
        border-radius 6px
        overflow hidden
        & .layer-head
            text-align center
            line-height 100px
            border-bottom 1px solid #999999
        & .foot
            overflow hidden
            
            line-height 50px
            & .btn-cancel
                display inline-block
                text-align center
                width 48%
                height 50px
                border-right 1px solid #999999
            & .btn-sure
                display inline-block
                text-align center
                width 48%
                height 50px 
</style>


