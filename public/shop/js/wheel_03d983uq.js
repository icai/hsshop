//add by 韩瑜 2018-8-14
$(function(){
	var url = location.href
	var urlarr = url.split('/');
	var giftid = urlarr[urlarr.length-2]
	var type = urlarr[urlarr.length-1]
	console.log(giftid)
	//点击修改按钮
	
	//点击确认按钮
	if(type == 1){
		$('.edit_btn').click(function(){
			window.location.href = '/shop/member/showAddress?giftid=' + giftid + '&wid=' + wid + '&activity_id=' + activity_id + '&come=gift1';
		})
		$('.sure_btn').click(function(){
			console.log(type)
			$.ajax({
				type:"post",
				url:'/shop/activity/setAwardAddress/'+ wid,
				data:{
					type:1,//1大转盘，2砸金蛋，3刮刮卡
		      		activityId:activity_id,
		      		addressId:address_id,
		        	isConfirm:1,
		        	_token: _token
				},
				success:function(res){
					location.href = '/shop/activity/method/'+ giftid + '/1';
				}
			});
		})
		$('.no_address').click(function(){
			location.href = '/shop/member/addAddress?come=gift1' + '&activity_id=' + activity_id + '&wid=' + wid + '&giftid=' + giftid 
		})
	}else if (type == 2){
		$('.edit_btn').click(function(){
			window.location.href = '/shop/member/showAddress?giftid=' + giftid + '&wid=' + wid + '&activity_id=' + activity_id + '&come=gift2';
		})
		$('.sure_btn').click(function(){
			console.log(type)
			$.ajax({
				type:"post",
				url:'/shop/activity/setAwardAddress/'+ wid,
				data:{
					type:2,//1大转盘，2砸金蛋，3刮刮卡
		      		activityId:activity_id,
		      		addressId:address_id,
		        	isConfirm:1,
		        	_token: _token
				},
				success:function(res){
					location.href = '/shop/activity/method/'+ giftid + '/2';
				}
			});
		})
		$('.no_address').click(function(){
			location.href = '/shop/member/addAddress?come=gift2' + '&activity_id=' + activity_id + '&wid=' + wid + '&giftid=' + giftid 
		})
	}
	
})
