$(function(){
//	轮播显示
	var myswiper = new Swiper('.swiper-container', {
	    direction: 'vertical',
	    loop:true,
	    autoplay:2000,
	    speed:2000,
	    autoplayDisableOnInteraction : false,
	    observer:true,//修改swiper自己或子元素时，自动初始化swiper 解决个别不loop
	});
//	提交
	$(".yuyue").click(function () {
    	//禁止重复提交
    	$(".yuyue").attr("disabled","disabled");	
    	$(".get-focus").on("input focus", function () {
		    $(".yuyue").attr("disabled",false);
		});	
	
        $.ajax({
            url:'',
            data:$('#myform').serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (res) {      
            	console.log(res)
	            if(res.status == 1){
					if(res.data.flag == 0){//可注册
						tool.hitEgg({
							type:1,
							content: $("input[name='title']").val(),
						});
					}else{
						tool.hitEgg({
							type:2,
							content: $("input[name='title']").val(),
						});
					}
				}else{
					tipshow(res.info,"warn");
				}
            },
            error : function() {
                alert("数据访问异常");
            }
        });
    }) 
});

function tip(content){
	$(".tip").remove();
	var _html = '<div class="tip">'+content+'</div>';
	$("body").append(_html);
	var Timer = setTimeout(function(){
		$(".tip").remove();
	},3000);
}

var tool = {};
tool.hitEgg = function(obj){
    var html = "";
    html += '<div id="mask" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;"></div>';
    html += '<div class="c_model" id="suc_register" style="background:#fff;padding: 8px;overflow: hidden; position: fixed; z-index: 1000; transition: opacity 300ms ease; top: calc(50% - 40px); left: 50%; transform: translate3d(-50%, -50%, 0px); visibility: visible; border-radius: 4px; background: white; width: 890px;opacity: 1;padding-bottom: 30px;">'
	html += '<div class="hearder" style="text-align: right;">'
	html += '<a href="javascript:void(0);" class="delete"></a>'
	html += '</div>'
	html += '<div class="model_content" style="background:#fff;">'
	html += '<div class="content_top">'
	if(obj.type == 1){
	html += '<i class="icon_logo"></i>'
	html += '<span class="info ff49">恭喜!</span>'
	html += '</div>'
	html += '<p class="content_info">小程序：<span class="fb31">'+obj.content+'</span>可以使用!</p>'
	html += '<div class="button_group">'
	html += '<a class="register" href="/home/index/reserve?type=3">立即注册，抢占先机！</a>'
	html += '<a class="again" href="javascript:void(0);">继续查询</a>'
	html += '</div>'
	}else{
	html += '<i class="icon_logo1"></i>'
	html += '<span class="info ff49">噢~</span>'
	html += '<p class="content_info">小程序：<span class="ff49">'+obj.content+'</span>已被使用!</p>'
	html += '<div class="button_group">'
	html += '<a class="register search" href="javascript:void(0);">立即查询其他小程序</a>'
	html += '</div>'
	}
	html += '</div>'
	html += '</div>'
    html += '</div></div>';
    $('body').append(html);
    $(".again").off().on("click",function(){
        $('#mask').remove();
        $('.c_model').remove();
    });
    $(".again, .delete,.button_group .search").off().on("click",function(){
    	$('#mask').remove();
        $('.c_model').remove();
    });

}
