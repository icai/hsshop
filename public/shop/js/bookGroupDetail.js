/*设置请求头部*/
Vue.http.options.emulateHTTP = true;
Vue.http.options.emulateJSON = true;
//post要求的请求token
Vue.http.options.headers = { 'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content") };

function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
}

var app = new Vue({
    el: "#app",
    data: {
        imgUrl: imgUrl, //动态图片路径前缀
        _host: _host, //页面路由前缀
        isShowInvite: false, //是否显示邀请拼团弹窗 
        isShowPeople: false, //是否显示拼团成员弹窗
        isShowNotice: false, //是否显示拼团须知弹框
        isShowShare: false, //是否显示分享引导弹窗
        isShowSever: false, //是否显示服务弹窗
        group_type: getQueryString("group_type"), //1.团长支付成功  2.通过分享进来  3.拼团成功  4.拼团失败 5.团已满
        groups_id: 0, //团id
        ruleId: null, //团规则id
        list: {}, //团详情信息
        gpList: {}, //其他人的团信息
        tjList: [], //推荐信息  
        groupEtime: null, //团剩余时间
        isRefresh:false,  //倒计时时间结束时是否刷新
        surplus: -1,//拼团剩余能够买数量
    },
    created: function() {
        this.groups_id = groups_id;
        this.getGroupDetailInfo();
        this.getRecommendInfo();
    },
    methods: {
        //获取推荐信息 
        getRecommendInfo: function() {
            var that = this;
            var url = "/shop/web/groups/recommendGroups";
            that.$http.get(url, {}).then(function(res) {
                if (res.body.status == 1) {
                    that.tjList = res.body.data;
                } else {
                    tool.tip(res.body.info);
                }
            });
        },
        /**
         * 设置团购状态 
         * @param status 0.未开团 1.待成团 2.已成团 3.未成团
         * @param is_join 是否自己参团 0.否 1.是 
         */
        setGroupStatus: function(status, is_join) {
            var that = this; 
            switch (status) {
                case "1":
                    if (is_join == 1) {
                        that.group_type = 1;
                    }
                    break;
                case "2":
                    if (is_join == 1) {
                        that.group_type = 3;
                    } else {
                        that.group_type = 5;
                        that.getGroupPeopleInfo();
                    }
                    that.isShowInvite = false;
                    break;
                case "3":
                    that.group_type = 4;
                    break;
            }
        },
        //获取团购信息 
        getGroupDetailInfo: function() {
            var that = this;
            var url = "/shop/web/groups/groupsDetail/" + that.groups_id;
            hstool.load();
            that.$http.get(url, {}).then(function(res) {
                if (res.body.status == 1) {
                    that.setGroupStatus(res.body.data.groups.status, res.body.data.groups.is_join);
                    that.list = res.body.data;
                    that.ruleId = res.body.data.groups.rule_id;
                    that.getrtime((new Date(res.body.data.groups.now_time)).getTime(), (new Date(res.body.data.groups.end_time)).getTime(), 0);
                    that.surplus = res.body.data.surplus;
                } else {
                    tool.tip(res.body.info);
                }
                setTimeout(function() {
                    hstool.closeLoad();
                }, 100);
            });
        },
        //获取拼团人信息 
        getGroupPeopleInfo: function() {
            var that = this;
            var url = "/shop/web/groups/getGroups/" + that.ruleId;
            hstool.load();
            that.$http.get(url, {}).then(function(res) {
                if (res.body.status == 1) {
                    that.gpList = res.body.data;
                } else {
                    tool.tip(res.body.info);
                }
                setTimeout(function() {
                    hstool.closeLoad();
                }, 100);
            });
        },
        //跳转到订单详情页
        gotoOrderDetail: function() {
            location.href = "/shop/order/groupsOrderDetail/" + this.list.groups.oid;
        },
        //跳转到详情页
        gotoDetail: function() {
            location.href = "/shop/grouppurchase/detail/" + this.list.rule.id;
        },
        //倒计时函数 
        getrtime: function(ntime, etime, time) {
            var that = this;
            ntime += time;
            var t = etime - ntime;
            if (t >= 0) {
                that.isRefresh = true;
                var h = Math.floor(t / 1000 / 60 / 60 % 24) < 10 ? "0" + Math.floor(t / 1000 / 60 / 60 % 24) : Math.floor(t / 1000 / 60 / 60 % 24);
                var m = Math.floor(t / 1000 / 60 % 60) < 10 ? "0" + Math.floor(t / 1000 / 60 % 60) : Math.floor(t / 1000 / 60 % 60);
                var s = Math.floor(t / 1000 % 60) < 10 ? "0" + Math.floor(t / 1000 % 60) : Math.floor(t / 1000 % 60);
                that.groupEtime = h + ":" + m + ":" + s;
                setTimeout(function() {
                    that.getrtime(ntime, etime, 1000);
                }, 1000);
            }else{
                if(that.isRefresh)
                    location.reload();
            }
        },
        //关闭或者开启弹框
        setShowSever: function() {
            this.isShowSever = !this.isShowSever;
        },
        setShowPeople: function() {
            this.isShowPeople = !this.isShowPeople;
        },
        setShowNotice: function() {
            this.isShowNotice = !this.isShowNotice;
        },
        setShowInvite: function() {
            this.isShowInvite = !this.isShowInvite;
        },
        setShowShare: function() {
            this.isShowShare = !this.isShowShare;
        },
        //一键开团
        groupPurchaseBuy: function() {
            var that = this;
            if (that.list.rule.is_over == 1) {
                location.href = "/shop/index/" + wid;
            } else {
                tool.spec.open({
                    "type": 2,
                    "url": "/shop/web/groups/getSkus/" + that.ruleId, //获取规格接口 
                    "data": {
                        "_token": $("meta[name='csrf-token']").attr("content")
                    },
                    "initSpec": { // 默认商品数据
                        "title": that.list.rule.title,
                        "img": that.list.rule.product.img,
                        "stock": that.list.rule.product.stock,
                        "price": that.list.rule.min,
                    },
                    "limit_num": that.list.rule.num,
                    "limit_type": that.list.rule.limit_type,// 0每单  1每人 -1不限制
                    "surplus_num": that.surplus,// 剩余用户能购买数量（每人） 
                    "unActive": 1, //非拼团活动
                    "isEdit": true, //点x  不保存数据
                    "buyCar": false, //按钮  为单按钮  加入购物车  可不写
                    "noteList": that.list.rule.product.noteList,
                    "pid":that.list.rule.product.id,
                    callback: function(res) {
                        if(that.surplus == 0 && that.list.rule.limit_type == 1){
                            tool.tip("该商品已超过限购数量");
                            return false;
                        }
                        if (res.status == 1) {
                            tool.spec.close();
                            location.href = '/shop/web/groups/getSettlementInfo?pid=' + that.list.rule.product.id + '&rule_id=' + that.ruleId + '&sku_id=' + res.data.spec_id + '&num=' + res.data.num + '&limit_num=' + that.list.rule.num;
                        }
                    }
                });
            }

        },
        groupPurchaseBuy2: function() {
            var that = this;
            // if(isBind){
            //     tool.bingMobile(function(){
            //         isBind = 0;
            //         that.groupPurchaseToBuy(that);
            //     })
            //     return;
            // }
            that.groupPurchaseToBuy(that);
            
        },
        groupPurchaseToBuy:function(that){
            tool.spec.open({
                "type": 2,
                "url": "/shop/web/groups/getSkus/" + that.ruleId, //获取规格接口 
                "data": {
                    "_token": $("meta[name='csrf-token']").attr("content")
                },
                "initSpec": { // 默认商品数据
                    "title": that.list.rule.title,
                    "img": that.list.rule.product.img,
                    "stock": that.list.rule.product.stock,
                    "price": that.list.rule.min,
                },
                "limit_num": that.list.rule.num,
                "unActive": 1, //非拼团活动
                "isEdit": true, //点x  不保存数据
                "buyCar": false, //按钮  为单按钮  加入购物车  可不写
                "noteList": that.list.rule.product.noteList,
                "pid":that.list.rule.product.id,
                callback: function(res) {
                    if(that.surplus == 0 && that.list.rule.limit_type == 1){
                        tool.tip("该商品已超过限购数量");
                        return false;
                    }
                    if (res.status == 1) {
                        tool.spec.close();
                        location.href = '/shop/web/groups/getSettlementInfo?pid=' + that.list.rule.product.id + '&rule_id=' + that.ruleId + '&sku_id=' + res.data.spec_id + '&num=' + res.data.num + '&limit_num=' + that.list.rule.num;
                    }
                }
            });
        },
        //一键参团
        groupPurchaseBuy1: function() {
            var that = this;
            tool.spec.open({
                "type": 2,
                "url": "/shop/web/groups/getSkus/" + that.ruleId, //获取规格接口 
                "data": {
                    "_token": $("meta[name='csrf-token']").attr("content")
                },
                "initSpec": { // 默认商品数据
                    "title": that.list.rule.title,
                    "img": that.list.rule.product.img,
                    "stock": that.list.rule.product.stock,
                    "price": that.list.rule.min,
                },
                "limit_num": that.list.rule.num,
                "limit_type": that.list.rule.limit_type,// 0每单  1每人 -1不限制
                "surplus_num": that.surplus,// 剩余用户能购买数量（每人） 
                "unActive": 1, //非拼团活动
                "isEdit": true, //点x  不保存数据
                "buyCar": false, //按钮  为单按钮  加入购物车  可不写
                "noteList": that.list.rule.product.noteList,
                "pid":that.list.rule.product.id,
                callback: function(res) {
                    if(that.surplus == 0 && that.list.rule.limit_type == 1){
                        tool.tip("该商品已超过限购数量");
                        return false;
                    }
                    if (res.status == 1) {
                        tool.spec.close();
                        location.href = '/shop/web/groups/getSettlementInfo?pid=' + that.list.rule.product.id + '&rule_id=' + that.ruleId + '&sku_id=' + res.data.spec_id + '&num=' + res.data.num + '&limit_num=' + that.list.rule.num + '&groups_id=' + that.groups_id;
                    }
                }
            });
        }

    }
});