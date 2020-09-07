$(function(){
  $.fn.numberAnimate = function(setting) {
    var defaults = {
      speed : 1000,//动画速度
      num : "", //初始化值
      iniAnimate : true, //是否要初始化动画效果
      symbol : '',//默认的分割符号，千，万，千万
      dot : 0 //保留几位小数点
    }
    //如果setting为空，就取default的值
    var setting = $.extend(defaults, setting);
  
    //如果对象有多个，提示出错
    if($(this).length > 1){
      alert("just only one obj!");
      return;
    }
  
    //如果未设置初始化值。提示出错
    if(setting.num == ""){
      alert("must set a num!");
      return;
    }
    var nHtml = '<div class="mt-number-animate-dom" data-num="{{num}}">\
            <span class="mt-number-animate-span">0</span>\
            <span class="mt-number-animate-span">1</span>\
            <span class="mt-number-animate-span">2</span>\
            <span class="mt-number-animate-span">3</span>\
            <span class="mt-number-animate-span">4</span>\
            <span class="mt-number-animate-span">5</span>\
            <span class="mt-number-animate-span">6</span>\
            <span class="mt-number-animate-span">7</span>\
            <span class="mt-number-animate-span">8</span>\
            <span class="mt-number-animate-span">9</span>\
            <span class="mt-number-animate-span">.</span>\
          </div>';
  
    //数字处理
    var numToArr = function(num){
      num = parseFloat(num).toFixed(setting.dot);
      if(typeof(num) == 'number'){
        var arrStr = num.toString().split("");  
      }else{
        var arrStr = num.split("");
      }
      //console.log(arrStr);
      return arrStr;
    }
  
    //设置DOM symbol:分割符号
    var setNumDom = function(arrStr){
      var shtml = '<div class="mt-number-animate">';
      for(var i=0,len=arrStr.length; i<len; i++){
        if(i != 0 && (len-i)%3 == 0 && setting.symbol != "" && arrStr[i]!="."){
          shtml += '<div class="mt-number-animate-dot">'+setting.symbol+'</div>'+nHtml.replace("{{num}}",arrStr[i]);
        }else{
          shtml += nHtml.replace("{{num}}",arrStr[i]);
        }
      }
      shtml += '</div>';
      return shtml;
    }
  
    //执行动画
    var runAnimate = function($parent){
      $parent.find(".mt-number-animate-dom").each(function() {
        var num = $(this).attr("data-num");
        num = (num=="."?10:num);
        var spanHei = $(this).height()/11; //11为元素个数
        var thisTop = -num*spanHei+"px";
        if(thisTop != $(this).css("top")){
          if(setting.iniAnimate){
            //HTML5不支持
            if(!window.applicationCache){
              $(this).animate({
                top : thisTop
              }, setting.speed);
            }else{
              $(this).css({
                'transform':'translateY('+thisTop+')',
                '-ms-transform':'translateY('+thisTop+')',   /* IE 9 */
                '-moz-transform':'translateY('+thisTop+')',  /* Firefox */
                '-webkit-transform':'translateY('+thisTop+')', /* Safari 和 Chrome */
                '-o-transform':'translateY('+thisTop+')',
                '-ms-transition':setting.speed/1000+'s',
                '-moz-transition':setting.speed/1000+'s',
                '-webkit-transition':setting.speed/1000+'s',
                '-o-transition':setting.speed/1000+'s',
                'transition':setting.speed/1000+'s'
              }); 
            }
          }else{
            setting.iniAnimate = true;
            $(this).css({
              top : thisTop
            });
          }
        }
      });
    }
  
    //初始化
    var init = function($parent){
      //初始化
      $parent.html(setNumDom(numToArr(setting.num)));
      runAnimate($parent);
    };
  
    //重置参数
    this.resetData = function(num){
      var newArr = numToArr(num);
      var $dom = $(this).find(".mt-number-animate-dom");
      if($dom.length < newArr.length){
        $(this).html(setNumDom(numToArr(num)));
      }else{
        $dom.each(function(index, el) {
          $(this).attr("data-num",newArr[index]);
        });
      }
      runAnimate($(this));
    }
    //init
    init($(this));
    return this;
  }

  

  
  //初始化
  var numRun3 = $(".numberRun3").numberAnimate({num:'258650000', speed:2000});
  var numRun2 = $(".numberRun2").numberAnimate({num:'3864005', speed:2000});
  var numRun1 = $(".numberRun1").numberAnimate({num:'2843', speed:2000});
  
  //顶部轮播
var a= new Swiper ('.cs', {
  direction: 'horizontal',
  loop: true,
  autoplay : 3000,
	speed:300,
	slidesPerView : 3,
	paginationClickable :true,
    
    // 如果需要分页器
    pagination: '.cs .swiper-pagination',
    
    // 如果需要前进后退按钮
    nextButton: '.swiper-button-next',
    prevButton: '.swiper-button-prev',
})   	
$('.cs').mouseenter(function(){
	a.stopAutoplay();              //自动播放停止
	$(".bu-nex").show()
}).mouseleave(function(){
	a.startAutoplay();             //自动播放开始
	$(".bu-nex").hide()
});
  
//底部轮播
var mySwiper = new Swiper ('.cv', {
    direction: 'horizontal',
    loop: true,
    autoplay : 3000,
	  speed:300,
	  paginationClickable :true,
    
    // 如果需要分页器
    pagination: '.cv .swiper-pagination',
    
    // 如果需要前进后退按钮
    nextButton: '.swiper-button-next',
    prevButton: '.swiper-button-prev',
})   	
$('.cv').mouseenter(function(){
			mySwiper.stopAutoplay();              //自动播放停止
			$(".bu-nex").show()
		}).mouseleave(function(){
			mySwiper.startAutoplay();             //自动播放开始
			$(".bu-nex").hide()
});  	
  
  
});

	
