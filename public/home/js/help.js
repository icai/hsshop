function GetRequest() {
 	var url = location.search; //获取url中"?"符后的字串
 	var theRequest = new Object();
	if (url.indexOf("?") != -1) {
  		var str = url.substr(1);
  		strs = str.split("&");
  		for(var i = 0; i < strs.length; i ++) {
   			theRequest[strs[i].split("=")[0]]=(strs[i].split("=")[1]);
  		}
 	}
 	return theRequest;
}

$(function () {
	var hot_arr = [
		{
			"icon":"home/image/help_1@2x.png",
			"title":"微信授权"
		},
        {
            "icon":"home/image/help_2@2x.png",
            "title":"支付"
        },
        {
            "icon":"home/image/help_3@2x.png",
            "title":"提现"
        },
        {
            "icon":"home/image/help_4@2x.png",
            "title":"多人拼团"
        },
        {
            "icon":"home/image/help_5@2x.png",
            "title":"快递打单"
        },
        {
            "icon":"home/image/help_6@2x.png",
            "title":"多客服"
        },
        {
            "icon":"home/image/help_7@2x.png",
            "title":"多门店"
        },
        {
            "icon":"home/image/help_8@2x.png",
            "title":"会员卡"
        },
	]
	var html = ''
    hot_arr.forEach(function (val,key) {
		if(key == 7){
            html += '<li class="hot_li"><a href="'+APP_URL+'home/index/helpList" target="_blank">' +
                '<img src='+ (APP_IMGURL + val.icon) +'>' +
                '<span>'+val.title+'</span>' +
                '</a></li>'
		}else if(key == 3){
            html += '<li class="hot_li"><a href="'+APP_URL+'home/index/helpList" target="_blank">' +
                '<img src='+ (APP_IMGURL + val.icon) +'>' +
                '<span>'+val.title+'</span>' +
                '</a></li>'
		}else{
			html += '<li><a href="'+APP_URL+'home/index/helpList" target="_blank">' +
                '<img src='+ (APP_IMGURL + val.icon) +'>' +
                '<span>'+val.title+'</span>' +
                '</a></li>'
		}
    })
	$(".help_hot_special_content").append(html)


	/*
	* @auther 邓钊
	* @desc 帮助中心搜索功能
	* @date 2018-7-9
	* */
	$(".right_btn").on('click',function () {
		var val = $(".right_inp").val()
        if(!val){
            return false
        }
        window.location.href = APP_URL + 'home/index/helpList?keywords=' + val
		var html = "<span>"+val+"</span>"
		var spans = $(".help_top_right_tip").children('span').length
		if(spans >= 4){
            $(".help_top_right_tip").children('span').eq(3).remove()
		}
        $(html).insertBefore($(".help_top_right_tip").children('span')[0]);
    })

    // 邓钊 2018年08月15日 回车搜索
    $("body").on('keydown',function (e) {
        if(e.keyCode == 13){
            $(".right_btn").click()
        }
    })
})
