$(function(){
    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
        autoplay:3000,
        speed:1000,
        observer:true,//修改swiper自己或子元素时，自动初始化swiper 解决个别不loop
        loop:true
    });
});
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
        productTemplate:[],//商品模板数据   
        productAd:[],//商品广告数据   
        productAdPosition: 1,//商品广告位置  1 头部 2 底部 
        commentList: [],//评论数 
        wid: $("#wid").val()//店鋪id
    },
    created: function () {
        //商品详情数据  
        if(product){
            var productDetail = JSON.parse(product);
            console.log(productDetail)
            if(productDetail.length > 0){
                componentAssign(this.lists,productDetail);
            }
        }
        //公共广告数据  
        if(microPageNotice.errCode == 0 && microPageNotice.data.length > 0){
            var ad = microPageNotice.data.noticeTemplateData;
            if(ad){
                ad = JSON.parse(ad);
                componentAssign(this.productAd,ad);
                this.productAdPosition = microPageNotice.data.position;
                var swiper = new Swiper('.swiper-container', {
                    pagination: '.swiper-pagination',
                    paginationClickable: true,
                    autoplay:5000,
                    speed:1000,
                    loop:true
                });
            }
        }
        
        //商品模板数据
        if(productDetailTemplate.product_template_info){
            var productTemplate = JSON.parse(productDetailTemplate.product_template_info);
            if(productDetailTemplate.length > 0){
                componentAssign(this.productTemplate,productTemplate);
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
                                content[i]['goods'][j]['thumbnail'] = _host + content[i]['goods'][j]['thumbnail'];
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
                                content[i]['goods'][j]['thumbnail'] = _host + content[i]['goods'][j]['thumbnail'];
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
                                if(content[i]['top_nav'][z]['goods'].length>0){
                                    for(var j = 0;j<content[i]['top_nav'][z]['goods'].length;j++){
                                        content[i]['top_nav'][z]['goods'][j]['thumbnail'] = _host + content[i]['top_nav'][z]['goods'][j]['thumbnail'];
                                        content[i]['top_nav'][z]['goods'][j]['price'] = '￥' + content[i]['top_nav'][z]['goods'][j]['price'];
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
                                if(content[i]['left_nav'][z]['goods'].length>0){
                                    for(var j = 0;j<content[i]['left_nav'][z]['goods'].length;j++){
                                        content[i]['left_nav'][z]['goods'][j]['thumbnail'] = _host + content[i]['left_nav'][z]['goods'][j]['thumbnail'];
                                        content[i]['left_nav'][z]['goods'][j]['price'] = '￥' + content[i]['left_nav'][z]['goods'][j]['price'];
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
                                obj[i].images[j]['FileInfo']['path'] = _host + obj[i].images[j]['FileInfo']['path'];
                            }
                        }
                        
                    }
                    if(content[i]['type'] == 'image_link'){
                        if(content[i]['images'].length > 0){
                            for(var j=0;j<content[i]['images'].length;j++){
                                content[i]['images'][j]['thumbnail'] = _host + content[i]['images'][j]['thumbnail'];
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