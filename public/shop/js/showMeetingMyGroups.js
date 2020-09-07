var vm = new Vue({
    el: "#app",
    delimiters: ['[[', ']]'],
    data:{
        isShowShare: false,
        host:host,//域名
        wid:wid,
        imgUrl:imgUrl,//动态图片地址
        pageShow: false,//页面展示
        nav_bar:[//nav信息
            { status: "", title: "我的拼团" },
            { status: "1", title: "我的订单" },
        ],
        nav_index:0,//nav当前下标
        status:"",//列表状态
        tag:null,
        groupList:[],//拼团列表
        page:1,//页码数
        noMore: false,//没有更多数据
        share: null,//分享
        shareShow:false,//分享蒙版显示
        headImage:null,
    },
    created:function(){
        
        if(window.location.href.match(/(tag=3)/g)){
            this.tag = 2;
            this.navChange('',1);
            getScroll(this);
            
        }else{
            this.tag = 1;
            getPage(this);
            //加载
            getScroll(this);
        }
        
    },
    methods:{
        //弹窗分享
        setShowShare: function() {
            this.isShowShare = !this.isShowShare;
        },
        //切换nav
        navChange:function(status,index){
            this.nav_index = index;
            this.status = status;
            this.page = 1;
            hstool.load();
            if(this.nav_index==1){
                this.tag = 2;
                this.groupList=[];
                getOrder(this);
            }else{
                this.tag = 1;
                this.groupList=[];
                getPage(this);
            }
        },
        //邀请好友拼团
        getShare:function(item){
            this.share = item.shareData;
            this.shareShow = true;
            this.setShowShare();
            this.share.share_url = this.host + "shop/meeting/groupon/" + item.id+"/"+this.wid+"?group_type=2"+'&_pid_='+ pid;
            wxShare();
        },
        //分享隐藏
        shareHide:function(){
            this.shareShow = false;
        },
        //弹窗
        popUp:function(){
            if(wid == "661"){
                $('#pop_up_click .pop_content .title').text('了解微信微商城');
            }else if(wid == '626'){
                $('#pop_up_click .pop_content .title').text('了解总裁班课程');
            }else{
                $('#pop_up_click .pop_content .title').text('了解微信小程序');
            }
            
            $('#pop_up_click').show()
        },
        //关闭弹窗
        popClose:function(){
            $('#pop_up_click').hide()
            $('#pop_up').hide()
        }
    }
})
//获取页面信息
function getPage(that){
    //获取拼团列表
    var pinTuan = that.host+"shop/meeting/groups/myGroups?status="+that.status+"&page="+that.page;
    that.$http.get(pinTuan).then(function (res) {
        request = true;
        // that.tag = res.data.data.tag;
        if (res.body.status == 1) {
            var list  = res.body.data[0];
            // that.wid = res.body.data[1];
            //状态处理
            for (var i = 0; i < list.length;i ++){
                //团详情类型  分享2 待成团1 已成团3  拼团失败4   group_type
                if (list[i].status == 1){
                    list[i].statusText = "待成团";
                    list[i].group_type = 1;
                } else if (list[i].status == 2) {
                    list[i].statusText = "已完成";
                    list[i].group_type = 3;
                } else if (list[i].status == 3) {
                    list[i].statusText = "拼团失败";
                    list[i].group_type = 4;
                }
                // list[i].headImage = list[i].detail.filter(v=>v.is_head==1)[0].nickname;
                for(var a=0,l=list[i].detail.length;a<l;a++){
                    if(list[i].detail[a].is_head==1){
                        list[i].headImage=list[i].detail[a].nickname
                    }
                }
            }
            
            if (list.length < 15) {
                that.noMore = true;
            } else {
                that.noMore = false;
            }
            //若是大于1 则拼接数组
            if (that.page > 1) {
                list = that.groupList.concat(list)
            }
            that.pageShow = true;
            that.groupList = list;
            hstool.closeLoad()
        }
    });
}

//获取订单数据
function getOrder(that){
    var mineOrder = that.host+"shop/meeting/groups/myOrder?status="+that.status+"&page="+that.page;
    that.$http.get(mineOrder).then(function (res){
        request = true;
        if (res.body.status == 1) {
            var oList  = res.data.data.order;
            // that.wid = res.body.data[1];
            //状态处理
            for (var i = 0; i < oList.length;i ++){
                //付款详情类型  0 待付款 1 已付款 2 已发货 3 已完成 4 已关闭
                if (oList[i].status == 0){
                    oList[i].statusText = "待付款";
                    oList[i].group_type = 1;
                } else if (oList[i].status == 1) {
                    oList[i].statusText = "已付款";
                    oList[i].group_type = 3;
                } else if (oList[i].status == 2) {
                    oList[i].statusText = "已发货";
                    oList[i].group_type = 3;
                } else if(oList[i].status == 3){
                    oList[i].statusText = "已完成";
                    oList[i].group_type = 4;
                } else if(oList[i].status == 4){
                    oList[i].statusText = "已关闭";
                    oList[i].group_type = 5;
                }
            }

            if (oList.length < 15) {
                that.noMore = true;
            } else {
                that.noMore = false;
            }
            //若是大于1 则拼接数组
            if (that.page > 1) {
                oList = that.groupList.concat(oList)
            }
            that.pageShow = true;
            that.groupList = oList;
            hstool.closeLoad()
        }
    })
}

var request = true;
//上拉加载
function getScroll(that){
    window.onscroll=function(){
        var scrollTop = document.body.scrollTop || document.documentElement.scrollTop;
        var sH = document.documentElement.clientHeight;
        if(scrollTop + sH +50 >= document.body.scrollHeight && scrollTop > 100 && !vm.noMore && request){
            request = false;
            that.page = that.page + 1;
            if(that.tag == 1){
                getPage(that);
            }else{
                request = false;
                getOrder(that);
            }
        }
    }
}