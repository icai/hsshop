$(function(){

    // 分页样式
	// 上一页
	$(".page").on('click','.firstPage',function(){
		$(".page").find(".active").removeClass("active")
		$(this).addClass("active")
	})

	$(".page").on('click','li:not(.ellipsis)',function(){
		$(".page").find(".active").removeClass("active")
		$(this).addClass("active")
	})
	// 下一页
	$(".page").on('click','.lastPage',function(){
		$(".page").find(".active").removeClass("active")
		$(this).addClass("active")
	})

	$('.yes_btn').click(function(){
		alert('很高兴为您服务！');
		$('.yes_btn').unbind('click');
		$('.no_btn').unbind('click');
	 })
	 $('.no_btn').click(function(){
		 $('.tip_model').css('left',window.screen.availWidth/2 - $('.tip_model').outerWidth()/2);
		 $('.tip_model').css('top',window.screen.availHeight/2 - $('.tip_model').outerHeight()/2);
		 $('.modal-backdrop').show();
		 $('.tip_model').show();
	 })
	 $('.modal-backdrop').click(function(){
	   $('.modal-backdrop').hide();
	   $('.tip_model').hide();
	 })
	 $('.btn_save').click(function(){
	   $.get('/home/index/putQuestion',$('form').serialize(),function(data){
		 $('.no_btn').unbind('click');
		 $('.yes_btn').unbind('click');
		 $('.modal-backdrop').hide();
		 $('.tip_model').hide();
	   })
	 })

})