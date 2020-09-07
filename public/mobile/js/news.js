/*author 韩瑜 
 *date 2018.7.9
 *{param}
*/
$(function(){
	//点击导航
	$('.nav_list_item').click(function(){
		$(this).addClass("nav_border").siblings().removeClass("nav_border");
	})
	//横向导航
	var now_left = $('.nav_border').offset().left;
    var now_right = now_left + $('.nav_border').width();
    var now_width = $(window).width();
    var now_hide = now_right - now_width
	if(now_right > now_width){
		$(".nav_list").scrollLeft(now_hide); 
	} 
	// add by 赵彬 2018-8-20
	// 滚动加载
	var status = true; 
	var page = 1;
	$(window).scroll(function(){
		if($(this).scrollTop() + $(window).height() >= $(document).height() - 400 && status){
			page++;
			status = false;
			$.ajax({
				url:'/home/index/news',
				data:{
					page:page,
					api:true,
					Pid:pid
				},
				type:'get',
				success:function(res){
					if(res.status == 1){
						var data = res.data.newsList.data;
						var html = '';
						if(data.length > 0){
							for(var i = 0; i<data.length; i++){
								html += '<div class="zixun_list_item">'+
											'<a href="/home/index/newsDetail/'+data[i].id+'/news">';
								if(data[i].source == undefined){
										html += '<div class="zixun_list_item_img">'+
													'<img width="240px" height="220px" src=""/>'+
												'</div>';
								}else{
										html +=	'<div class="zixun_list_item_img">'+
													'<img src="'+imgUrl + data[i].source.l_path+'" alt="" />'+
												'</div>';
								}
										html += '<div class="zixun_list_item_r">'+
													'<h3>'+data[i].title+'</h3>'+
													'<p>'+data[i].created_at+'</p>'+
												'</div>'+
											'</a>'+
										'</div>';
							}
							status = true
						}
						
						$(".zixun_list").append(html)
					}
					
				}
			})
		}
	})
	//end
})