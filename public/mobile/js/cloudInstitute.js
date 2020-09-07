$(function(){
	var $categoryLi = $('.category').find('li');
	var $cloudContent = $('.cloud_content');
	for(var i=0;i<$categoryLi.length;i++){
//		$categoryLi.eq(i).click(function(){
//			var index = $(this).index();
//			$categoryLi.removeClass('active');
//			console.log($(this).hasClass());
//			$(this).addClass('active');
//			$cloudContent.hide();
//			$cloudContent.eq(index).show();
//		})
		if($categoryLi.eq(i).html() == "微营销技能"){
			$categoryLi.eq(i).css('min-width','1.7rem');
		}
	}
})