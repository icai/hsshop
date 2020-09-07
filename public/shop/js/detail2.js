
$(function() {
    var mySwiper = new Swiper('.swiper-container', {
        autoplay: 3000, //可选选项，自动滑动
        pagination: '.swiper-pagination',
        loop: true,
    });
    // 遮罩禁止滚动
    $('body').on('touchmove','.zhezhao',function(e){
        e.preventDefault();
    })
    $('body').on('touchmove','.pinNow-zhezhao',function(e){
        e.preventDefault();
    })
})
hstool.load();
var app = new Vue({
    el: '#app',
    data: {
        bgZhezhao: false,
        fwbz: false,
        quickNavShow: false,
        mpinDan: false,
        list1: {},
        listPro: {},
        listProContent: "",
        listLable: {},
        list2: {},
        list2Data: [],
        list2Data2: [],
        surplusTime: [],
        listE: {},
        listEData: {},
        list3: {},
        show: '',
        swiperH: document.body.clientWidth,
        pageHeight: null, //页面的高度
        imgHeight: null, //拿到图票的高度
        previewImg: null,
        previewShow: false,
        lists: [], //商品详情数据
        shopId: '',
        topTip: [],
        topTipList: null,
        shareTip: false,
        groupInfo:{
            service_status:[],
            service_txt:''
        },//拼团保障
        chatUrl:'',
        showModel:false
    },
    created: function() {
        var urlTip = "/shop/meeting/groups/getGroupsMessage";
        
        this.$http.get(urlTip).then(function(res){
            this.topTip = res.body.data;
            var topTipIndex = 0;
            var that = this;
            hstool.closeLoad();
            setInterval(function(){
                if(topTipIndex >= that.topTip.length){
                    topTipIndex = 0;
                }
                that.topTipList = that.topTip[topTipIndex];
                setTimeout(function(){
                    that.topTipList = null;
                    topTipIndex++;
                },3000)
            },5000)
        })
        var url = "/shop/meeting/groups/detail/" + rule_id;
        var _self = this;
        this.$http.get(url).then(function(res) { 
            var dataTop = res.body.data;
            this.list1 = dataTop;
            tool.spec.wid = this.list1.wid;
            this.listPro = dataTop.product;
            this.listLable = dataTop.weixinLable;
            // 客服url
            this.chatUrl = CHAT_URL + '/zfb/kefu?productName=' + encodeURIComponent(dataTop.product.title) + '&productImg=' + APP_IMG_URL + dataTop.product.img +
                           '&productPrice=' + dataTop.product.price + '&productLink='+ encodeURIComponent('host='+ APP_HOST +'&id='+ dataTop.id +'&wid='+ dataTop.wid + '&type=5') +
                           '&userId=' + dataTop.member.id + '&shopId=' + dataTop.wid + '&username=' + dataTop.member.nickname +
                           '&headurl=' + dataTop.member.headimgurl + '&shopName=' + dataTop.shop.shop_name + '&shopLogo=' + APP_IMG_URL + dataTop.shop.logo + 
                           '&sign=' + dataTop.shop.sign+ '&timestp=' + Date.parse(new Date())/1000;
                                
            dataTop.service_status = dataTop.service_status.split(",");
            for(var i = 0;i<dataTop.service_status.length;i++){
                if(dataTop.service_status[i] == 1){
                    if(i==0){
                        this.groupInfo.service_status.push({title:'全场包邮'});
                    }else if(i == 1){
                        this.groupInfo.service_status.push({title:'品质保障'});
                    }else if(i == 2){
                        this.groupInfo.service_status.push({title:'七天无忧退换'});
                    }
                }
            }
            this.groupInfo.service_txt = dataTop.service_txt;
            var urle = "/shop/meeting/groups/getDetailEvaluate/" + this.list1.pid;
            this.show = 'show'
            this.$http.get(urle).then(function(resp) {
                hstool.closeLoad();
                this.listE = resp.body.data;
                this.listEData = resp.body.data.data;
            })
            //      商品详情
            var that = this;
            var product = that.listPro;
            that.shopId = that.list1.wid;
            //商品详情数据  
            if(product.content) {
                var productDetail = JSON.parse(product.content);
                if(productDetail.length > 0) {
                    this.listProContent = productDetail[0].content;
                    componentAssign(this.lists, productDetail);
                }
            }
            /**
             * 组件赋值
             * 参数 赋值对象 赋值模板
             * 用到对象  商品的富文本自定义组件    商品页模板  广告业模板
             */
            function componentAssign(obj, template) {
                var content = template; //模板遍历赋值
                for(var i = 0; i < content.length; i++) {
                    if(content[i] != undefined) {
                        if(content[i]['type'] == 'header') {
                            content[i]['order_link'] = '/shop/order/index/' + id;
                        }
                        if(content[i]['type'] == 'goods') {
                            if(content[i]['cardStyle'] == '3' && content[i]['listStyle'] != 4) {
                                content[i]['btnStyle'] = '0';
                            }
                            // 判断商品名显示
                            if(content[i]['goodName']) {
                                content[i].title = 'info-title';
                            } else {
                                content[i].title = 'info-no-title'
                            }
                            // 判断商品名显示
                            // alert(content[i]['priceShow']);
                            // 判断价格显示
                            if(content[i]['priceShow']) {
                                content[i].priceClass = 'info-price';
                            } else {
                                content[i].priceClass = 'info-no-price'
                            }
                            if(!content[i]['goodName'] && !content[i]['priceShow']) {
                                content[i].hide_all = 'hide';
                            }
                            // 按钮显示样式
                            if(content[i]['btnStyle'] == 1) {
                                content[i].btnClass = 'btn1';
                            } else if(content[i]['btnStyle'] == 2) {
                                content[i].btnClass = 'btn2';
                            } else if(content[i]['btnStyle'] == 3) {
                                content[i].btnClass = 'btn3';
                            } else if(content[i]['btnStyle'] == 4) {
                                content[i].btnClass = 'btn4';
                            } else {
                                content[i].btnClass = 'btn0';
                            }

                            // 判断是否有商品简介
                            if(content[i]['goodInfo']) {
                                content[i].has_sub_title = 'has-sub-title';
                            }
                            if(content[i]['cardStyle'] == 1) {
                                content[i].list_style = 'card';
                            } else if(content[i]['cardStyle'] == 3) {
                                content[i].list_style = 'normal';
                            } else if(content[i]['cardStyle'] == 4) {
                                content[i].list_style = 'promotion';
                            }
                            if(content[i].goods == undefined) {
                                content[i].goods = [];
                            }
                            if(content[i]['goods'].length > 0) {
                                content[i]['thGoods'] = [];
                                for(var j = 0; j < content[i]['goods'].length; j++) {
                                    content[i]['goods'][j]['thumbnail'] = imgUrl + content[i]['goods'][j]['thumbnail'];
                                    if(content[i].thGoods.length > 0) {
                                        if(content[i]['thGoods'][content[i]['thGoods'].length - 1].length >= 3) {
                                            content[i]['thGoods'].push([]);
                                            content[i]['thGoods'][content[i]['thGoods'].length - 1].push(content[i]['goods'][j])
                                        } else {
                                            content[i]['thGoods'][content[i]['thGoods'].length - 1].push(content[i]['goods'][j])
                                        }
                                    } else {
                                        content[i]['thGoods'][0] = [];
                                        content[i]['thGoods'][0].push(content[i]['goods'][j])
                                    }
                                }
                            }
                        }
                        if(content[i]['type'] == 'goodslist') {
                            if(content[i]['cardStyle'] == '3' && content[i]['listStyle'] != 4) {
                                content[i]['btnStyle'] = '0';
                            }
                            // 判断商品名显示
                            if(content[i]['goodName']) {
                                content[i].title = 'info-title';
                            } else {
                                content[i].title = 'info-no-title'
                            }
                            // 判断商品名显示
                            // alert(content[i]['priceShow']);
                            // 判断价格显示
                            if(content[i]['priceShow']) {
                                content[i].priceClass = 'info-price';
                            } else {
                                content[i].priceClass = 'info-no-price'
                            }
                            if(!content[i]['goodName'] && !content[i]['priceShow']) {
                                content[i].hide_all = 'hide';
                            }
                            // 按钮显示样式
                            if(content[i]['btnStyle'] == 1) {
                                content[i].btnClass = 'btn1';
                            } else if(content[i]['btnStyle'] == 2) {
                                content[i].btnClass = 'btn2';
                            } else if(content[i]['btnStyle'] == 3) {
                                content[i].btnClass = 'btn3';
                            } else if(content[i]['btnStyle'] == 4) {
                                content[i].btnClass = 'btn4';
                            } else {
                                content[i].btnClass = 'btn0';
                            }

                            // 判断是否有商品简介
                            if(content[i]['goodInfo']) {
                                content[i].has_sub_title = 'has-sub-title';
                            }
                            if(content[i]['cardStyle'] == 1) {
                                content[i].list_style = 'card';
                            } else if(content[i]['cardStyle'] == 3) {
                                content[i].list_style = 'normal';
                            } else if(content[i]['cardStyle'] == 4) {
                                content[i].list_style = 'promotion';
                            }
                            if(content[i].goods == undefined) {
                                content[i].goods = [];
                            }
                            if(content[i]['goods'].length > 0) {
                                content[i]['thGoods'] = [];
                                for(var j = 0; j < content[i]['goods'].length; j++) {
                                    content[i]['goods'][j]['thumbnail'] = imgUrl + content[i]['goods'][j]['thumbnail'];
                                    if(content[i].thGoods.length > 0) {
                                        if(content[i]['thGoods'][content[i]['thGoods'].length - 1].length >= 3) {
                                            content[i]['thGoods'].push([]);
                                            content[i]['thGoods'][content[i]['thGoods'].length - 1].push(content[i]['goods'][j])
                                        } else {
                                            content[i]['thGoods'][content[i]['thGoods'].length - 1].push(content[i]['goods'][j])
                                        }
                                    } else {
                                        content[i]['thGoods'][0] = [];
                                        content[i]['thGoods'][0].push(content[i]['goods'][j])
                                    }
                                }
                            }
                        }
                        // 标题
                        if(content[i]['type'] == 'title') {
                            if(content[i]['titleStyle'] == 2) {
                                content[i]['bgColor'] = '#fff';
                            }
                        }
                        //商品分组
                        if(content[i]['type'] == 'good_group'){
                            if(content[i]['top_nav'].length > 0){
                                for(var z = 0;z<content[i]['top_nav'].length;z++){
                                    content[i]['top_nav'][z]['href'] = 'top_nav_'+ randomString(12);
                                    content[i]['top_nav'][z]['isActive'] =  false;
                                    content[i]['top_nav'][z]['width'] =  content[i]['width'] + '%';
                                    if(z == 0){
                                        content[i]['top_nav'][z]['isActive'] =  true;
                                    }
                                    if(content[i]['group_type'] == 2 && content[i]['top_nav'][z]['goods'].length>0){
                                        for(var j = 0;j<content[i]['top_nav'][z]['goods'].length;j++){
                                            content[i]['top_nav'][z]['goods'][j]['thumbnail'] = imgUrl + content[i]['top_nav'][z]['goods'][j]['thumbnail'];
                                            if(content[i]['top_nav'][z]['goods'][j]['is_price_negotiable'] == 1){
                                                content[i]['top_nav'][z]['goods'][j]['price'] = content[i]['top_nav'][z]['goods'][j]['price'];
                                            }else{
                                                content[i]['top_nav'][z]['goods'][j]['price'] = '￥' + content[i]['top_nav'][z]['goods'][j]['price'];
                                            }
                                        }
                                    }
                                }
                                
                            }
                            if(content[i]['left_nav'].length > 0){
                                for(var z = 0;z<content[i]['left_nav'].length;z++){
                                    content[i]['left_nav'][z]['href'] = 'top_nav_'+ randomString(12);
                                    content[i]['left_nav'][z]['isActive'] =  false;
                                    if(z == 0){
                                        content[i]['left_nav'][z]['isActive'] =  true;
                                    }
                                    if(content[i]['group_type'] == 1 && content[i]['left_nav'][z]['goods'].length>0){
                                        for(var j = 0;j<content[i]['left_nav'][z]['goods'].length;j++){
                                            content[i]['left_nav'][z]['goods'][j]['thumbnail'] = imgUrl + content[i]['left_nav'][z]['goods'][j]['thumbnail'];
                                            // content[i]['left_nav'][z]['goods'][j]['price'] = '￥' + content[i]['left_nav'][z]['goods'][j]['price'];
                                            if(content[i]['left_nav'][z]['goods'][j]['is_price_negotiable'] == 1){
                                                content[i]['left_nav'][z]['goods'][j]['price'] = content[i]['left_nav'][z]['goods'][j]['price'];
                                            }else{
                                                content[i]['left_nav'][z]['goods'][j]['price'] = '￥' + content[i]['left_nav'][z]['goods'][j]['price'];
                                            }
                                        }
                                    }
                                }
                                
                            }
                        }
                        obj.push(content[i]);
                        if(content[i]['type'] == 'image_ad') {
                            if(content[i].images.length > 0) {
                                for(var j = 0; j < content[i].images.length; j++) {
                                    obj[i].images[j]['FileInfo']['path'] = imgUrl + obj[i].images[j]['FileInfo']['path'];
                                }
                            }

                        }
                        if(content[i]['type'] == 'image_link') {
                            if(content[i]['images'].length > 0) {
                                for(var j = 0; j < content[i]['images'].length; j++) {
                                    content[i]['images'][j]['thumbnail'] = imgUrl + content[i]['images'][j]['thumbnail'];
                                }
                            }
                        }
                    }
                }
            }
        })
        var url2 = "/shop/meeting/groups/getGroups/" + rule_id
        this.$http.get(url2).then(function(res) {
            this.list2 = res.body.data;
            this.list2Data = res.body.data.data; 
            this.list2Data2 = this.list2Data.slice(0, 2);
            for(var i = 0; i < this.list2Data.length; i++) {
                var d1 = new Date(this.list2Data[i].now_time).getTime();
                var d2 = new Date(this.list2Data[i].stop_time).getTime();
                runTime = parseInt((d2 - d1) / 1000) % (86400 * 365) % (86400 * 30) % 86400;
                var hour = Math.floor(runTime / 3600) < 10 ? '0' + Math.floor(runTime / 3600) : Math.floor(runTime / 3600);
                runTime = runTime % 3600;
                var minute = Math.floor(runTime / 60) < 10 ? '0' + Math.floor(runTime / 60) : Math.floor(runTime / 60);
                runTime = runTime % 60;
                var second = runTime < 10 ? '0' + runTime : runTime;
                this.surplusTime.push(hour + ':' + minute + ':' + second);
            }
        })
        var url4 = "/shop/meeting/getGroupsNum/" + rule_id;
        setInterval(function(){
            _self.$http.get(url4).then(function(res) {
                _self.list1.pnum = res.body.data;
            })
        },3000);
        // this.resetTime()
    },
    methods: {
        resetTime:function(){
            const TIME_COUNT = math.floor(this.resetTimeCount/1000000);
            let contant = TIME_COUNT;
            this.timer = setInerval(()=>{
                if(contant>0 && contant<=TIME_COUNT){
                    this.resetSecond = contant
                    contant--
                }
            },1000)
        },
        hideModel:function(){
            this.showModel = false;
        },
        showMd:function(){
            this.showModel = true
        },
        greet: function(event) {
            // `this` 在方法里指当前 Vue 实例
            alert('Hello ' + this.name + '!')
        },
        bgClick: function(event) {
            if(!this.quickNavShow) {
                this.bgZhezhao = false;
                this.fwbz = false;
                this.mpinDan = false;
                this.shareTip = false
            }
        },
        fwbzTc: function(event) {
            var that = this;
            // that.bgZhezhao = true;
            that.fwbz = true;
        },
        //关闭服务保障 @author hgh
        closeServerModal: function(){
            this.fwbz = false;
        },
        goMorePin: function(event) {
            var that = this;
            that.mpinDan = true;
            // that.bgZhezhao = true;
        },
        closeMorePin: function() {
            this.mpinDan = false
        },
//      去拼单
        goTuanDetail: function(tcPin,tcHList){
            location.href = "/shop/meeting/groupon/" + tcPin.id +'/' + this.list1.wid + '?group_type=2';
        },
//      去首页
        goIndex: function(){
            location.href = "/shop/index/" + this.list1.wid
        },
        //预览图片
        lookImg: function(src) {
            //this.$router.push({path: "/preview_picture", query:{imgSrc: src}})
        
            this.previewShow = true;
            this.previewImg = src;
            var that = this;
            that.pageHeight = window.screen.availHeight;
            // alert(3);
            //解决安卓手机上图片的位置错位问题
            setTimeout(function() {
                that.$nextTick(function(){
                    that.imgHeight = that.$refs.img.height;
                })
            }, 50)
        },
        //隐藏预览
        previewHide: function() {
            this.previewShow = false;
        },
        buyTuan: function(event) {
            //判断是否绑定手机号
            var that = this;
            // if(isBind){
            //     tool.bingMobile(function(){
            //         isBind = 0;
            //         that.goBuyTuan();
            //     })
            //     return;
            // }
            this.goBuyTuan();
        },
        goBuyTuan:function(){
            var product = this.list1.product;
            var showPrice = this.list1.max ? this.list1.min + '~' + this.list1.max : this.list1.min;
            tool.spec.open({
                "type": 2,
                "callback": buyCallBack,
                "url": "/shop/grouppurchase/getSkus", //获取规格接口 
                "data": {
                    "_token": $("meta[name='csrf-token']").attr("content"),
                    rule_id: this.list1.id
                },
                "initSpec": { // 默认商品数据
                    "title": product.title,
                    "img": product.img,
                    "stock": product.stock,
                    "price": showPrice,
                },
                "limit_type": this.list1.limit_type,// 0每单  1每人 -1不限制
                "surplus_num": this.list1.surplus,// 剩余用户能购买数量（每人） 
                "limit_num": this.list1.num,
                "unActive": 1, //非拼团活动
                "isEdit": true, //点x  不保存数据
                "buyCar": false, //按钮  为单按钮  加入购物车  可不写
                "noteList": this.listPro.noteList, //留言字段
                "pid": this.list1.pid, //商品id
                "groupNum":this.list1.pnum
            });
            var that = this;
            var pid = this.list1.pid;
            var rule_id = this.list1.id;
            var wid = this.list1.wid;
            var limit_num = this.list1.num;
            var limit_type = this.list1.limit_type;//限购类型
            var surplus_num = this.list1.surplus;//每人能够购买数量
            function buyCallBack(data) {
                if(limit_type == 1 && surplus_num == 0){//每人限购
                    tool.tip("该商品不能超过限购数量");
                    return false;
                }
                if(data.status == 1) {
                    var new_data = {
                        "id": pid,
                        "num": data.data.num,
                        "propid": data.data.spec_id,
                        "content": "", //留言  to do something
                        "rule_id": rule_id,
                        "tag": 1
                    }
                    window.location.href = data.remark_no == '' ? '/shop/meeting/groups/getSettlementInfo?pid=' + new_data.id + '&rule_id=' + new_data.rule_id + '&num=1&sku_id=' + new_data.propid + '&limit_num=' + limit_num + '&flag=1' : '/shop/meeting/groups/getSettlementInfo?pid=' + new_data.id + '&rule_id=' + new_data.rule_id + '&num=1&sku_id=' + new_data.propid + '&limit_num=' + limit_num + '&remark_no=' + data.remark_no + '&flag=1';
                }

            }
        },
        oneBuy: function(event) {
            //判断是否绑定手机号
            var that = this;
            // if(isBind){
            //     tool.bingMobile(function(){
            //         isBind = 0;
            //         that.goOneBuy();
            //     })
            //     return;
            // }
            this.goOneBuy();
            
        },
        goOneBuy:function(){
            var rule = this.list1;
            var product = rule.product;
            var showPrice = rule.product.price;
            tool.spec.open({
                "type": 1,
                "callback": singleBuy,
                "url": "/shop/product/getSku", //获取规格接口
                "data": {
                    "_token": $("meta[name='csrf-token']").attr("content"),
                    "pid": product.id
                },
                "initSpec": { // 默认商品数据
                    "title": product.title,
                    "img": product.img,
                    "stock": product.stock,
                    "price": showPrice,
                },
                "limit_num": product.quota,
                "unActive": 1, //非拼团活动
                "isEdit": true, //点x  不保存数据
                "buyCar": false, //按钮  为单按钮  加入购物车  可不写
                "pid": this.list1.pid, //商品id
                "groupNum":this.list1.pnum
            });
            var wid = this.list1.wid;

            function singleBuy(res) {
                if(res.status == 1) {
                    var data = {
                        "id": rule.product.id,
                        "num": res.data.num,
                        "propid": res.data.spec_id,
                        "content": "" //留言  to do something
                    }
                        $.ajax+({
                            url: '/shop/cart/add/' + wid + '?tag=1', // 跳转到 action
                            data: data,
                            type: 'post',
                            cache: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: 'json',
                            success: function(response) {
                                if(response.status == 1) {
                                    tool.spec.close();
                                    window.location.href = '/shop/order/waitPayOrder?cart_id=[' + response.data.id + ']';
                                } else {
                                    tool.tip(response.info);
                                }
                            },
                            error: function() {
                                tool.tip("异常！");
                            }
                        });
                }
            }
        },
        goTop: function(event) {
            document.body.scrollTop = document.documentElement.scrollTop = 0;
        },
        share: function(){
            this.shareTip = true;
            this.bgZhezhao = true;
        },
        goEval: function(){
            location.href = "/shop/product/showProductEvaluate/" + this.list1.pid;
        },
        goShopDetail: function(rList){
            location.href = "/shop/meeting/detail/" + rList.id + '/' + rList.wid;
        }
    }
})
function randomString(len) {  
　　len = len || 32;  
　　var $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';  
　　var maxPos = $chars.length;  
　　var pwd = '';  
　　for (i = 0; i < len; i++) {  
        //0~32的整数  
　　　　pwd += $chars.charAt(Math.floor(Math.random() * (maxPos+1)));  
　　}  
　　return pwd;  
}