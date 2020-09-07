$(function(){
	//公告动画
	var _left = 0;
	var win_width = $(window).width();
	var _hint_width = $(".public_hint p").css("width");
	var SD = setInterval(function(){
		_left -= 2;
		$(".public_hint p").css("left", _left);
		if(-_left >= parseInt(_hint_width)){
			_left = win_width;
		}
	}, 50)
	
	//点击提交
	$("#submit").click(function(){
		var phoneNum = $("#mobile").val()
		if (!$("#name").val() || !(/^1(3|4|5|6|7|8|9)\d{9}$/.test(phoneNum))) {
			tool.tip("请填写正确的格式")
		}else{
			$(".dialog_div").css("display", "block")
		}
	});
	
	//隐藏对话框
	$(".dialog_board, .cancle, .sure").click(function(){
		$(".dialog_div").css("display", "none")
	})
	
	//点击确定提交
	$(".sure").click(function(){
		var name = $("#name").val();
		var phone = $("#mobile").val();
		$.post("/shop/activity/registerSalesMan/"+wid, {
			_token: $('meta[name="csrf-token"]').attr('content'),
			name  : name,
			mobile: phone
		}, function(res){
			console.log(res, "-=-=-res")
			if (res.status == 1 ){
				tool.tip(res.info);
				window.location.reload();
			}else {
                tool.tip(res.info)
			}

		})
	})

	//上拉加载更多

	var page = 2;
	var flag = true;
    $(window).scroll(function() {
        if ($(this).scrollTop() + $(window).height() + 100 >= $(document).height() && $(this).scrollTop() > 100) {
            var nickname = $(".search_input input[name='nickname']").val();
            var mobile = $(".search_input input[name='mobile']").val();
            var name = $(".search_input input[name='name']").val();
        	var url = "/shop/activity/registerSalesMan/"+wid+"?page="+page+"&nickname="+nickname+'&mobile='+mobile+'&name='+name+'&flag=1';
        	if (flag){
        		flag = false;
                $.get(url, function(result){
                    if (result.status == 1){
						var data = result.data[0].data;
						console.log(data);
						if (data.length>0){
                            for (var i=0;i<data.length;i++){
                                var is_open_groups = '<a href="/shop/activity/getGroupsInfo?id='+data[i].id+'">是(点击查看)<a/>';
                                if (data[i].is_open_groups == '0'){
                                    is_open_groups = '否';
                                }
                                var html = '<ul class="list list_body"> ' +
                                    '<li>'+data[i].nickname+' </li> ' +
                                    '<li> '+is_open_groups+'</li> ' +
                                    '<li> '+data[i].level+'</li> ' +
                                    '<li> '+data[i].intime+'</li> ' +
                                    '</ul>';
                                $('.list_div').append(html);
                            }
                            page = page+1;
                            flag = true;
						}else{
                            $('.list_div').append("<div  style='text-align: center;'>没有更多信息</div>");
						}


					}else{

					}
                });
			}

        }
    });
    
    
    
    $('#search').click(function () {
    	flag = true;
    	var nickname = $(".search_input input[name='nickname']").val();
        var mobile = $(".search_input input[name='mobile']").val();
        var name = $(".search_input input[name='name']").val();
        var url = "/shop/activity/registerSalesMan/"+wid+"?nickname="+nickname+'&mobile='+mobile+'&name='+name+'&flag=1';
        $.get(url, function(result){
            $('.list_div').children().remove();
            var data = result.data[0].data;
            console.log(data);
            if (data.length>0){
                for (var i=0;i<data.length;i++){
                    var is_open_groups = '是';
                    if (data[i].is_open_groups == '0'){
                        is_open_groups = '否';
                    }
                    var html = '<ul class="list list_body"> ' +
                        '<li>'+data[i].nickname+' </li> ' +
                        '<li> '+is_open_groups+'</li> ' +
                        '<li> '+data[i].level+'</li> ' +
                        '<li> '+data[i].intime+'</li> ' +
                        '</ul>';
                    $('.list_div').append(html);
                }
            }

		})



    })


})

