$(function(){
    /*
    *地址栏获取字段
     */
    var key = [];
    var j = 0;
    var k = 0;
    var iarr = [];
    /*vue开始*/
    var vm = new Vue({
        el: ".sign",
        delimiters: ['[[', ']]'],//更改模板转移符
        data: {
            "signModule":{
                "showModal": false,
                "userData":{},
                "signData": {},
                "difference": 0,//差值
                "signText": "签到",
                "progressWidth" : 0,
                "progressIndex" : 0,
                "signReward" : false,
                "signRewardResidueDays" : 0,
            },
            "lists":[],
            "otherModule": [],
            "getScore": 0,
            "is_on": false,//活动开启为true 
        },
        created: function(){
            // wid= window.location.href.substring(window.location.href.lastIndexOf("/")+1);
            console.log(wid);
            var that = this;
            this.$http.get('/shop/point/selectSignTemplateData/'+wid).then(function(res){
                if(res.body.errCode != 0){
                    tool.tip(res.body.errMsg);
                    return false;
                }
                //判断商家是否设置活动
                if(res.body.signTemplateData.length == 0){
                    tool.notice(0,"提示","商家暂未设置签到功能，3秒钟后自动返回","确定",time)
                    function time(){
                        window.history.go(-1);
                    }
                    setTimeout(function(){
                        window.history.go(-1);
                    },3000);
                    return false;
                }
                /*首先判断活动是否开启*/
                if(res.body.signTemplateData.is_on != 1){
                    tool.tip("活动尚未开启");
                    return false;
                }
                this.is_on = true;
                if(res.body.signTemplateData.length!=0){
                    this.otherModule = JSON.parse(res.body.signTemplateData.template_data);
                }
                if(JSON.stringify(res.body.userData) != "{}"){
                    this.signModule.userData = res.body.userData;
                }
                /*签到*/
                this.signModule.signReward = this.otherModule[0]["signList"].length == 0 ? false : true;
                this.signModule.signReward = true;
                this.signModule.signData = res.body.signData;
                this.signModule.difference = this.signModule.signData.signDay < 6 ? 0 : this.signModule.signData.signDay-3;
                this.signModule.progressWidth = this.signModule.signData.signDay < 6 ? (this.signModule.signData.signDay * 58)  : 174;
                this.signModule.progressIndex = this.signModule.signData.signDay < 6 ? this.signModule.signData.signDay : 3;
                if(this.signModule.signData.isSign){
                    this.signModule.signText = "已签到";
                }
                /*其他模块*/
                console.log(this.otherModule)
                var content = this.otherModule;
                //处理自定义模块
                if(content.length>0){
                    for(var i = 0;i<content.length;i++){
                        if(content[i]['type'] == 'model'){
                            if(typeof(content[i].template_data) == 'string'){
                                var model_data = JSON.parse(content[i].template_data);
                            }else{
                                var model_data = content[i].template_data;
                            }
                            var data = content.slice(0,i);
                                data = data.concat(model_data);
                            var content = content.splice(i+1,content.length);
                            content = data.concat(content);
                        }
                    }
                }
                for(var i in content){
                    if(content[i]['type'] == 'goods'){
                        if(content[i]['cardStyle'] == '3' && content[i]['listStyle'] != 4){
                            content[i]['btnStyle'] = '0';
                        }
                        // 判断商品名显示
                        if(content[i]['goodName']){
                            content[i].title = 'info-title';
                        }else{
                            content[i].title = 'info-no-title'
                        }
                        // 判断商品名显示
                        // alert(content[i]['priceShow']);
                        // 判断价格显示
                        if(content[i]['priceShow']){
                            content[i].priceClass = 'info-price';
                        }else{
                            content[i].priceClass = 'info-no-price'
                        }
                        if(!content[i]['goodName'] && !content[i]['priceShow']){
                            content[i].hide_all = 'hide';
                        }
                        // 按钮显示样式
                        if(content[i]['btnStyle'] == 1){
                            content[i].btnClass = 'btn1';
                        }else if(content[i]['btnStyle'] == 2){
                            content[i].btnClass = 'btn2';
                        }else if(content[i]['btnStyle'] == 3){
                            content[i].btnClass = 'btn3';
                        }else if(content[i]['btnStyle'] == 4){
                            content[i].btnClass = 'btn4';
                        }else{
                            content[i].btnClass = 'btn0';
                        }

                        // 判断是否有商品简介
                        if(content[i]['goodInfo']){
                            content[i].has_sub_title = 'has-sub-title';
                        }
                        if(content[i]['cardStyle'] == 1){
                            content[i].list_style = 'card';
                        }else if(content[i]['cardStyle'] == 3){
                            content[i].list_style = 'normal';
                        }else if(content[i]['cardStyle'] == 4){
                            content[i].list_style = 'promotion';
                        }
                        if(content[i].goods == undefined){
                            content[i].goods = [];
                        }
                        if(content[i]['goods'].length>0){
                            content[i]['thGoods'] = [];
                            for(var j =0; j< content[i]['goods'].length;j++){
                                content[i]['goods'][j]['thumbnail'] = imgUrl + content[i]['goods'][j]['thumbnail']
                                if(content[i].thGoods.length > 0){
                                    if(content[i]['thGoods'][content[i]['thGoods'].length - 1].length>=3){
                                        content[i]['thGoods'].push([]);
                                        content[i]['thGoods'][content[i]['thGoods'].length-1].push(content[i]['goods'][j])
                                    }else{
                                        content[i]['thGoods'][content[i]['thGoods'].length - 1].push(content[i]['goods'][j])
                                    }
                                }else{
                                    content[i]['thGoods'][0] = [];
                                    content[i]['thGoods'][0].push(content[i]['goods'][j])
                                }
                            }
                        }
                    }
                    if(content[i]['type'] == 'rich_text'){
                        //图片家域名
                        content[i]['content'] = content[i]['content'].replace(/<img [^>]*src=['"]([^'"]+)[^>]*>/gi, function (match, capture) {
                          if(capture.indexOf('http') == -1){
                            var newSrc =  CDN_IMG_URL.substr(0,CDN_IMG_URL.length-1) + capture;
                            match = match.replace(capture,newSrc)
                          }
                          return match
                        });
                        // 视频添加域名
                        content[i]['content'] = content[i]['content'].replace(/<video [^>]*src=['"]([^'"]+)[^>]*>/gi, function (match, capture) {
                          if(capture.indexOf('http') == -1){
                            var newSrc =  CDN_IMG_URL.substr(0,CDN_IMG_URL.length-1) + capture;
                            match = match.replace(capture,newSrc)
                          }
                          return match
                        });
                    }
                    // 标题
                    if(content[i]['type'] == 'title'){
                        if(content[i]['titleStyle'] == 2){
                            content[i]['bgColor'] = '#fff';
                        }
                    }
                    if(content[i]['type'] == 'spell_goods'){
                        if(content[i]['groups'].length){
                            for(var j = 0;j<content[i]['groups'].length;j++){
                                if(content[i]['groups'][j]['member'] == undefined){
                                    content[i]['groups'][j]['member'] = [];
                                }
                            }
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
                            // console.log(content[i]['left_nav']);
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
                    this.lists.push(content[i]);
                    if(content[i]['type'] == 'image_ad'){
                        //console.log(content[i])
                        if(content[i].images.length>0){
                            for(var j = 0;j<content[i].images.length;j++){
                                if(content[i].images[j]['FileInfo']['path'].indexOf(imgUrl)>=0){
                                    content[i].images[j]['FileInfo']['path'] = content[i].images[j]['FileInfo']['path'];
                                }else{
                                    content[i].images[j]['FileInfo']['path'] = imgUrl + content[i].images[j]['FileInfo']['path'];
                                }
                            }
                        }
                        if(content[i]['advsListStyle'] == 2){
                            setTimeout(function(){
                                var swiper = new Swiper('.swiper-container', {
                                    pagination: '.swiper-pagination',
                                    paginationClickable: true,
                                    autoplay:2000,
                                    loop:true
                                });
                            },1000)
                        }
                    }
                }
            }); 
        },
        computed: {
            /*签到剩余天数计算*/
            signRewardResidueDays: function(){
                for(var i = 0 ; i < this.otherModule[0]["signList"].length; i ++){
                    if((this.otherModule[0]["signList"][i]["signDay"] - this.signModule.signData.signDay) > 0){
                        var signRewardResidueDays = this.otherModule[0]["signList"][i]["signDay"] - this.signModule.signData.signDay;
                        break;
                    }
                };
                if(i ==  this.otherModule[0]["signList"].length && !signRewardResidueDays){
                    this.signModule.signReward = false;
                    return false;
                }
                return signRewardResidueDays;
            }
        },
        methods: {
            sign: function(){
                var that = this;
                //判断是否绑定手机号
                // if(isBind){
                //     tool.bingMobile(function(){
                //         isBind = 0;
                //         that.goSign();
                //     })
                //     return;
                // }
                that.goSign();
            },
            goSign: function(){
                if(this.signModule.signText == "签到"){
                    if(!this.is_on){
                        tool.tip("活动尚未开启");
                        return false;
                    }
                    this.$http.get('/shop/point/addSignRecord/'+wid).then(function(data){
                        console.log(data.body)
                        var data = data.body;
                        if(data.errCode == 0){
                            this.signModule["showModal"] = true;
                            this.signModule["signText"] = "已签到";
                            this.signModule.signData.signDay = data.data.signDay;
                            this.signModule.difference = this.signModule.signData.signDay < 6 ? 0 : this.signModule.signData.signDay - 3;
                            this.signModule.progressWidth = this.signModule.signData.signDay < 6 ? (this.signModule.signData.signDay * 58)  : 174;
                            this.signModule.progressIndex = this.signModule.signData.signDay < 5 ? this.signModule.signData.signDay : 3;
                            if(this.signModule.userData.score != data.data.score){
                                this.getScore = data.data.score - this.signModule.userData.score;
                                this.signModule.userData.score = data.data.score;
                            }
                        }else{
                            tool.tip(data.errMsg)
                        }
                    });

                }
            },
            hideModule: function(){
                this.signModule["showModal"] = false;
            },
            /*其他模块*/
            // 设置加入购物弹窗data
            setGoodData:function(data){
                // console.log(data);
                this.goodData = data;
                console.log(this.goodData);
            },
            hideGoodModel:function(){
              this.goodData = null;
            }
        }
    })
    

});
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
// 点击图片查看大图 add by 黄新琴 2018/9/4
$('body').on('click','.J_parseImg',function(){
    var nowImgurl = $(this).data('src');
    wx.previewImage({
        "urls":[nowImgurl],
        "current":nowImgurl
    });
});
/*
* @auther 黄新琴
* @desc 富文本图片点击放大
* @date 2018-10-18
* */
$('body').on('click','.js-custom-richtext',function(){
    var imgs = [];
    var imgObj = $(this).find('img');
    for(var i=0; i<imgObj.length; i++){
        imgs.push(imgObj.eq(i).attr('src'));
        imgObj.eq(i).click(function(){
            var nowImgurl = $(this).attr('src');
            wx.previewImage({
                "urls":imgs,
                "current":nowImgurl
            });
        });
    }
});