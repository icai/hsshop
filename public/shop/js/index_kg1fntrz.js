$(function(){
	var wid = $('.wid').val();
	var valid = 0;
	getAjax('/shop/member/couponList/'+wid+'/valid',0);//获取默认数据
	loading('/shop/member/couponList/'+wid+'/valid',0);//默认加载
    var index = 0;
	$('.tab').click(function(){
		var _this = $(this);
        index = $(this).index();
		$('.tab').removeClass('active');
		$(this).addClass('active');
		$('.promote-card-list').eq(index).empty();
        $('.promote-card-list').hide();
        $('.promote-card-list').eq(index).show();
		getAjax($(this).data('href'),index);
		loading($(this).data('href'),index);
		// valid = $(this).data('id');

	});
	//上拉加载
	function loading(url,index){
		var page = 1;
		window.onscroll=function(){
			if($(window).scrollTop()+$(window).height()>=$(document).height()){
				page ++;
				getAjax(url+'?page='+page,index);
			}
		}
	}
	function getAjax(url,index){
		$.get(url,function(data){
			$(".pageLoading").hide();//页面加载完成loading隐藏
			if(data.status == 1){
				var list  = data.data.data;
				next_url = data.next_page_url;
				for(var i = 0;i < list.length; i++){
					var endTime = Date.parse(list[i].end_at)/1000;//结束时间时间戳
					var newTime = Date.parse(new Date())/1000;//当前时间时间戳
					var html='';
					var inner= '';
                    var invalidText = '已过期';
					list[i].start_at=list[i].start_at.substr(0,list[i].start_at.indexOf(' '));
					list[i].end_at=list[i].end_at.substr(0,list[i].end_at.indexOf(' '));
					//range_type == 0 无指定商品  limit_amount==0无门槛限制 only_original_price ==0 无指定原价
					if (list[i].limit_amount == 0 && list[i].only_original_price == 0 && list[i].range_type == 0) {
						//无任何条件无门槛
						inner = "<p>无使用门槛</p>";
					} else if (list[i].limit_amount != 0 && list[i].only_original_price != 0 && list[i].range_type != 0) {
						//设置满减，指定商品，原价
						inner = "<p>满" + list[i].limit_amount + "元可使用</p>";
						inner += "<p>仅原价购买指定商品时可用</p>";
					} else if (list[i].limit_amount != 0 && list[i].only_original_price == 0 && list[i].range_type != 0) {
						//设置满减，指定商品
						inner = "<p>满" + list[i].limit_amount + "元可使用</p>";
						inner += "<p>仅指定商品可用</p>";
					} else if (list[i].limit_amount != 0 && list[i].only_original_price != 0 && list[i].range_type == 0) {
						//设置满减，原价
						inner = "<p>满" + list[i].limit_amount + "元可使用</p>";
						inner += "<p>仅原价购买商品时可用</p>";
					} else if (list[i].limit_amount != 0 && list[i].only_original_price == 0 && list[i].range_type == 0) {
						//设置满减
						inner = "<p>满" + list[i].limit_amount + "元可使用</p>";
					} else if (list[i].limit_amount == 0 && list[i].only_original_price != 0 && list[i].range_type != 0) {
						//设置指定，原价
						inner = "<p>仅原价购买指定商品时可用</p>";
					} else if (list[i].limit_amount == 0 && list[i].only_original_price == 0 && list[i].range_type != 0) {
						//设置指定
						inner = "<p>仅指定商品可用</p>";
					} else if (list[i].limit_amount == 0 && list[i].only_original_price != 0 && list[i].range_type == 0) {
						//设置原价
						inner = "<p>仅原价购买商品时可用</p>";
					}
					if(index==0){
                        

                        //可使用优惠券
                        html = '<li class="promote-item promote-item--active">' + '<a class="clearfix" href="/shop/member/couponDetail/' + wid + "/" + list[i].id + '">' + '<div class="promote-left-part">' + '<div class="promote-shop-name">' + list[i].title + '<div class="use">立即使用</div>' + "</div>" + '<div class="inner">' + '<div class="promote-card-value">' + "<span>￥</span>" + "<i>" + list[i].amount + "</i>" + "</div>" + '<div class="promote-box"><div class="promote-inner">'+ inner +'</div><div class="promote-condition font-size-12">' + list[i].start_at + " ~ " + list[i].end_at + "</div></div>" + "</div>" + "</div>" + '<div class="promote-right-part center">' + '<div class="promote-use-state"></div>' + '<div class="inner">' ;
                        "</div>" + "</div>" + '<i class="expired-icon"></i>' + '<i class="left-dot-line"></i>' + "</a>" + "</li>";
                        $(".promote-card-list").eq(0).append(html);
                      }else{
						// 过期优惠券
                        if (list[i].status > 0) {
                            invalidText = '已使用';
                        }
				        html = '<li class="promote-item ">'+
				            '<a class="clearfix" href="/shop/member/couponDetail/'+wid+'/'+list[i].id+'">'+
								'<div class="promote-left-part">'+
									'<div class="promote-shop-name">'+ list[i].title + '<div class="use">查看</div>' +'</div>'+
				                    '<div class="inner">'+
				                        '<div class="promote-card-value">'+
				                            '<span>￥</span>'+
				                            '<i>'+list[i].amount+'</i>'+
				                        '</div>'+
				                        '<div class="promote-box"><div class="promote-inner">'+ inner +'</div><div class="promote-condition font-size-12">'+list[i].start_at+' ~ '+list[i].end_at+'</div></div></div>'+
				                '</div>'+
				                '<div class="promote-right-part center">'+
				                    // '<div class="promote-use-state">'+invalidText+'</div>'+
				                    // '<div class="inner">'+inner
				                    // '</div>'+
								'</div>'+
								'<span class="invalid-icon"></span>'+
				                '<i class="expired-icon"></i>'+
								'<i class="left-dot-line"></i>'+
								
				            '</a>'+
				        '</li>';
				        
						$('.promote-card-list').eq(1).append(html);
					}
				}
			}else{
				tool.tip(data.info)
			}
			if($('.promote-card-list').eq(0).find('li').length > 0){
				$('.empty-coupon-list').hide();
			}else if(index == 1){
                $('.empty-coupon-list').hide();
            }else if($('.promote-card-list').eq(0).find('li').length == 0){
                $('.empty-coupon-list').show();
            }
		});
	}
})