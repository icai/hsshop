$(function(){
	//搜索
	$(".searchBtn").click(function(){
		var val = $("#searchVal").val();
		location.href = "/home/index/news?keywords="+val+"&clickTitle=搜索结果"
	})
	
	//点击左侧导航栏
	$(".leftp1").click(function(){
        $(this).addClass("border");
        var UL = $(this).next("ul");
        if(UL.css("display")=="none"){
        	$(this).find('.right_img_1').css('transform','rotate(90deg)');
            UL.css("display","block");
        }
        else{
        	$(this).find('.right_img_1').css('transform','rotate(0deg)');
            UL.css("display","none");
        }
    });

    var Request = new Object();
	Request = GetRequest();
	console.log(Request)
	console.log(Request.Pid)
	console.log(Request.info_type)
	var pi = Request.Pid;
	var ele = $(".leftp1");
	console.log(ele)
    for(var u=0; u<ele.length; u++){
        if(ele[u].dataset.id==pi){
        	console.log(ele[u].dataset.id)
        	console.log(pi)
            ele[u].classList.add("selected");
            $(ele[u]).addClass("border");
            var UL = $(ele[u]).next("ul");
            if(UL.css("display")=="none"){
                $(ele[u]).find('.right_img').css('transform','rotate(90deg)');
                UL.css("display","block");
                var as = UL.children('a');
                for(var s = 0; s < as.length; s++){
                    if($(as[s]).attr('data-id') == Request.info_type){
                        $(as[s]).children('li').css('color','#999')
                        break;
                    }
                }
            }
            else{
                $(ele[u]).find('.right_img').css('transform','rotate(0deg)');
                UL.css("display","none");
            }
            break;
        }
    }
    
    
    //点击二级导航
    $(".leftul1-l1").click(function(){
    	var eId = $(this).data("id");
    	var pId = $(this).data("pid");
    	var title = $(this)["0"].innerText;
    	console.log(eId, pId,title)
    	$(this).attr("href", "/home/index/news?info_type="+eId+"&Pid="+pId)
    })
});

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
