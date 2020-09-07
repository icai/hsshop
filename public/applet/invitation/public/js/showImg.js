$(function() {
	$("#back").click(function(){
		history.go(-1)
	})
	
	//判断显示的文字
	var type = 1;
	if(type==1){
		$(".wordsChange").html("#婚礼已经在筹备啦#")
	}else{
		$(".wordsChange").html("#不要忘了参加婚礼哦#")
	}
	var imgSrc = $("#browserImg").attr("src");
	//图片预览
	var pb1 = $.photoBrowser({
	  	items: [ imgSrc, ]
	});
	$("#browserImg").click(function() {
		console.log($(this).index())
        pb1.open($(this).index());
    });
    
	//复制粘贴
	var btn = document.getElementsByClassName('copy')[0];
	var clipboard = new Clipboard(btn);

	clipboard.on('success', function(e) {
		console.log(e);
		$.toast("复制成功！");
	});

	clipboard.on('error', function(e) {
		console.log(e);
		$.toast("复制失败！", "forbidden");
	});
})