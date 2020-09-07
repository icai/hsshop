$(function(){
	// 下拉实现提取记录的加载
	var noData = false;
    var page = 1;
    getData(1);
	$(window).scroll(function(){
		var clientHeight = $(window).scrollTop() + $(window).height();//实际高度
		if(clientHeight>=$(document).height() && !noData){
			page++;
			getData(page);
		}
    });
    function getData(page){
        $.post('/shop/distribute/cashLog',{page:page,_token:$('meta[name="csrf-token"]').attr('content')},function(res){
            if (res.status == 1){
                var html = '';
                var status = '';
                var data = res.data,year,timer;
                for(var i = 0;i<data.length;i++){
                    switch (data[i].status)
                    {
                        case 0:
                            status = '已申请';
                            break;
                        case 1:
                            status = '等待打款';
                            break;
                        case 2:
                            status = '已打款';
                            break;
                        case 3:
                            status = '已拒绝';
                            break;
                    }

                    year = data[i].created_at.split(' ')[0];
                    timer = data[i].created_at.split(' ')[1];
                   
                    html += '<div class="earning-body-item">\
                                <div class="timer-item">'+year+ '<br>'+timer+'</div>\
                                <div class="money-item">￥'+data[i].money+'</div>\
                                <div class="status-light">'+status+'</div>\
                            </div>';
                }
                if(data.length < 15){
                    noData = true;
                }
                $('.container').append(html);
            }
           
        })
    }
    
});