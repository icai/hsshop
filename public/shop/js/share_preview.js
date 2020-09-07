$(function(){
    var mySwiper = new Swiper('.swiper-container', {
        autoplay: 3000, //可选选项，自动滑动
        pagination: '.swiper-pagination',
        loop: true,
    });
})
var app = new Vue({
    el: '#app',
    data: {
        pageData:null,
        activityImg:[],
        product:null,
        imgUrl:imgUrl,
        shopId:wid,
        lists:[],
        recommend:[]
    },
    created: function() {
        this.$http.get('/shop/share/showDetail?activityId=' + tool.getParams('activityId')).then(function(data){
            this.pageData = data.data.data;
            this.activityImg = this.pageData.product.activityImg.split(',');
            this.product = this.pageData.product;
            var productDetail = JSON.parse(this.pageData.product.content);
                if(productDetail.length > 0) {
                    componentAssign(this.lists, productDetail);
                }
            componentAssign(this.lists, productDetail);
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
        });
        this.$http.get('/shop/share/more?activityId=' + tool.getParams('activityId')).then(function(data){
            this.recommend = data.data.data;
        })
    },
    mounted(){

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