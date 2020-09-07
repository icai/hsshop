$(function(){
	init();
	$(window).resize(function() {
		init();
	});

	/*移动*/
	starScroll( $('.main'));

	// 登录弹框
	$('.logo_btn').click(function(){
		$('.logo_box').show();						// 登录框显示
		$('.mask').show();							// 遮罩层显示
		$('.down_box').hide();					    // 下载框隐藏
	});

	// 下载弹框
	$('.down_btn').click(function(){
		$('.down_box').show();					    // 下载框显示
		$('.mask').show();							// 遮罩层显示
		$('.logo_box').hide();						// 登录框隐藏
	});

	// 关闭弹框
	$('.colse_btn ,.mask').click(function(){
		$('.down_box').hide();					    // 下载框隐藏
		$('.mask').hide();							// 遮罩层隐藏
		$('.logo_box').hide();						// 登录框隐藏
	});

    $(".ajax-post").click(function(){
        var _form=$("form");
        $.post(_form.attr("action"),_form.serialize(),function(data){
            if(data.status==1){
                window.location.href=data.url;
            }else{
                layer.msg(data.info,{icon:2,time:2000});
            }
        },'json');
        return false;
    });
})

/**
 * 鼠标滑轮滚动事件 该函数第一次进入只能先向下滚动
 * @param  {[type]}  obj   滚动对象
 * @return {[type]}         [description]
 */
function starScroll ( obj){
	var waitTimer = 1000 ; 					// 初始化等待时间
	var flag = false ; 						// false -> 第一次进入 ， 1->第一张 ， 2-> 第二张
	var timer = new Date();
	
	// 页面点击
    $('.page').click(function(){
		flag = $(this).index();
		if( new Date() - timer > waitTimer ){
			starMove( $('.main') ,flag);
			timer = new Date();
		}	
	});
    
    // 判断滚动
    function  scrollFunc ( e ){
        var e = e || window.event ;
        if( e.wheelDelta ){ 					// IE 或 谷歌
            if( flag === 'false'){				// 第一次进入
                flag = 0;   
            }else{								// 非第一次进入
                if( e.wheelDelta > 0 && new Date() - timer > waitTimer){	 //向上滑动 ,图片向前滚动
                    if( flag == 0 ){
                        flag = 0 ;
                    }else{
                       flag --; 
                    }
                    starMove( obj , flag);
                    timer = new Date();
                }

                if( e.wheelDelta < 0 && new Date - timer > waitTimer){ // 向下滑动,图片向后滚
                    if( flag == obj.find('.carousel').length -1 ){
                        flag = obj.find('.carousel').length -1 
                    }else{
                       flag ++; 
                    }
                    //轮播
                    starMove( obj ,flag);
                 
                    timer = new Date();
                }
            }
            
        }else if( e.detail){

            if( flag === 'false'){ //第一次进入
                flag = 0;   
            }else{
                if( e.detail < 0 && new Date() - timer >waitTimer){		//向上滑动 ,图片向前滚动
                    //循环？非循环
                    if( flag == 0 ){
                        flag = 0 ;
                    }else{
                       flag --; 
                    }
                    //轮播
                    starMove( obj ,flag);
                    timer = new Date();
                }

                if( e.detail > 0 && new Date - timer > waitTimer){ //向下滑动,图片向后滚

                    if( flag == obj.find('.carousel').length -1 ){
                        flag = obj.find('.carousel').length -1 
                    }else{
                       flag ++; 
                    }
                    
                    //轮播
                    starMove( obj ,flag);
                    
                    timer = new Date();
                } 
            }
        }
    }

    // if( document.addEventListener ){
    //     document.addEventListener('DOMMouseScroll','scrollFunc',false);
    // }
    if(document.addEventListener){ 
		document.addEventListener('DOMMouseScroll',scrollFunc,false); 
	}
    document.onmousewheel = scrollFunc;
}

/**
 * [starMove 开始运动函数]
 * @param  {[type]} obj  [运动的对象]
 * @param  {[type]} flag [目标]
 * @return {[type]}      [无]
 */
function starMove( obj ,flag){
	var _top = -flag*getPageSize()[3];
	obj.stop().animate({top: _top},900,function(){
		$('.page').removeClass('hover').eq( flag ).addClass('hover');
	});
}

/**
 *   
 * [init 初始化函数]
 * @return {[type]} [description]
 */
function init(){
    $('.viewport ,.carousel').height(getPageSize()[3]);						// 最外盒子和显示区的高度
    $('.main').height( $('.carousel').height()*$('.carousel') );			// 可视区父级的高度
    for( var i=0;i<$('.carousel').length;i++){
    	var _height = $('.carousel').eq(i).find('.header_wrap').innerHeight() +$('.carousel').eq(i).find('.footer_wrap').innerHeight()+20;// 20px为离底部20px
    	$('.carousel').eq(i).find('.content').height( getPageSize()[3] - _height);	
    }
    $('.pg2 .show_items').height( $('.pg2').parent().height() );
    $('.pg3 .show_items').height( $('.pg3').parent().height() );
}
