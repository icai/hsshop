var key = [];
var j = 0;
var k = 0;
var iarr = [];
var n = 0;
new Vue({
  	el: '#container',
  	data:{
    	lists:[],
    	footer:{},
    	goodData:null, 
        group_name: '',//分组名称 
        editor_intro: '',//分组简介
        page:2,
        btnFlag:true,
  	},
  	methods:{
  		//底部点击
        showSub:function(menu,index){
            for(var i=0;i<this.footer.menu.length;i++){
                if(i != index){
                    this.footer.menu[i].submenusShow = false;
                }
            }
            if(menu.submenus.length>0){
                if(menu.submenusShow){
                    menu.submenusShow = false;
                }else{
                    menu.submenusShow = true;
                }
            }
        },
        // 设置加入购物弹窗data
        setGoodData:function(data){
            this.goodData = data;
        },
        hideGoodModel:function(){
            this.goodData = null;
        },
        getTextList:function(kind,list){
            this.textList = [];
            if(list.lists.length){
                for(var n = 0;n<list.lists.length;n++){
                    list.lists[n]['isActive'] = false;
                }
            }
            if(kind['lists'].length){
                for(var n = 0;n<kind['lists'].length;n++){
                    this.textList.push(kind['lists'][n]);
                }
            }
            kind.isActive = true;
        },
        // 点击加载更多
        // /shop/group/productGroupDetail
        getGroup:function(list){
            _this = this
            console.log(list,'eeeee')
            if(list.goods.length > 0){
                 var goodlist =list.goods
                 var id = list.id
                 var page = this.page++
            }
            if(list.listStyle ==3){
                this.$http.get("/shop/group/productGroupDetail?group_id="+ id +'&page=' + page + '&isNew=2').then(
                    function(res){ 
                       var thgoods =res.body.data
                       if (thgoods.length >= 0 && thgoods.length<5) {
                          for(var  i =0;i < thgoods.length;i++){
                            list.thGoods.push(thgoods[i])
                          }
                          _this.btnFlag=false
                      }else if(thgoods.length ==5){
                        for(var  i =0;i < thgoods.length;i++){
                            list.thGoods.push(thgoods[i])
                          }
                                _this.btnFlag=true
                      }
                    }
                )
            }else{
                this.$http.get("/shop/group/productGroupDetail?group_id="+ id +'&page=' + page + '&isNew=1').then(
                    function(res){ 
                        console.log(res)
                      if(res.body.status == 1){
                           var list = res.body.data
                          if(list.length >= 0 && list.length < 15){
                              for(var  i = 0 ;i < list.length;i++){
                                  goodlist.push(list[i])
                              }
                              _this.btnFlag=false
                          }else if(list.length ==15){
                            for(var  i = 0 ;i < list.length;i++){
                                goodlist.push(list[i])
                            }
                              _this.btnFlag=true
                          }
                      }      
                    }
                )
            }
        },
  	},
	created: function () {
        var content = dataList.tpl.data;
        this.group_name = dataList.title;
        this.editor_intro = dataList.introduce;
	    for(var i in content){
            if(content[i] != undefined){
                if(content[i]['type'] == 'imageTextModel'){
                    $('body').addClass('full-screen auto-footer-off');
                    this.lists = [];
                    if(content[i]['lists'][0] != undefined && content[i]['lists'][0]['lists'].length){
                        for(var n = 0;n<content[i]['lists'][0]['lists'].length - 1;n++){
                            if(n == 0){
                                content[i]['lists'][n]['isActive'] = true;
                            }else{
                                if(n < content[i]['lists'].length){
                                    content[i]['lists'][n]['isActive'] = false;
                                }
                            }
                            this.textList.push(content[i]['lists'][0]['lists'][n]);
                        }
                    }
                    this.lists.push(content[i]);
                    setTimeout(function(){
                        var swiper = new Swiper('.swiper-container', {
                            paginationClickable: true,
                            autoplay:2000,
                            loop:true
                        });
                    },1000)
                    $('footer').remove();
                    break;
                }
                if(content[i]['type'] == 'header'){
                    if(content[i]['logo'].indexOf(imgUrl)>=0){
                        content[i]['bg_image'] = content[i]['bg_image'];
                        content[i]['logo'] = content[i]['logo'];
                    }else{
                        content[i]['logo'] = imgUrl + content[i]['logo'];
                        content[i]['bg_image'] = imgUrl + content[i]['bg_image'];
                    }
                    content[i]['order_link'] = '/shop/order/index/'+id;
                }
                if(content[i]['type'] == 'goods'||content[i]['type'] == 'goods_group'){
                    if(content[i]['cardStyle'] == '3' && content[i]['listStyle'] != 4){
                        content[i]['btnStyle'] = '0';
                    }
                    // 判断商品名显示
                    if(content[i]['goodName']){
                        content[i].title = 'info-title';
                    }else if(content[i].listStyle == 4){
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
                            if(content[i]['top_nav'][z]['goods'].length>0){
                                for(var j = 0;j<content[i]['top_nav'][z]['goods'].length;j++){
                                    content[i]['top_nav'][z]['goods'][j]['thumbnail'] = imgUrl + content[i]['top_nav'][z]['goods'][j]['thumbnail'];
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
                                    content[i]['left_nav'][z]['goods'][j]['thumbnail'] = imgUrl + content[i]['left_nav'][z]['goods'][j]['thumbnail'];
                                    content[i]['left_nav'][z]['goods'][j]['price'] = '￥' + content[i]['left_nav'][z]['goods'][j]['price'];
                                }
                            }
                        }
                        
                    }
                }
                this.lists.push(content[i]);
                //秒杀活动
                if(content[i]['type'] == 'marketing_active'){
                    if(content[i]['content'].length==0){
                        continue;
                    }
                    var sku = content[i]['content'][0]['sku'];
                    var seckill_price = sku[0]['seckill_price'];//秒杀价格
                    var seckill_stock = 0;//秒杀库存
                    for(var j = 0;j < sku.length;j ++){
                        seckill_price =  parseFloat(seckill_price) <= parseFloat(sku[j]['seckill_price']) ? seckill_price : sku[j]['seckill_price'];//秒杀价格取最小
                        seckill_stock += parseInt(sku[j]['seckill_stock']);
                    }
                    content[i].min_seckill_price = seckill_price;
                    content[i].total_seckill_stock = seckill_stock;
                    
                }
                if(content[i]['type'] == 'image_ad'){
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
                if(content[i]['type'] == 'image_link'){
                    if(content[i]['images'].length > 0){
                        for(var j=0;j<content[i]['images'].length;j++){
                            content[i]['images'][j]['thumbnail'] = imgUrl + content[i]['images'][j]['thumbnail'];
                        }
                    }
                }
            }
            if (content[0].goods.length >=0 && content[0].goods.length < 15) {
                this.btnFlag = false;
            }else{
                this.btnFlag = true;
            }
        }
        if(navData != '' && this.lists[0]['type'] != 'bingbing'){
            var footer = JSON.parse(navData);
            this.footer = footer;
            for(var i =0;i< this.footer.menu.length;i++){
                if(APP_HOST  + this.footer.menu[i]['linkUrl'].substr(1,this.footer.menu[i]['linkUrl'].length) == location.href){
                    footer.menu[i].styleObject = { backgroundImage:'url('+ imgUrl + footer.menu[i]['iconActive'] + ')',backgroundSize: '64px 50px'};
                } else {
                    footer.menu[i].styleObject = { backgroundImage:'url('+ imgUrl + footer.menu[i]['icon'] + ')',backgroundSize: '64px 50px'};
                }
            }
        } else {
            this.footer = [];
        }
	}
})
//生成随机字符串
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
// 点击图片查看大图 add by 黄新琴 2018/9/3
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