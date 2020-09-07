$(function(){
	$.ajax({
		type:"post",
		url:"/shop/activity/myGift/"+wid+"?page=1",
		async:true,
		headers: {
	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    },
		success:function(res){
			var length = res.data.data.length;
			console.log(res)
			for(var i = 0;i<length;i++){
				if (res.data.data[i].type == 3 || res.data.data[i].type == 4) {
					var img_url = res.data.data[i].img;
					var html ='<div class="giftbox"><div class="gift-img">';
					if (img_url) {
						html+='<img width="100" height="100" src="'+ imgUrl +img_url+'"/>';
					} else {
						html+='<img width="100" height="100" src="'+ _host +'shop/images/lottery-bg.png'+'"/>';
					}
					html+='</div><div class="gift-div">';
					html+='<p class="gift-p">'+res.data.data[i].title+'</p>';
					html+='<div class="gift-list">';
					if(res.data.data[i].type == 4){
						html+='<a data-id='+res.data.data[i].content+' data-type='+ res.data.data[i].type +' class="gift-look">立即购买</a>';
					}else{
						html+='<a data-id='+res.data.data[i].id+' data-type='+ res.data.data[i].type +' class="gift-look">查看兑奖方式</a>';
					}
					html+='<a data-id='+res.data.data[i].id+' class="gift-del">删除</a>';
					html+="</div></div></div>";
				} else if (res.data.data[i].type == 2){
					let item = res.data.data[i];
					if (item.coupon.range_type == 0 && item.coupon.limit_amount == 0) {
						res.data.data[i].coupon.limit = '无使用门槛';
					} else if (item.coupon.range_type == 1 && item.coupon.limit_amount == 0) {
						res.data.data[i].coupon.limit = '指定商品可用';
					} else if (item.coupon.range_type == 0 && (+item.coupon.limit_amount) > 0) {
						res.data.data[i].coupon.limit = '满' + item.coupon.limit_amount + '可用';
					} else if (item.coupon.range_type == 1 && (+item.coupon.limit_amount) > 0) {
						res.data.data[i].coupon.limit = '指定商品，满' + item.coupon.limit_amount + '可用';
					}
					var html = '<div class="giftbox J_go-lottery" data-id='+res.data.data[i].content+'><div class="div-box"><div class="lotter-box">';
					html+="<div class='lottery-title'>" + res.data.data[i].title + '</div>';
					html+="<div class='lottert-content'><div class='lottery-num'>¥<span>" + res.data.data[i].coupon.amount +"</span></div>";
					html+="<div class='limit-box'>";
					if (res.data.data[i].coupon.range_type == 1 && res.data.data[i].coupon.limit_amount > 0) {
						html+="<div class='limit-amount'>" + res.data.data[i].coupon.limit + "</div>";
					} else {
						html+="<div>" + res.data.data[i].coupon.limit + "</div>";
					}
					html+="</div></div>";
					html+="<div class='live-time'>使用期限：" + res.data.data[i].coupon.start_at + "~" + res.data.data[i].coupon.end_at + "</div>";
					html+=' </div></div></div>';
				} else if (res.data.data[i].type == 5 || res.data.data[i].type == 6){
					var html = '<div class="giftbox J_go-point"><div class="div-box"><div class="points-box"><div class="points-left">';
					html+='<span class="points-title">' + res.data.data[i].title + '</span>';
					html+='<span class="gmt-time">' + res.data.data[i].created_at +'</span></div>';
					html+='<div class="points-right">+' + res.data.data[i].content;
					html+='</div></div></div></div>';
				}
				
				$('.content').append(html);
			}
		},
		error:function(){
			alert('数据访问错误')
		}
	});
	var id ='';
	var gift = '';
	$('body').on('click','.gift-del',function(){
		gift = $(this).parents('.giftbox');
		id = $(this).data('id');
		$('.mod-con').show();
		$('.mod-quxiao').click(function(){
			$('.mod-con').hide();
		});
	});
	$('.mod-sure').click(function(){
		$.ajax({
			type:"post",
			url:"/shop/activity/delGift/"+wid+"/"+id,
			async:true,
			headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
			success:function(res){
				console.log(res);
				gift.remove();
				$('.mod-con').hide();
			},
			error:function(){
				alert('数据访问错误');
			}
		});	
	});
	$('body').on('click','.gift-look',function(){
		id = $(this).data('id');
		var type = $(this).data('type');
		if(type == 3){
			window.location.href='/shop/activity/method/'+id + '/1';//1大转盘，2砸金蛋
		}
		if(type == 4){
			window.location.href='/shop/product/detail/'+ wid + '/' + id;
		}
	})
	$('body').on('click','.J_go-lottery',function(){
		window.location.href='/shop/member/coupons/'+wid +'/1';
	});
	$('body').on('click','.J_go-point',function(){
		window.location.href="/shop/point/mypoint";
	})
	var stop = true; //触发开关，防止多次调用事件
	var page = 1;
	$(window).scroll(function(event) {
	    if ($(this).scrollTop() + $(window).height() + 100 >= $(document).height() && $(this).scrollTop() > 100) {
	        if (stop == true) {
	            stop = false;
	            page = page + 1; //当前要加载的页码
	            var parm = {
	                'page': page
	            };
	            $.ajax({
					type:"post",
					url:"/shop/activity/myGift/"+wid,
					headers: {
				        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				    },
				    data:parm,
					success:function(res){
						var length = res.data.data.length;		
						for(var i = 0;i<length;i++){
							if (res.data.data[i].type == 3 || res.data.data[i].type == 4) {
								var img_url = res.data.data[i].img;
								var html ='<div class="giftbox"><div class="gift-img">';
								if (img_url) {
									html+='<img width="100" height="100" src="'+ imgUrl +img_url+'"/>';
								} else {
									html+='<img width="100" height="100" src="'+ _host +'shop/images/lottery-bg.png'+'"/>';
								}
								html+='</div><div class="gift-div">';
								html+='<p class="gift-p">'+res.data.data[i].title+'</p>';
								html+='<div class="gift-list">';
								if(res.data.data[i].type == 4){
									html+='<a data-id='+res.data.data[i].content+' data-type='+ res.data.data[i].type +' class="gift-look">立即购买</a>';
								}else{
									html+='<a data-id='+res.data.data[i].id+' data-type='+ res.data.data[i].type +' class="gift-look">查看兑奖方式</a>';
								}
								html+='<a data-id='+res.data.data[i].id+' class="gift-del">删除</a>';
								html+="</div></div></div>";
							} else if (res.data.data[i].type == 2){
								let item = res.data.data[i];
								if (item.coupon.range_type == 0 && item.coupon.limit_amount == 0) {
									res.data.data[i].coupon.limit = '无使用门槛';
								} else if (item.coupon.range_type == 1 && item.coupon.limit_amount == 0) {
									res.data.data[i].coupon.limit = '指定商品可用';
								} else if (item.coupon.range_type == 0 && (+item.coupon.limit_amount) > 0) {
									res.data.data[i].coupon.limit = '满' + item.coupon.limit_amount + '可用';
								} else if (item.coupon.range_type == 1 && (+item.coupon.limit_amount) > 0) {
									res.data.data[i].coupon.limit = '指定商品，满' + item.coupon.limit_amount + '可用';
								}
								var html = '<div class="giftbox J_go-lottery" data-id='+res.data.data[i].content+'><div class="div-box"><div class="lotter-box">';
								html+="<div class='lottery-title'>" + res.data.data[i].title + '</div>';
								html+="<div class='lottert-content'><div class='lottery-num'>¥<span>" + res.data.data[i].coupon.amount +"</span></div>";
								html+="<div class='limit-box'>";
								if (res.data.data[i].coupon.range_type == 1 && res.data.data[i].coupon.limit_amount > 0) {
									html+="<div class='limit-amount'>" + res.data.data[i].coupon.limit + "</div>";
								} else {
									html+="<div>" + res.data.data[i].coupon.limit + "</div>";
								}
								html+="</div></div>";
								html+="<div class='live-time'>使用期限：" + res.data.data[i].coupon.start_at + "~" + res.data.data[i].coupon.end_at + "</div>";
								html+=' </div></div></div>';
							} else if (res.data.data[i].type == 5 || res.data.data[i].type == 6){
								var html = '<div class="giftbox J_go-point"><div class="div-box"><div class="points-box"><div class="points-left">';
								html+='<span class="points-title">' + res.data.data[i].title + '</span>';
								html+='<span class="gmt-time">' + res.data.data[i].created_at +'</span></div>';
								html+='<div class="points-right">+' + res.data.data[i].content;
								html+='</div></div></div></div>';
							}
							$('.content').append(html);
						}
						stop = true;
					},
					error:function(){
						alert('数据访问错误')
					}
				});
	        }
	    }
	})
})