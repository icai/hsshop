$(function(){
    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
        autoplay:5000,
        speed:1000,
        observer:true,//修改swiper自己或子元素时，自动初始化swiper 解决个别不loop
        loop:true
    });
    /**
     * update by 韩瑜
     * date 2018-10-31
     * 分销商品规格弹窗
     */
    var isDistribute = 0 //是否是分销商品
    var noSkuDisPrice = 0 //没有规格的分销商品佣金
    if(rate != 0){
        isDistribute = 1
        noSkuDisPrice = (product.price * rate / 100).toFixed(2)
    }    
    var ways = 0;//1 为加入购物车   0 为立即购买
    $('.js-add-cart').click(function(){
        $('video').css('display','none');
        var that = this;
        // if(isBind){
        //     tool.bingMobile(function(){
        //         isBind = 0;
        //         addCart();
        //     })
        //     return;
        // }
        addCart();
       
    })
    function addCart(){
        ways = 1;
        $('#addCart').removeClass('hide');
        tool.spec.open({
            "type":2,               //1为 多按钮  2 为单按钮
            "callback":callback,     //点击规格按钮返回数据
            "url": "/shop/product/getSku",  //获取规格接口 
            "data": {  // 获取规格参数
                "_token": $("meta[name='csrf-token']").attr("content"),
                "pid": product.id
            },
            "initSpec": {    // 默认商品数据
                "title": product.title,
                "img": product.img,
                "stock": product.stock,
                "price": product.showPrice,
                "wholesale_flag":product.wholesale_flag,
                "wholesale_array":product.wholesale_array
            },
            "unActive":1,   //非拼团活动
            "isEdit":true,  //点x  不保存数据
            "buyCar": true,  //按钮  为单按钮  加入购物车  可不写
            "limit_num": product.quota,
            "buy_min": product.buy_min,
            "isDistribute":isDistribute,//是否是分销商品 0为否 1为是
            "rate":rate, //一级佣金比例
            "rateSec":rateSec, //二级佣金比例
            "noSkuDisPrice":noSkuDisPrice//没有规格的分销商品佣金            
        });
    }
    $('.js-cancel').click(function(){
        $('#Xms3Sq4JR6').hide();
        $('#LBqDHKuruf').hide();
    })
    $('.js-buy-it').click(function(){
        $('video').css('display','none');
        var that = this;
        // if(isBind){
        //     tool.bingMobile(function(){
        //         isBind = 0;
        //         buyIt();
        //     })
        //     return;
        // }
        buyIt();
    });
    //购买
    /**
     * @auther 邓钊
     * @desc 选择规格
     * @data 2018-10-22
     * @param
     * @return
     *
     * */
    $('.selectSku').on('click',function () {
        buyIt()
    })
    function buyIt(){
        ways = 0;
        tool.spec.open({
            "type":2,               //1为 多按钮  2 为单按钮
            "callback":callback,     //点击规格按钮返回数据
            "url": "/shop/product/getSku",  //获取规格接口
            "data": {  // 获取规格参数
                "_token": $("meta[name='csrf-token']").attr("content"),
                "pid": product.id
            },
            "initSpec": {    // 默认商品数据
                "title": product.title,
                "img": product.img,
                "stock": product.stock,
                "price": product.showPrice,
                "wholesale_flag":product.wholesale_flag,
                "wholesale_array":product.wholesale_array
            },
            "unActive":1,   //非拼团活动
            "isEdit":true,  //点x  不保存数据
            "buyCar": false,  //按钮  为单按钮  加入购物车  可不写
            "limit_num": product.quota,
            "buy_min": product.buy_min,
            "isDistribute":isDistribute,//是否是分销商品 0为否 1为是
            "rate":rate, //一级佣金比例
            "rateSec":rateSec, //二级佣金比例
            "noSkuDisPrice":noSkuDisPrice//没有规格的分销商品佣金
        });
    }
    // 购物车点击
    $('#global-cart').click(function(){
    //规格选择后回调
        var that = $(this);
        // if(isBind){
        //     tool.bingMobile(function(){
        //         isBind = 0;
        //         window.location.href="/shop/cart/index/"+ that.data('id');
        //     })
        //     return;
        // }
        window.location.href="/shop/cart/index/" + $(this).data('id');
    })
    //规格选择后回调
    function callback(data){
        if(!data.data.spec_id && product.sku_flag!=0){
            tool.tip('请先选择规格');
            return false;
        } 
        var data = {
            "id": product.id,
            "num": data.data.num,
            "propid": data.data.spec_id,
            "content": ""//留言  to do something
        }
        if(ways){
            $.ajax({
                url:'/shop/cart/add/'+$('#wid').val(),// 跳转到 action
                data:data,
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (response) {
                    if (response.status == 1){
                        $('#Xms3Sq4JR6').hide();
                        $('#LBqDHKuruf').hide();
                        $('.goods-num').show().html(response.data.cartNum);  
                        tool.spec.close();
                        tool.tip('已加入购物车,赶紧去结算吧!');
                    }else{
                        tool.tip(response.info);
                    }
                },
                error : function() {
                    // view("异常！");
                    tool.tip("异常！");
                }
            });
        }else{
            $.ajax({
                url:'/shop/cart/add/'+$('#wid').val()+'?tag=1',// 跳转到 action
                data:data,
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (response) {
                    if (response.status == 1){
                        tool.spec.close();
                        window.location.href='/shop/order/waitPayOrder?cart_id=['+response.data.id+']';
                    }else{
                        tool.tip(response.info);
                    }
                },
                error : function() {
                    // view("异常！");
                    tool.tip("异常！");
                }
            });
        }
    }
    // 规格选择
    $('body').on('click','#one li',function () {
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
        $("#propid").val('');
        var val = $(this).html();
        var data = new Array();
        data = props[val]
        var secHtml = '';
        if (data){
            for (i=0;i<data.length;i++){
                secHtml = secHtml+'<li class="tag sku-tag pull-left ellipsis">'+data[i]['prop_value2']+'</li>'
            }
        }
        $('#sec').html(secHtml);
    });
    $('body').on('click','#sec li',function () {
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
        var sec = $(this).html();
        var one = $("#one .active").html()
        var data = new Array();
        var sku = new Array();
        data = props[one];
        for (i=0;i<data.length;i++){
            if (data[i]['prop_value2'] == sec){
                sku = data[i];
                break;
            }
        }
        $('.stock').html('剩余'+sku['stock']+'件');
        $("#propid").val(sku['id']);
        $("#propPrice").html(sku['price']);
        $("#img").attr('src',source+sku['img']);

    });
    $('.discount-item').each(function(){
        $(this).html($(this).html().replace(/\d+/g, '<span style="color:#FF9A40">$&</span>'));
    })
    $('.J_discount').click(function(){
        $('.discount-popup').show();
    });
    $('.discount-popup,.discount-close').click(function(){
        $('.discount-popup').hide();
    })
    $('.discount-wraper').click(function(e){
        e.stopPropagation();
    });

    // 数量增加
    $('.response-area-plus').click(function(){
    	var This = $(this);
    	$(".plus").attr("disabled",false)
	//限购数量  forbidden_buy值
        if($('#buy_num').val()<3){
        	This.siblings('input').val(parseInt($(this).siblings('input').val()) + 1);
        	$('#num').val($(this).siblings('input').val()); 
        	$(".plus").attr("disabled",true)
        	return;
        }else{
        	tool.tip("每人限购"+3);
        }
    })
    // 数量减少
    $('.response-area-minus').click(function(){
        if($(this).siblings('input').val()<=1){
            return;
        }
        $(this).siblings('input').val(parseInt($(this).siblings('input').val()) - 1);
        $('#num').val($(this).siblings('input').val());
    })
    // tab切换
    $('.js-tabber button').click(function(){
        $('.js-tabber button').removeClass('active');
        $(this).addClass('active');
        $('.js-tabber-content>div').removeClass('hide');
        $('.js-tabber-content>div').eq($(this).index()).addClass('hide');
    })
    // 评价区域点击事件
    $('.js-review-tabber .item').click(function(){
        $('.js-review-tabber .item').children('button').removeClass('active');
        $(this).children('button').addClass('active');
        $('.js-review-tabber-content').children('.review-detail-container').addClass('hide');
        $('.js-review-tabber-content').children('.review-detail-container').eq($(this).index()).removeClass('hide');
    })


    var page=2
    $('body').on('click','.more',function () {
        var obj = $(this);
        var wid = $('#wid').val();
        var str='page='+page+'&pid='+$('#pid').val();
        if (obj.data(status) != 0){
            str = str+'&status='+obj.data('status');
        }
        $.ajax({
            url:'/shop/product/evaluate/'+wid,// 跳转到 action
            data:str,
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                page = page+1;
                if (response.status == 1){
                    if (response.data==''){
                        obj.removeClass('more');
                        obj.html('没有更多');
                        return;
                    }
                    $.each(eval(response.data),function (data,val) {
                        if (val.is_hiden == 0){
                            var _html = '<a href="/shop/product/evaluateDetail/'+val.wid+'/?eid='+val.id+'" class="js-review-item review-item block-item"> ' +
                                '<div class="name-card"> ' +
                                '<div class="thumb">' +
                                ' <img src="'+val.member.headimgurl+'" alt="">' +
                                '</div> ' +
                                '<div class="detail"> <h3> '+val.member.nickname+'</h3> ' +
                                '<p class="font-size-12"> '+val.created_at+'</p>' +
                                '</div>' +
                                ' </div> ' +
                                '<div class="item-detail font-size-14 c-gray-darker"> ' +
                                '<p>'+val.content+'</p> ' +
                                '<div class="business-reply">'+
                                '<span>【商家回复】' + val.seller_reply + '</span>'+
                                '</div>'+
                                '</div> <div class="other"> ' +
                                '<span class="from">购买自：本店</span>' +
                                ' <p class="pull-right"> ' +
                                '<span class="js-like like-item "> ' +
                                '<i class="like"></i>' +
                                ' <i class="js-like-num">'+val.agree_num+'</i>' +
                                '</span> ' +
                                '<span class="js-add-comment"> ' +
                                '<i class="comment"></i> ' +
                                '<i class="js-comment-num"></i> ' +
                                '</span> </p>' +
                                ' </div>' +
                                ' </a>'
                        }else{
                            var _html = '<a href="/shop/product/evaluateDetail/'+val.wid+'/?eid='+val.id+'" class="js-review-item review-item block-item"> ' +
                                '<div class="name-card"> ' +
                                '<div class="thumb">' +
                                ' <span class="center font-size-18 c-orange">匿</span>' +
                                '</div> ' +
                                '<div class="detail"> <h3>匿名</h3>' +
                                '<p class="font-size-12"> '+val.created_at+'</p>' +
                                '</div>' +
                                ' </div> ' +
                                '<div class="item-detail font-size-14 c-gray-darker"> ' +
                                '<p>'+val.content+'</p> ' +
                                '<div class="business-reply">'+
                                '<span>【商家回复】' + val.seller_reply + '</span>'+
                                '</div>'+
                                '</div> <div class="other"> ' +
                                '<span class="from">购买自：本店</span>' +
                                ' <p class="pull-right"> ' +
                                '<span class="js-like like-item "> ' +
                                '<i class="like"></i>' +
                                ' <i class="js-like-num">'+val.agree_num+'</i>' +
                                '</span> ' +
                                '<span class="js-add-comment"> ' +
                                '<i class="comment"></i> ' +
                                '<i class="js-comment-num"></i> ' +
                                '</span> </p>' +
                                ' </div>' +
                                ' </a>'
                        }
                        obj.before(_html);
                    })

                }else{
                    tool.tip(response.info);
                }
            },
            error : function() {
                // view("异常！");
                tool.tip("异常！");
            }
        });
    })

    var wid = $('#wid').val();
    referCartNum()
    function referCartNum(){
         $.get("/shop/cart/getNumber/" + wid,function(res){
            
            if(res.data != 0){
                var num = res.data;
                $(".goods-num").html(num);
            }else{
                $(".goods-num").html("");
            }
            
            var value = sessionStorage.getItem("refer") || "";
            if(value != ""){
                sessionStorage.clear();
                window.location.reload();
            }
        })
    }
   
	//留言验证
	// 验证手机号
	function checkPhone() {
		var phone = $("[data-valid-type='tel']").val();
		if(!(/^1[34578]\d{9}$/.test(phone))) {
			tool.tip("手机号码不正确");
			return false;
		}
	}
	
	// 验证身份证 
	function isCardNo() {
		var pattern = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
		var card = $("[data-valid-type='id_no']").val();
		if(!(pattern.test(card))){
			tool.tip("身份证号码不正确");
			return false;
		}		
	}
	
	//验证邮箱
	function CheckMail() {
		var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		var email = $("[data-valid-type='email']").val();
		if(!(filter.test(email))){
			tool.tip("邮箱格式不正确");
			return false;
		}
	} 
	//隐藏客服
    $('.weui-actionsheet_cancel').click(function(){
        $('.weui-mask').addClass('hide');
        $('.weui-actionsheet').addClass('hide');
    })
    /*
    * @auther 邓钊
    * @desc 复制微信号
    * @date 2018-7-13
    * */
    $('body').on('click','#copy_btn',function(e){
        e.stopPropagation(); //阻止事件冒泡
        var id = $(this).attr('data-id')
        var aux = document.createElement("input");                  // 创建元素用于复制
        // 设置元素内容
        aux.setAttribute("value", id);
        // 将元素插入页面进行调用
        document.body.appendChild(aux);
        // 复制内容
        aux.select();
        // 将内容复制到剪贴板
        document.execCommand("copy");
        // 删除创建元素
        document.body.removeChild(aux);
        $('.tipshow').show()
        setTimeout(function () {
            $('.tipshow').hide()
        },1500)
    });
     /*
    * @auther 黄新琴
    * @desc 富文本图片点击放大
    * @date 2018-10-18
    * @update 魏冬冬（zbf5279@dingtalk.com）解决有时候苹果7,7p点击不了的问题 2019-05-28 15:45:50 
    * */
    $('.js-custom-richtext').click(function(){
        var imgs = [];
        var imgObj = $(this).find('img');
        for(var i=0; i<imgObj.length; i++){
            imgs.push(imgObj.eq(i).attr('src'));
            imgObj.eq(i).unbind('click');
            imgObj.eq(i).click(function(){
                var nowImgurl = $(this).attr('src');
                wx.previewImage({
                    "urls":imgs,
                    "current":nowImgurl
                });
             });
         }
    });
})

/* 
 * add by 韩瑜 
 * date 2018-10-26
 * 组件图片懒加载
 */
Vue.use(VueLazyload,{
    preLoad: 1.3,
    error: imgUrl + 'shop/images/lazyload1.gif',
    loading: imgUrl + 'shop/images/lazyload1.gif',
    attempt: 1
})
//end
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
        wid: $("#wid").val(),//店鋪id
        goodData: null,
        host: host,
        shopId:shop_id,//商品id
        sale_time_flag:0, //商品类型2为预售商品
        showPreSell:false, //是否显示预售倒计时
        preSell:{
            days:'00',
            hours:'00',
            minutes:'00',
            seconds:'00'
        }, //预售倒计时 add by 魏冬冬 2018-7-16
        isFavorite:false,//是否收藏
        iscollecttip:false,//收藏提示
        nocollecttip:false,//取消收藏提示
        hiddenScroll: false, //页面是否可以滚动 false不可 true可以
        shareDialogShow:false, //分享弹框是否弹出
        shareDialog_friend:false, //分享给好友弹框是否弹出
        shareDialog_card:false,  //生成卡片是否弹出
        cardurl:'',
        state_wait:false,
        state_end:false,
        rate:rate,
        rateSec:rateSec,
        maxPrice:0
    },
    methods:{
        getrtime:function(time) {
            var EndTime = new Date(time)
            var t = EndTime.getTime() - this.newTime
            if (t >= 0) {
              var d = evenNum(Math.floor(t / 1000 / 60 / 60 / 24))
              var h = evenNum(Math.floor((t / 1000 / 60 / 60) % 24))
              var m = evenNum(Math.floor((t / 1000 / 60) % 60))
              var s = evenNum(Math.floor((t / 1000) % 60))
              this.preSell.days = d
              this.preSell.hours = h
              this.preSell.minutes = m
              this.preSell.seconds = s
              var that = this;
              setTimeout(function() {
                that.getrtime(time)
                that.newTime += 1000
              }, 1000)
            }
        },
        /* add by 韩瑜
	     * date 2018-9-6
	     * 收藏点击事件
	     */
		collect:function(){
			this.isFavorite = true
			$('.iscollecttip').show()
			setTimeout(function () {
	            $('.collecttip').hide()
	        },1500)
			this.$http.post('/shop/member/favorite',{
				type: 0,
		    	relativeId: product.id,
		    	_token: $("meta[name='csrf-token']").attr("content"),
		    	title: product.title,
		    	price: product.price,
		    	image: product.img
			}).then(function(res){
				if(res.body.status != 1){
					this.isFavorite = false
				}
			})
		},
		//取消收藏
		collectcancel:function(){
			this.isFavorite = false
			$('.nocollecttip').show()
			setTimeout(function () {
	            $('.collecttip').hide()
	        },1500)
			this.$http.post('/shop/member/cancelFavorite',{
				type: 0,
		    	relativeId: product.id,
		    	_token: $("meta[name='csrf-token']").attr("content"),
			}).then(function(res){
				if(res.body.status != 1){
					this.isFavorite = true
				}
			})
        },
        // 商品详情页分享 by 崔源  2018.11.2
        dialogShow: function() {
            var that = this
            that.hiddenScroll = true;
            that.shareDialogShow = true;
          },
          shareCancle: function() {
            var that = this
            that.hiddenScroll = false;
            that.shareDialogShow = false;
          },

        // 分享给好友
        share_friend:function(){
            var that = this
            that.shareDialog_friend = true;
            // 获取分享信息
                that.$http.get(that.host+"shop/product/getShareData/"+product.id).then(function (res) {
                    if (res.body.status == 1) {
                        share_title = res.body.data.share_title;
                        share_img = res.body.data.share_img;
                        share_desc = res.body.data.share_desc;
                        //微信分享
                    }
                });

        },
        shareDialog_friend:function(){
            var that = this
            that.hiddenScroll = true;
            that.shareDialogShow = false;
            that.shareDialog_friend = true;
        },
        shareCancle_friend:function(){
            that = this
            that.shareDialog_friend = false;
        },
        //分享给好友确定按钮获取分享信息
        shre_sent_message:function(){
            that = this
            that.shareDialog_friend =false;
        },
        //生成卡片
        share_card:function(){
            var that = this
            that.shareDialog_card = true;
            that.state_wait = true;
            that.$http.get(that.host+"shop/product/getProductCard?id=" + product.id).then(function (res) {
                if (res.body.status == 1) {
                    that.state_wait = false;
                    that.state_end = true;
                    
                }
                that.cardurl = res.body.data
            });
        },
        shareDialog_friend:function(){
            var that = this
            that.hiddenScroll = true;
            that.shareDialogShow = false;
            that.shareDialog_card = true;
        },
        shareCancle_card:function(){
            that = this
            that.shareDialog_card = false;
            that.state_end = false;
        },


		//end
    },
    created: function () {
        var that = this;
        var price_region = product.showPrice.split("～");
        if(price_region.length == 1){
            that.maxPrice = price_region[0]
        }else{
            that.maxPrice = price_region[1]
        }
        this.$http.get("/shop/product/evaluate/"+wid+"?&pid="+$("#pid").val()).then(
            function (res) {
                var data = res.body;
                if(data.status == 1){
                    var len = data.data.length > 2 ? 2 : data.data.length;
                    for(var i = 0;i < len;i ++){
                        that.commentList.push(data.data[i])
                    }
                }
            },function (res) {
            // 处理失败的结果
            }
        );
        this.sale_time_flag = product.sale_time_flag;
        if(this.sale_time_flag == 2){
            this.newTime = product.now_timestamp * 1000;
            var endTime = product.sale_timestamp * 1000;
            if(endTime > this.newTime){
                this.showPreSell = true;
                this.getrtime(endTime);
            }
        }
        //商品详情数据 
        if(product.content){
            var productDetail = JSON.parse(product.content);
            if(productDetail.length > 0){
                componentAssign(this.lists,productDetail);
                this.$nextTick(function () {
                    setTimeout(function(){
                        $('.custom-richtext img').removeAttr('width');
                        $('.custom-richtext img').removeAttr('height');
                        $('.custom-richtext video').attr('width','100%');
                        $('.custom-richtext video').attr('height','auto');
                    },1000)
                })
            }
        }
        //公共广告数据  
        if(micro_page_notice.errCode == 0 && micro_page_notice.data.length>0){
            var ad = micro_page_notice.data.noticeTemplateData;
            if(ad){
                ad = JSON.parse(ad);
                componentAssign(this.productAd,ad);
                this.productAdPosition = micro_page_notice.data.position;
                var swiper = new Swiper('.swiper-container', {
                    pagination: '.swiper-pagination',
                    paginationClickable: true,
                    autoplay:5000,
                    speed:1000,
                    loop:true
                });
                this.$nextTick(function () {
                    setTimeout(function(){
                        $('.custom-richtext img').removeAttr('width');
                        $('.custom-richtext img').removeAttr('height');
                    },1000)
                })
            }
        }
        
        //商品模板数据
        if(productModel.product_template_info){
            var productTemplate = JSON.parse(productModel.product_template_info);
            if(productTemplate.length > 0){
                componentAssign(this.productTemplate,productTemplate);
                this.$nextTick(function () {
                    setTimeout(function(){
                        $('.custom-richtext img').removeAttr('width');
                        $('.custom-richtext img').removeAttr('height');
                    },1000)
                })
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
        //add by 韩瑜 2018-9-6 是否收藏
        this.$http.get('/shop/member/isFavorite?type=0&relativeId='+product.id).then(function(res){
			this.isFavorite = res.body.data.isFavorite
		})
    },
})
 //单转双
function evenNum(num) {
    num = num < 10 ? '0' + num : num
    return num
}
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
// alert(randomString(12))