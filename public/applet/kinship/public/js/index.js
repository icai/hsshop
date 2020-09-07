$(function() {
	var browser = {
		versions: function() {
			var u = navigator.userAgent,
				app = navigator.appVersion;
			return {
				ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/),  //ios终端 
			};
		}(),
		language: (navigator.browserLanguage || navigator.language).toLowerCase()
	}
	//判断是ios系统时触发
	if (browser.versions.ios) {
		// 通过config接口注入权限验证配置后, 在 ready 中 play 一下 audio
		function autoPlayAudio1() {
			wx.config({
				// 配置信息, 即使不正确也能使用 wx.ready
				debug: false,
				appId: '',
				timestamp: 1,
				nonceStr: '',
				signature: '',
				jsApiList: []
			});
			wx.ready(function() {
				document.getElementById('bgmusic').play();
			});
		}
		autoPlayAudio1(); 
	}

	//音乐播放
	playMusic();

	function playMusic() {
		$(".music").on("click", function() {
			var audioP = $("#bgmusic")[0];
			if(audioP.paused) {
				audioP.play();
			} else {
				audioP.pause();
			}
			$(".music").toggleClass("music-animate")
		})
	};

	//最后分享按钮
	$(".btn").click(function() {
		$(".board").fadeIn()
	})
	$(".board").click(function() {
		$(this).fadeOut()
	})
	//swiper注册
	var swiper = new Swiper('.swiper-container', {
		direction: 'horizontal',
		onlyExternal: true,

		onInit: function(swiper) { //Swiper2.x的初始化是onFirstInit
			swiperAnimateCache(swiper); //隐藏动画元素 
			swiperAnimate(swiper); //初始化完成开始动画
		},
		onSlideChangeEnd: function(swiper) {
			swiperAnimate(swiper); //每个slide切换结束时也运行当前slide动画
		}
	});
	//点击按钮向下翻页
	$('.img_2').click(function() {
		var topics = $(this).siblings(".topic")["0"];
		if(topics) { //有答题的情况（必须选择之后才能到下一页）
			$.each(topics.children, function(index, ele) {
				var checked = $(this).find("input").prop("checked");
				console.log(checked, index, topics.children.length)
				if(checked) {
					$(".hint").fadeOut()
					swiper.slideNext();
					return false;
				} else {
					if((index + 1) == topics.children.length) {
						setTimeout(function() {
							$(".hint").fadeIn()
						}, 50)
						setTimeout(function() {
							$(".hint").fadeOut()
						}, 2000)
					}
				}
			})
		} else { //第一页的情况
			swiper.slideNext();
		}
	})
})