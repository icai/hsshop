$(function(){
	 //底部轮播图
		var imgArr = [];
		var swiper = new Swiper('.case_swiper .swiper-container', {
			autoplay: 3000,//可选选项，自动滑动
			loop : true,
			slidesPerView: 3,      //同时显示的slides数量
			spaceBetween: 15,      //slide之间的距离（单位px）
			
	//      loop: true,
			prevButton:'.case_swiper .swiper-button-prev',     //上一页
			nextButton:'.case_swiper .swiper-button-next',     //下一页
		});
		   $('.case_swiper .swiper-container').mouseenter(function(){
			swiper.stopAutoplay();              //自动播放停止
		}).mouseleave(function(){
			swiper.startAutoplay();             //自动播放开始
		});
	// 禁用列表 
	$('body').on('click','.comm_sub',function(e){
        var _this = $(this);
        var caseId = _this.data('id');
		var content  = $('#content').val();
		var nickname = $('#nickname').val();
		var captcha = $('#captcha').val();
		var length = $('#content').val().length;
		if(length>=200){
			tipshow('评论字数不能超过200字符','warn');
			return false;
		};
		if(length<=0){
			tipshow('评论字数不能为空','warn');
			return false;
		}
		$.ajax({
			type:"post",
			url:'/home/index/caseDetails?id='+caseId,
			data:{
				caseId:caseId,
				content:content,
				nickname:nickname,
				captcha:captcha
			},
			headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        },
			success: function(res){
				console.log(res);
				if(res.status == 1){
					captcha_img.click();
					tipshow('评论成功','info');
					$('.commNum').text(parseInt($('.commNum').text())+1);
					$('input').val('');
					$('textarea').val('');
	        		var html = '';
	        		html += '<div class="case-sec comm_mail"> ';	   
	        		html += '<p class="disinl">'+nickname+'</p>';
	        		html += '<span class="csae-cp">'+res.data.created+'</span>';
	        		html += '<p>'+content+'</p>';
	        		html += '</div>';
					$('.js_comment').prepend(html);
					location.reload();
				}else{
					tipshow(res.info,'warn');
				}
			},
			error:function(msg){
				alert('数据访问异常')
			}
		});	         
	});
	$(".wx").hover(function(){
		$(".wx_code").show()
	},function(){
		$(".wx_code").hide()
	})
    
})

