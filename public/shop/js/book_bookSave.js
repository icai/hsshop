$(function(){
	
	//时间选择器
	if($("input").hasClass("book_date")){
		var calendar = new LCalendar();
	    calendar.init({
	        'trigger': '.book_date', //标签id
	        'type': 'date', //date 调出日期选择 datetime 调出日期时间选择 time 调出时间选择 ym 调出年月选择,
	        'minDate': limit_type == 0 ? start_at : '1900-1-1', //最小日期
	        'maxDate': limit_type == 0 ? end_at : ''
	    });

	}

    //时间选择器
    var timeArr = ['00:00--01:00','01:00--02:00','02:00--03:00','03:00--04:00','04:00--05:00','05:00--6:00','06:00--07:00','07:00--08:00','08:00--09:00','09:00--10:00','10:00--11:00','11:00--12:00','12:00--13:00','13:00--14:00','14:00--15:00','15:00--16:00','16:00--17:00','17:00--18:00','18:00--19:00','19:00--20:00','20:00--21:00','21:00--22:00','22:00--23:00','23:00--0:00'];
    var mobileSelect1 = new MobileSelect({
    trigger: '.book_time', 
    title: '预约时间',  
    wheels: [
                {data: timeArr}
            ],
    position:[8], //初始化定位 打开时默认选中的哪个 如果不填默认为0
    transitionEnd:function(indexArr, data){
        
    },
    callback:function(indexArr, data){
    	$(".book_time").val(data[0]);
        
    }
}); 

	//提交信息
	$('.detail_submit').on('click',function(){
		var data={}
		var length=$('.formData').length
		var content = {};
		for(var i=0;i<length;i++){
			content[i]={}
			var ykey=$('.formData').eq(i).children("label").text()
			var yval=$('.formData').eq(i).find("input").val() ? $('.formData').eq(i).find("input").val()  : $('.formData').eq(i).find("select option:checked").text()
			var ytype = $('.formData').eq(i).find("select option:checked").text() ? 'select' : 'text';
			var yclass=$('.formData').eq(i).children("input").attr('class') ? $('.formData').eq(i).children("input").attr('class') : ''
			content[i].ykey=ykey
			content[i].yval=yval
			content[i].ytype=ytype;
			content[i].yclass=yclass;
		}
		data.content=content
		var book_date = $(".book_date").val();
		var book_times = $(".book_time").val();
		
		data.content=content
		data.book_date = book_date;
		data.book_time = book_times;
		data.remark    = $(".remark").val();

		$.ajax({
			headers: {'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
			type:"post",
			url:"",
			async:true,
			data:data,
			success:function(response){
				if(response.status == 1 ){
                    tool.tip(response.info);
                    setTimeout(function(){
                    	window.location.href='/shop/book/user/list/'+wid+'/'+bookId;
                    },1000);
               } else {
                   tool.tip(response.info,'warn')
                }
			}
		});
	})
})
