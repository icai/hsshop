$(function(){
	$.get('/shop/point/selectSignRule',function(data){
		console.log(data)
		var html = "";
		$('.description').text(data.data.activityInfo);
		for(var i = 0;i < data.data.signList.length;i ++){
			html += '<li class="checkin-rule-item">'+(i+1)+'. 连续签到 '+data.data.signList[i].signDay+' 次，获得 '+data.data.signList[i].signCredite+' 积分奖励</li>';
		}
		$('.checkin-rule-list').html(html);
	});
});