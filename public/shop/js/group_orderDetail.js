var _token = $('meta[name="csrf-token"]').attr('content');
var vm = new Vue({
    el:"#page",
    data:{
        host:host,//域名
        imgUrl:imgUrl,//动态图片地址
        orderDetail:{},//订单信息
        opt_btn:false,//操作按钮 默认隐藏
        pageShow:false,//页面展示  默认false
        add_field:{},//订单附加字段
        group_status:3,//团状态，  默认成功
        logistics:false,//物流状态
        logisticsInfo:[],//物流信息
        group_info:null,//团购信息
        recommendGroups:[],//推荐信息
        share:null,//分享信息
        shareShow:false,//分享展示
        isRefundBtnShow: true,//退款申请按钮 显示判断
        show_no_express:0,// 无需物流显示
        shareTip: false,
        bgZhezhao: false,
        pageData:pageData, //页面返的数据
        order_m:'60',
        order_s:'00',
        downTime:'',
        span_show:false,
        balanceMoney: balance,
        selectPayType: 1, //支付类型
        selectPayTypeOff: 1,
        payShow: false
    },
    delimiters: ['[[', ']]'],
    created:function(){
        //页面信息处理
        var that = this;
        var orderDetail = pageData.orderDetail;
        //canRefund:1 可退款 0 不可退款  处理确认十五天后不能退款
        //退款申请按钮 显示判断
        if (orderDetail.canRefund == 0) {
            that.isRefundBtnShow = false;
        }
        if (orderDetail.status == 0) {//待付款
            orderDetail.statusText = "待支付";
            that.opt_btn = true;
                that.add_field = {
                "img":"shop/images/order_wait_pay.png",
                "title":"等待买家付款",
                "info":""
            }
            that.span_show = true
            var str = orderDetail.created_at.replace(/\-/g, "/");
            // var str = '2018-5-10 14:22:00';
            var times = new Date(str).getTime()
            var countdown = 60 * 60 * 1000
            var newTimes = new Date().getTime()
            var orderTime = newTimes - times
            that.downTime = countdown - orderTime
            var setTimes = setInterval(function () {
                that.downTime = that.downTime - 1000
                that.order_m = Math.floor(that.downTime / 1000 / 60 % 60)
                that.order_s = Math.floor(that.downTime / 1000 % 60)
                if (that.order_m < 10) {
                    that.order_m = '0' + that.order_m
                }
                if (that.order_s < 10) {
                    that.order_s = '0' + that.order_s
                }
                if(that.downTime < 0) {
                    that.span_show = false
                    clearInterval(setTimes)
                }
            }, 1000)
        } else if (orderDetail.status == 1) {//待发货
            if (orderDetail.groups_status == 1) {
                orderDetail.statusText = "拼团中";
                that.add_field = {
                    "img": "shop/images/pintuan-process.png",
                    "title": "拼团中",
                    "info": "快叫小伙伴来拼团吧"
                }
                that.group_status = 1;
            } else if (orderDetail.groups_status == 2) {
                if (orderDetail.is_open_draw == 1) {//开启抽奖活动   抽奖活动失败直接关闭订单  待发货，待收货，评论状态   中奖状态等级最高 
                    orderDetail.statusText = "已中奖，待发货";
                    that.add_field = {
                        "img": "shop/images/order-fahuo.png",
                        "title": "已中奖，待发货",
                        "info": ""
                    }
                    that.groupgroup_status = 1;
                     that.isRefundBtnShow = false;
                }else{
                    if (orderDetail.refund_status == 4 || orderDetail.refund_status == 8) {
                        orderDetail.statusText = "未发货，退款成功";
                        that.add_field = {
                          "img": "shop/images/pintuan-ytk.png",
                          "title": "未发货，退款成功",
                          "info": ""
                        }
                    }else{
                        that.group_status = 3;
                        orderDetail.statusText = "已成团,待收货";
                        that.add_field = {
                          "img": "shop/images/order-fahuo.png",
                          "title": "待收货",
                          "info": "预计拼团成功48小时内发货"
                        }
                    }
                }
            } else if (orderDetail.refund_status == 4 || orderDetail.refund_status == 8) {
                orderDetail.statusText = "未发货，退款成功";
                that.add_field = {
                    "img": "shop/images/pintuan-ytk.png",
                    "title": "未发货，退款成功",
                    "info": ""
                }
            } else if (orderDetail.groups_status == 3 && (orderDetail.refund_status == 4 || orderDetail.refund_status == 8)) {
                orderDetail.statusText = "未成团,退款成功";
                that.group_status = 4;
                that.add_field = {
                    "img": "shop/images/pintuan-ytk.png",
                    "title": "未成团，退款成功",
                    "info": ""
                }
            } 
        } else if (orderDetail.status == 2) {
            that.logistics = true;//发货之后开启物流
            that.opt_btn = true;
            if (orderDetail.is_open_draw == 1) {//开启抽奖活动   抽奖活动失败直接关闭订单  待发货，待收货，评论状态   中奖状态等级最高 
                orderDetail.statusText = "已中奖，待收货";
                that.add_field = {
                    "img": "shop/images/order-fahuo.png",
                    "title": "已中奖，待发货",
                    "info": ""
                }
                that.groupgroup_status = 1;
                that.isRefundBtnShow = false;
            }else{
                if (orderDetail.refund_status == 4 || orderDetail.refund_status == 8) {
                    orderDetail.statusText = "已发货，退款成功";
                    that.add_field = {
                      "img": "shop/images/pintuan-ytk.png",
                      "title": "已发货，退款成功",
                      "info": ""
                    }
                } else {
                    orderDetail.statusText = "待收货";
                    that.add_field = {
                      "img": "shop/images/order-fahuo.png",
                      "title": "商家已发货",
                      "info": "还剩余N天N时自动确认"
                    }
                }
                
            }
        } else if (orderDetail.status == 3) {
            that.opt_btn = true;
            that.logistics = true;//发货之后开启物流
            if (orderDetail.evaluate == 0) {
                if (orderDetail.is_open_draw == 1) {//开启抽奖活动 
                    orderDetail.statusText = "已中奖，待评价";
                    that.add_field = {
                        "img": "shop/images/pintuan-wait-pj.png",
                        "title": "已中奖，待评价",
                        "info": ""
                    }
                    that.groupgroup_status = 1;
                    that.isRefundBtnShow = false;
                }else{
                    orderDetail.statusText = "待评价";
                    that.add_field = {
                      "img": "shop/images/pintuan-wait-pj.png",
                      "title": "交易成功",
                      "info": ""
                    } 
                }
            } else if (orderDetail.evaluate == 1) {
                if (orderDetail.is_open_draw == 1) {//开启抽奖活动 
                    orderDetail.statusText = "已中奖，已评价";
                    that.add_field = {
                        "img": "shop/images/pintuan-ytk.png",
                        "title": "已中奖，已评价",
                        "info": ""
                    }
                    that.groupgroup_status = 1;
                    that.isRefundBtnShow = false;
                }else{
                    orderDetail.statusText = "已评价";
                    that.add_field = {
                        "img": "shop/images/pintuan-ytk.png",
                        "title": "交易成功",
                        "info": ""
                    }
                }
            }
        } else if (orderDetail.status == 4) {
            if (orderDetail.is_open_draw == 1 && (orderDetail.refund_status == 4 || orderDetail.refund_status == 8)) {//开启抽奖活动   
                orderDetail.statusText = "未中奖,退款成功";
                that.add_field = {
                  "img": "shop/images/pintuan-ytk.png",
                  "title": "未中奖,退款成功",
                  "info": ""
                }
                that.groupgroup_status = 1;
                that.isRefundBtnShow = false;
            }else{
                if (orderDetail.groups_status == 3 && (orderDetail.refund_status == 4 || orderDetail.refund_status == 8)) {
                    orderDetail.statusText = "未成团,退款成功";
                    that.group_status = 4;
                    that.add_field = {
                      "img": "shop/images/pintuan-ytk.png",
                      "title": "未成团，退款成功",
                      "info": ""
                    }
                }else{
                    orderDetail.statusText = "交易已取消";
                    that.opt_btn = true;
                    that.add_field = {
                        "img": "shop/images/order-close.png",
                        "title": "订单已取消",
                        "info": ""
                    }
                }
            }
        }else if (orderDetail.status == 7) {
            if (orderDetail.is_open_draw == 1) {//开启抽奖活动  
                orderDetail.statusText = "已成团，待抽奖";
                that.add_field = {
                    "img": "shop/images/pintuan-process.png",
                    "title": "已成团，待抽奖",
                    "info": ""
                }
                that.groupgroup_status = 1;
                that.isRefundBtnShow = false;
            }
        }
        //退款状态梳理
        
        switch (orderDetail.refund_status) {
            case 0://0申请退款
                orderDetail.refundOrder = 0;
                break;
            case 1://1退款处理中
            case 2:
            case 6:
            case 7:
                orderDetail.refundOrder = 1;
                break;
            case 3://2退款中
                orderDetail.refundOrder = 2;
                break;
            case 5://3重新申请退款
            case 9://3重新申请退款
                orderDetail.refundOrder = 3;
                break;
            case 4://4退款成功
            case 8://4退款成功
                orderDetail.refundOrder = 4;
                break;
            default:
                // statements_def
                break;
        }
        //以下状态不存在退款流程
        if(orderDetail.statusText=='未成团,退款成功' || orderDetail.statusText=='已发货，退款成功' || orderDetail.statusText=='待支付' || orderDetail.statusText=='拼团中'){
            orderDetail.refundOrder = -1;
        }
        if (orderDetail.nowtime > 0 && orderDetail.statusText == "待收货"){
            //开启定时器
            var surplus_time = orderDetail.nowtime * 1000;
            getrtime(surplus_time);
            //单转双
            function evenNum(num) {
               num = num < 10 ? "0" + num : num;
               return num;
            }
            //秒杀倒计时
            function getrtime(time) {
                if (time >= 0) {
                    var d = evenNum(Math.floor(time / 1000 / 60 / 60 / 24));
                    var h = evenNum(Math.floor(time / 1000 / 60 / 60 % 24));
                    var m = evenNum(Math.floor(time / 1000 / 60 % 60));
                    var s = evenNum(Math.floor(time / 1000 % 60));
                    that.add_field.info = '还剩余' + d + '天' + h + '时' + m + '分' + s + '秒自动确认';
                    setTimeout(function(){
                      time -= 1000;
                      getrtime(time)
                    },1000)
                }else{
                    getPage(that);
                }
            }
        }
        that.orderDetail = orderDetail;
        
        //获取物流信息
        this.$http.get(this.host+"shop/order/getLogistics/"+orderDetail.wid+"/"+orderDetail.id).then(function (res) {
            if(res.body.status == 1){
                this.logisticsInfo = res.body.data;
                this.show_no_express = res.body.data[0].no_express;
            }
        });
        //获取团购信息
        this.$http.get(this.host+"shop/web/groups/groupsById/"+ orderDetail.groups_id).then(function (res) {
            if (res.body.status == 1) {
                this.group_info = res.body.data;
            }
        });
        //获取分享信息
        this.$http.get(this.host+"shop/web/groups/getShareData/"+ orderDetail.groups_id).then(function (res) {
            if (res.body.status == 1) {
                share_title = res.body.data.share_title;
                share_img = res.body.data.share_img;
                share_desc = res.body.data.share_desc;
                share_url = host +"/shop/grouppurchase/groupon/"+this.orderDetail.groups_id+"/"+this.orderDetail.wid+"?group_type=2"
                //微信分享
                wsshare();
            }
        });
        //获取推荐信息
        this.$http.get(this.host+"shop/web/groups/recommendGroups").then(function (res) {
            that.pageShow = true;
            if (res.body.status == 1) {
                this.recommendGroups = res.body.data;
            }
        });        
    },
    methods:{
        selectPay: function(num) {
            this.selectPayTypeOff = num
            this.selectPayType = num
        },
        closePay: function() {
            this.payShow = false
        },
        immediately_pay: function(){
            this.payShow = true
        },
        //去支付
        goBuy:function(id, num){
            if (reqFrom == 'aliapp') {
                if(num == 1){
                    if(Number(this.balanceMoney) < Number(this.orderDetail.pay_price)){
                        tool.tip('余额不足');
                        return;
                    }
                    window.location.href= '/shop/pay/index?id='+ id +'&payment=' + 3;
                }else{
                    my.navigateTo({url:'/pages/shop/alipay/alipay?id='+id});
                }
            }
            /*
             * add by 韩瑜 
             * data 2018-10-17
             * 跳转百度小程序
             */
            else if (reqFrom == 'baiduapp') {
                if(num == 1){
                    if(Number(this.balanceMoney) < Number(this.orderDetail.pay_price)){
                        tool.tip('余额不足');
                        return;
                    }
                    window.location.href= '/shop/pay/index?id='+ id +'&payment=' + 3;
                }else{
                    swan.webView.navigateTo({url: '/pages/baidupay/baidupay?id='+id});
                }
            }
            else {
                if(this.selectPayType == 1){
                    if(Number(this.balanceMoney) < Number(this.orderDetail.pay_price)){
                        tool.tip('余额不足');
                        return;
                    }
                    window.location.href= '/shop/pay/index?id='+ id +'&payment=' + 3;
                }else{
                    window.location.href = '/shop/pay/index?id=' + id + '&payment=' + 1;
                }
            }
              
        },
        //立即评价
        appraise:function(oid, wid) {
            location.href = "/shop/order/comment/"+wid+"?odid="+oid
        },
        //确认收货
        sureOrder:function(refund,oid,wid){
            if(refund != 0 && refund != 4 && refund != 8){
                tool.notice(1,'确认收货','您正在申请退款中,确认收货将会关闭退款','确认收货',function(){
                    $.ajax({
                        url:'/shop/order/received/'+wid+'/'+oid,// 跳转到 action
                        data:{
                            _token:_token
                        },
                        type:'post',
                        cache:false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType:'json',
                        success:function (response) {
                            if (response.status == 1){
                                tool.tip(response.info);
                                location.reload();
                                return false;
                            }else{
                                tool.tip(response.info);
                                return false;
                            }
                        },
                        error : function() {
                            // view("异常！");
                            tool.tip("异常！");
                        }
                    });
                }); 
            }else{
                tool.notice(1,'确认收货','确认收货后，订单交易完成，钱款将立即到达商家账户。','确认收货',function(){
                    $.ajax({
                        url:'/shop/order/received/'+wid+'/'+oid,// 跳转到 action
                        data:{
                            _token:_token
                        },
                        type:'post',
                        cache:false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType:'json',
                        success:function (response) {
                            if (response.status == 1){
                                tool.tip(response.info);
                                location.reload();
                                return false;
                            }else{
                                tool.tip(response.info);
                                return false;
                            }
                        },
                        error : function() {
                            // view("异常！");
                            tool.tip("异常！");
                        }
                    });
                }); 
            }
            
        },
        //邀请好友拼团
        getShare:function(){
            this.shareShow = true;
        },
        //分享隐藏
        shareHide:function(){
            this.shareShow = false;
        },
        //取消订单
        cancle:function(wid,oid) {
            tool.notice(1,'','确定取消订单？','确认',function(){
                $.ajax({
                    url:"/shop/order/cancle/"+wid+"/"+oid,// 跳转到 action
                    data:{
                        _token:_token
                    },
                    type:'post',
                    cache:false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType:'json',
                    success:function (response) {
                        if (response.status == 1){
                            tool.tip("取消订单成功");
                            location.reload();
                            return false;
                        }else{
                            tool.tip(response.info);
                            return false;
                        }
                    },
                    error : function() {
                        // view("异常！");
                        tool.tip("异常！");
                    }
                });
            });
            
        },
        //延长发货
        delay:function(oid, wid) {
            var that = this;
            that.$http.post('/shop/order/receiveDelay/'+wid+'/'+oid, {
                _token: _token
            }).then(function(res){
                if(res.body.status==0){
                    that.toastHint(res.body.info);
                }
            })
        },
        bgClick: function(event) {
            this.bgZhezhao = false;
            this.shareTip = false
        }
    }
})
//复制核销码
var clipboard = new Clipboard(".copy");
clipboard.on('success', function(e) {
    tool.tip("复制成功");
});

clipboard.on('error', function(e) {
    tool.tip("复制失败，请手动复制。"); 
});