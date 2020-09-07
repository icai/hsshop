$(function(){
	
	$('#myTab a').click(function (e) {
	    e.preventDefault();
	    $(this).tab('show');
	});
	
	var mySwiper = new Swiper ('.cb', {
	    direction: 'horizontal',
	    loop: true,
	    autoplay : 1000,
	    speed:500,
	    slidesPerView : 4,
	    spaceBetween: 0,
	    
	    // 如果需要前进后退按钮
	    nextButton: '.cn',
	    prevButton: '.cp',
	
	})
	
	//底部轮播图
	$(".bu-nex").hide()
	var mySwiper = new Swiper('.cona', {
//		width : 800,     //你的slide宽度
//		height: 300,    //你的slide高度
		autoplay: 1000, //可选选项，自动滑动
		speed:1000,      //滑动速度
		loop : true,    //环路
		pagination : '.top_swiper .swiper-pagination',    //分页器
		paginationClickable :true,            			  //分页器可点击
		prevButton:'.top_swiper .swiper-button-prev',     //上一页
		nextButton:'.top_swiper .swiper-button-next',     //下一页
	});
	$('.top_swiper .swiper-container').mouseenter(function(){
		mySwiper.stopAutoplay();              //自动播放停止
		$(".top_swiper .swiper-button-prev, .top_swiper .swiper-button-next").show()
	}).mouseleave(function(){
		mySwiper.startAutoplay();             //自动播放开始
		$(".top_swiper .swiper-button-prev, .top_swiper .swiper-button-next").hide()
	});
	
	$(".b").hover(function(){
		$(this).children('.slide-p1').hide();
		$(this).children('.slide-p2').show();
	},function(){
		$(this).children('.slide-p1').show();
		$(this).children('.slide-p2').hide();
	});
	
	//预约提交
	$(".tri-sua").click(function(){
		$.ajax({
            url:'/home/index/reserve',// 跳转到 action
            data:$('#myform').serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (res) {
            	if(res.status==0){
            		tipshow(res.info,"warn");
            	}else{
            		tipshow("预约成功");
            	}
            },
            error : function() {
                alert("数据访问错误");
            }
        });		
	});   
});
