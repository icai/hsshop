$(function(){
	//点击modelTitle的时候，modelContent进行显示隐藏；
	$(document).on("click",".modelTitle", function(){
		$(this).siblings(".modelContent").toggle();
	})
	
	//点击删除
	$(document).on("click",".del", function(event){
		event.stopPropagation();    //  阻止事件冒泡
		var delEle = $(this).parents(".modelDiv");
		//确定事件；
		var sureEven = function(){
			hideDelProver();
		 	delEle.remove();
		}
		//取消事件；
		var cancelEven = function(){
			hideDelProver();
		}
		showDelProver($(this),sureEven,cancelEven,-266, 240);
	})
})