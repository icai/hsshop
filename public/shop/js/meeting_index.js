/* 
 * add by 韩瑜 
 * date 2018-10-26
 * 微页面图片懒加载
 */
Vue.use(VueLazyload,{
    preLoad: 1.3,
    error: _host + 'shop/images/lazyload1.gif',
    loading: _host + 'shop/images/lazyload1.gif',
    attempt: 1
})
//end
hstool.load();
var key = [];
var j = 0;
var k = 0;
var iarr = [];
var n = 0;
Vue.filter("substr", function(value) {
    if(value.length > 5){
        value = value.substr(0,5) + '...';
    }else{
        value = value;
    }
    return value;
});
new Vue({
  el: '#container',
  data:{
    header:[],
    lists:[],
    footer:{},
    showLink:1,
    bg_color:'',
    textList:[],
    qq:'',
    url:'',
    telphone:'',
    kefuShow:false,
    host:host,
    _host:_host,
    imgUrl:imgUrl,
    videoUrl:videoUrl,
    wid:wid,
    is_show:'',//红包弹窗，0: 弹窗展示, 1: 右下角图标展示, 2: 不展示
    activity_title:'',//红包名称
    bonusShow:false,//红包弹窗出现开关
    bonusShow_tip:false,//红包提示
    leftIndex:0,//商品分组左侧index
    leftNav:"",//左侧列表
  },
  methods: {
      showSub:function(menu,index){
        for(var i=0;i<this.footer.menu.length;i++){
            if(i != index){
                this.footer.menu[i].submenusShow = false;
            }
        }
        if(menu.submenus.length>0){
            // menu.submenusShow = menu.submenusShow ? false:true;
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
      //显示客服
      showKeFu:function(){
        this.kefuShow = true;
      },
      // 隐藏客服
      hideKeFu:function(){
        this.kefuShow = false;
      },
      //add by 韩瑜 2018-8-2 拆红包
      getBouns: function() {
        var that = this;
        // if(isBind){
        //     tool.bingMobile(function(){
        //         isBind = 0;
        //         that.getBouns2(that);
        //     })
        //     return;
        // }
        that.getBouns2(that);
      },
      getBouns2:function(that){
      	var that = this;
      	this.$http.post('/shop/activity/bonusUnpack/'+wid,{_token:$('meta[name="csrf-token"]').attr('content')}).then(
      		function(res){
      			if(res.body.status == 1){
      				window.location.href = host + 'shop/activity/bonusDetail/' + wid
      			}
      		}
      	)
      },
      //关闭红包弹窗
      closeBouns:function(){
      	var that = this;
      	that.bonusShow_tip = true
	  	that.bonusShow = false
      	this.$http.post('/shop/activity/bonusClose/'+wid,{_token:$('meta[name="csrf-token"]').attr('content')}).then(
	      	function(res){
	  			if(res.body.status == 1){
	  				that.bonusShow_tip = true
	  				that.bonusShow = false
	  			}
	  		}
	    )
      },
      //点击右下角红包图标
      showBonus:function(){
      	this.bonusShow_tip = false;
      	this.bonusShow = true;
      },
      //end
      /* add by 韩瑜
       * date 2018-9-18
       * 商品分组模板页点击商品分组左侧
       */
      chooseGroup:function(item,list,index){
      	var that = this
      	if(list.classifyList.length>0){
			for(var i = 0;i<list.classifyList.length;i++){
				list.classifyList[i]['isActive'] = false;
			}
        }
      	that.leftIndex = index
      	item.isActive = true
      },
  },
  beforeCreate: function () {
    var that = this;
    this.$http.get("/microPage/indexPage/"+ wid +'/'+ id).then(
        function (res) {
            hstool.closeLoad();
            // 处理成功的结果
            that.qq = res.body.data.qq;
            that.telphone = 'tel:' + res.body.data.telphone;
            that.url = 'http://wpa.qq.com/msgrd?v=3&uin='+ res.body.data.qq +'&site=qq&menu=yes';
            // 分享
            var url = location.href.split('#').toString();
            $('title').html(res.body.data.title);
            this.showLink = res.body.data.isWebsite;
            /**
             * updata 2019-1-16 邓钊
             * */
            this.bg_color = res.body.data.bgcolor;
            // if(this.showLink > 0){
            //     this.bg_color = 'transparent';
            // }else{
            //     this.bg_color = res.body.data.bgcolor;
            // }
            // console.log(this.bg_color);
            /**end**/
            if(this.showLink == 1){
                $('.showLink').removeClass('showLink');
            }
            if(res.body.data.container != ''){
                var content = JSON.parse(res.body.data.container);
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
            }
            if(res.body.data.header != ''){
                var header = JSON.parse(res.body.data.header);
                if(header.length>0){
                    for(var i = 0;i<header.length;i++){
                        if(header[i]['type'] == 'model'){
                            if(typeof header[i].template_data == 'string'){
                                var model_data = JSON.parse(header[i].template_data);
                            }else{
                                model_data = header[i].template_data;
                            }
                            var data = header.slice(0,i);
                                data = data.concat(model_data);
                            var new_header = header.splice(i+1,header.length);
                            header = data.concat(new_header);
                        }
                    }
                }
                content = header.concat(content);
            }
            for(var i in content){
                if(content[i] != undefined){
                    if(content[i]['type'] == 'bingbing'){
                        $('body').addClass('full-screen auto-footer-off');
                        this.lists = [];
                        if(content[i]['lists'].length > 0){
                            for(var j = 0;j<content[i]['lists'].length;j++){
                                content[i]['lists'][j]['icon'] = imgUrl + content[i]['lists'][j]['icon'];
                            }
                        }
                        this.lists.push(content[i]);
                        // alert($('.swiper-slide').width());
                        this.bingbing = true;
                        $('footer').remove();
                        break;
                    }
                    if(content[i]['type'] == 'imageTextModel'){
                        $('body').addClass('full-screen auto-footer-off');
                        this.lists = [];
                        if(content[i]['lists'][0] != undefined && content[i]['lists'][0]['lists'].length){
                            for(var n = 0;n<content[i]['lists'][0]['lists'].length;n++){
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
                        // alert($('.swiper-slide').width());
                        setTimeout(function(){
                            var swiper = new Swiper('.swiper-container', {
                                paginationClickable: true,
                                autoplay:2000,
                                loop:true
                            });
                        },1000)
                        // this.bingbing = true;
                        $('footer').remove();
                        break;
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
                        if(content[i]['logo'].indexOf(imgUrl)>=0){
                            content[i]['logo'] = content[i]['logo'];
                            content[i]['bg_image'] = content[i]['bg_image'];
                        }else{
                            
                            content[i]['logo'] = imgUrl + content[i]['logo'];
                            content[i]['bg_image'] = imgUrl + content[i]['bg_image'];
                        }
                        content[i]['order_link'] = '/shop/order/index/'+id;
                    }
                if(content[i]['type'] == 'goods'){
                    if(content[i]['cardStyle'] == '3' && content[i]['listStyle'] != 4){
                        content[i]['btnStyle'] = '0';
                    }
                    // 判断商品名显示
                    if(content[i]['goodName'] || content[i]['listStyle'] == 4){
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
                if(content[i]['type'] == 'spell_goods'){
                    if(content[i]['groups'].length){
                        for(var j = 0;j<content[i]['groups'].length;j++){
                            if(content[i]['groups'][j]['member'] == undefined){
                                content[i]['groups'][j]['member'] = [];
                            }
                        }
                    }
                }
                if(content[i]['type'] == 'goodslist'){
                    if(content[i]['cardStyle'] == '3' && content[i]['listStyle'] != 4){
                        content[i]['btnStyle'] = '0';
                    }
                    // 判断商品名显示
                    if(content[i]['goodName'] || content[i]['listStyle'] == 4){
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
        }
            // update 2018/7/12 华亢
            // 如果商品分组是平铺，则其他组件都不显示
            var arr = [];
            for(var i=0,l=that.lists.length;i<l;i++){
                if(that.lists[i]['type'] == 'good_group'){
                    if(that.lists[i]['group_pinpu'] == 1){
                        arr.push(that.lists[i]);
                        that.lists = arr;
                        break;
                    }
                }
            }
             //如果最后一个组件是商品分组，去掉底部
            if(this.lists[this.lists.length - 1]['type'] == 'good_group'){
               $('footer').remove();
               $('#container').css('paddingBottom','60px');
            }
            if(res.body.data.footer != '' && this.lists[0]['type'] != 'bingbing'){
                var footer = JSON.parse(res.body.data.footer);
                this.footer = footer;
                for(var i =0;i< this.footer.menu.length;i++){
                    if(APP_HOST  + this.footer.menu[i]['linkUrl'].substr(1,this.footer.menu[i]['linkUrl'].length) == location.href){
                        footer.menu[i].styleObject = { backgroundImage:'url('+ imgUrl + footer.menu[i]['iconActive'] + ')',backgroundSize: '64px 50px'};
                    }else{
                        footer.menu[i].styleObject = { backgroundImage:'url('+ imgUrl + footer.menu[i]['icon'] + ')',backgroundSize: '64px 50px'};
                    }
                    this.footer.menu[i].submenusShow = false;
                }
            }else{
                this.footer = [];
            }
            /*
             * add by 韩瑜
             * date 2018-9-19
             * 商品分组模板左侧导航
             */
            for(i=0;i<this.lists.length;i++){
            	if(this.lists[i].type == 'group_page'){
            		this.lists[i].classifyList[0].isActive = 'true'
            	}
            }
            //end
        },function (res) {
        // 处理失败的结果
        }
    );
    //  add by 韩瑜 2018-8-2 红包弹窗
    this.$http.get('/shop/activity/bonusShow/' + wid).then(
    	function (res) {
    		if(res.body.status == 1){
	    		that.activity_title = res.body.data.activity_title
	    		if(res.body.data.is_show == 0){
	    			that.bonusShow = true
	    		}else if(res.body.data.is_show == 1){
	    			that.bonusShow_tip = true;
	    			that.bonusShow = false;
	    		}else{
	    			that.bonusShow_tip = false;
	    			that.bonusShow = false;
	    		}
    		}
    	}
    );
  },
  created: function () {
    // `this` 指向 vm 实例
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