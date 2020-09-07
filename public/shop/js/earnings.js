$(function(){
	// 下拉实现提取记录的加载
	var noData = false;
	var page = 1;
	$(window).scroll(function(){
		var clientHeight = $(window).scrollTop() + $(window).height();//实际高度
		if(clientHeight>=$(document).height() && !noData){
			page++;
			$.get(' /shop/distribute/earnings?page='+page,function(data){
				var html = '';
				var status = '';
				for(var i = 0;i<data.data.length;i++){
					switch (data.data[i].status)
	    			{
	    				case 0:
	    					status = '待提现';
	    				break;
	    				case 1:
	    					status = '已到账';
	    				break;
	    				case 2:
	    					status = '已流失';
	    				break;
					}
		
					html+=	'<div class="earning-body-item">\
								<div class="wd_40 header-item">'+data.data[i].order.oid+'</div>\
								<div class="wd_30 header-item">'+data.data[i].money+'</div>\
								<div class="wd_30 header-item status-light">'+status+'</div>\
				            </div>';
				}
				if(data.data.length<15){
					noData = true;
				}
				$('.js-earning-body').append(html);
			});
		}
	})
});