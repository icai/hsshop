$(function () {
	//	首页轮播图
	var mySwiper = new Swiper('.new_swiper .swiper-container', {
		observer: true, //修改swiper自己或子元素时，自动初始化swiper
		observeParents: true, //修改swiper的父元素时，自动初始化swiper
		autoplay: 2000, //可选选项，自动滑动
		speed: 1000, //滑动速度
		loop: true, //环路		
		pagination: '.new_swiper .swiper-pagination', // 如果需要分页器
		paginationClickable: true
	});
	$('.new_swiper .swiper-container').mouseenter(function () {
		mySwiper.stopAutoplay(); //自动播放停止
	}).mouseleave(function () {
		mySwiper.startAutoplay(); //自动播放开始
	});

	$('.J_box-li').hover(function () {
		$(this).children('.J_hover-link').css('opacity', 1)
	}, function () {
		$(this).children('.J_hover-link').css('opacity', 0)
	});

	$('.example-img-box').on('mouseenter', 'li', function () {
		$(this).children('.J_example-link').addClass('link-show');
	}).on('mouseleave', 'li', function () {
		$(this).children('.J_example-link').removeClass('link-show');
	});

	$('.example-ul').on('mouseenter', 'li', function () {
		var index = $(this).index();
		$('.example-ul li').removeClass('active').eq(index).addClass('active');
		$('.example-img-box').hide().eq(index).show();
	});

	$('.service-stream-item').hover(function () {
		$(this).siblings().removeClass('service-stream-active')
		$(this).addClass('service-stream-active')
		$('.agreement-wrap .sign-box').addClass('hide-agreement')
		$('.agreement-wrap .sign-box').eq($(this).index() / 2).removeClass('hide-agreement')
		console.log($(this).index() / 2)
	});

	//	营销应用
	var mySwcustom = new Swiper('.z-custom .swiper-container', {
		observer: true, //修改swiper自己或子元素时，自动初始化swiper
		observeParents: true, //修改swiper的父元素时，自动初始化swiper
		loop: true,
		slidesPerView: 3, //同时显示的slides数量
		spaceBetween: 32, //slide之间的距离（单位px）
		loop: true,
		nextButton: '.z-custom .swiper-button-next', //下一页
		prevButton: '.z-custom .swiper-button-prev', //上一页
	});

	$('.z-custom').mouseenter(function () {
		mySwcustom.stopAutoplay(); //自动播放停止
	}).mouseleave(function () {
		mySwcustom.startAutoplay(); //自动播放开始
	});

	$('.J_slider-item-content').on('mouseenter', 'li', function () {
		$(this).parent().children('.active').removeClass('active');
		$(this).addClass('active');
	});
})