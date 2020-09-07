"use strict";
$(function(){
    //懒加载
    $('.lazyload').picLazyLoad({
        threshold: 200,
        effect : "fadeIn"
    });
	//立即抢购点击事件
	$(".js-buy-it").click(function(){
		tool.tip('预览不支持改操作,<br />实际效果请再手机上进行。');
	});  
	handleSeckillStatus(status);
	//根距活动状态作出各种处理
    function handleSeckillStatus(status){
        status = status?parseInt(status) : 2; 
        switch(status){
            case 1: //未开始 显示原价购买 
            	$(".responsive-wrapper").eq(1).removeClass('hide').siblings().addClass('hide'); 
                $('.goods-activity').addClass('theme-orange');
                $(".countdown-title").html("距开始仅剩");
                getrtime(stime);//倒计时
                break;
            case 2: //进行中
            	$(".responsive-wrapper").eq(0).removeClass('hide').siblings().addClass('hide'); 
                $('.goods-activity').removeClass('theme-orange');
                $(".countdown-title").html("距结束仅剩");
                getrtime(overtime);//倒计时
                break;
            case 3: //已结束  正常商品预览页还没做好 
            	$(".responsive-wrapper").eq(2).removeClass('hide').siblings().addClass('hide');
                break;
            case 4: //失效
            	$(".responsive-wrapper").eq(2).removeClass('hide').siblings().addClass('hide'); 
                break;
        }
    }
});
//倒计时

function getrtime(time){ 
	var endTime = new Date(time);
	var t = endTime.getTime() - nowTime;
	if(t>=0){
		var h = Math.floor(t/1000/60/60);
		var m = Math.floor(t/1000/60%60);
		var s = Math.floor(t/1000%60);
		$(".js-span-h").html(h);
      	$(".js-span-m").html(m);
      	$(".js-span-s").html(s);
      	var d=Math.floor(t/1000/60/60/24);
        var h=Math.floor(t/1000/60/60%24);
        var m=Math.floor(t/1000/60%60);
        var s=Math.floor(t/1000%60);
        if(d>0){
            $(".js-span-d").html(d);
            $(".js-span-d").show();
            $(".js-i-d").show();
        }else{
            $(".js-span-d").hide();
            $(".js-i-d").hide(); 
        }
        
        $(".js-span-h").html(h);
        $(".js-span-m").html(m);
        $(".js-span-s").html(s); 
        setTimeout(function(){
            getrtime(time);
            nowTime += 1000;
        },1000);
	}else{
        location.reload();
    }
}
// 组件模块  @author huoguanghui
var key = [];
var j = 0;
var k = 0;
var n = 0;
new Vue({
    el: '#container',
    delimiters: ['[[', ']]'], 
    data:{
        lists:[],//商品详情数据    
        host: host,
        shopId:shop_id,//商品id
    },
    created: function () {
        var that = this;
        //商品详情数据  
        if(product.product.content){
            var productDetail = JSON.parse(product.product.content);
            if(productDetail.length > 0){
                componentAssign(this.lists,productDetail);
                setTimeout(function(){
                    $('img').removeAttr('width');
                    $('img').removeAttr('height');
                },1000)
            }
        } 

        /**
         * 组件赋值
         * 参数 赋值对象 赋值模板
         * 用到对象  商品的富文本自定义组件    商品页模板  广告业模板
         */
        function componentAssign(obj,template){
            var content = template;//模板遍历赋值
            for(var i =0;i < content.length;i ++){
                if(content[i] != undefined){
                    if(content[i]['type'] == 'shop_detail'){
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
                    if(content[i]['type'] == 'header'){
                        content[i]['order_link'] = '/shop/order/index/'+id;
                    }
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
                                content[i]['goods'][j]['thumbnail'] = imgUrl + content[i]['goods'][j]['thumbnail'];
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
                    if(content[i]['type'] == 'goodslist'){
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
                                content[i]['goods'][j]['thumbnail'] = imgUrl + content[i]['goods'][j]['thumbnail'];
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
                    // 标题
                    if(content[i]['type'] == 'title'){
                        if(content[i]['titleStyle'] == 2){
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
                    obj.push(content[i]);
                    if(content[i]['type'] == 'image_ad'){
                        console.log(content[i])
                        if(content[i].images.length>0){
                            for(var j = 0;j<content[i].images.length;j++){
                                obj[i].images[j]['FileInfo']['path'] = imgUrl + obj[i].images[j]['FileInfo']['path'];
                            }
                        }
                        
                    }
                    if(content[i]['type'] == 'image_link'){
                        if(content[i]['images'].length > 0){
                            for(var j=0;j<content[i]['images'].length;j++){
                                content[i]['images'][j]['thumbnail'] = imgUrl + content[i]['images'][j]['thumbnail'];
                            }
                        }
                    }
                }
            }
        }  
    },
  
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