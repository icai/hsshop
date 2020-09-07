$(function(){
	$(".select-type").height(innerHeight)
	//点击弹出类型
	$(".app-catalog").click(function(e){
		e.stopPropagation();				//阻止事件冒泡
		$(".app-type").removeClass("none")
		$("body,html").css("overflow","hidden")
	})
	//点击弹出目录
	$(".all-catalog").click(function(e){
		e.stopPropagation();
		$(".all-type").removeClass("none")
		$("body,html").css("overflow","hidden")
	})
	//关闭弹框
	$(".select-type").click(function(){
		$(".select-type").addClass("none")
		$("body,html").css("overflow","auto")
	})
	






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





})
