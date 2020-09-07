$(function(){
    var Request = new Object();
    Request = GetRequest();

    // $(".tab_nav li").removeClass("hover")
    if(type == 1){
    	$(".buildXCXNew, .buildGZHNew").show()
        // $(".tab_nav li").eq(1).addClass("hover")
    }else {
    	$(".buildXCXNew, .buildGZHNew").hide()
    	$(".sentdHistory").css('float','none')
        // $(".tab_nav li").eq(2).addClass("hover")
    }

	//新建微信模版
	$(".buildXCXNew").click(function(){
		$('#myModal').modal()
		$("#myModal .xcx").show()
		$("#myModal .gzh").hide()
	})
	//新建公众号模版
	$(".buildGZHNew").click(function(){
		$('#myModal').modal()
		$("#myModal .xcx").hide()
		$("#myModal .gzh").show()
	})
	//选择小程序模版
	$(".xcx .button").click(function(){
		var type = $(this).data("type");
		location.href = "/merchants/message/save?type="+type;
	})
	//选择公众号模版
	$(".gzh li").each(function(index, ele){
		$(this).click(function(){
			var type = $(this).data("type");
			location.href = "/merchants/message/create?type="+type;
		})
	})
	// 删除模板列表
    $('body').on('click','.tempDelBtn',function(e){            
        e.stopPropagation();
        var _this = this;
        var id=$(this).data('id');
        showDelProver($(_this),function(){
            $.ajax({
                type:"post",
                url:'/merchants/message/del?id='+id,
                data:{
                    id:id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    if(res.status===1){
                        tipshow('删除成功','info');
                        $(_this).parents('.data_content').remove();
                    }else{
                        tipshow('删除失败','warn');
                    }
                },
                error:function(){
                    alert('数据访问异常')
                }
            }); 
        })
    }); 

    // 删除记录列表
    $('body').on('click','.recordDelBtn',function(e){            
        e.stopPropagation();
        var _this = this;
        var id=$(this).data('id');
        showDelProver($(_this),function(){
            $.ajax({
                type:"post",
                url:'/merchants/message/record/del?id='+id,
                data:{
                    id:id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    if(res.status===1){
                        tipshow('删除成功','info');
                        $(_this).parents('.record_data_content').remove();
                    }else{
                        tipshow('删除失败','warn');
                    }
                },
                error:function(){
                    alert('数据访问异常')
                }
            }); 
        })
    }); 
    //发送小程序模板
    $(document).on('click', ".send", function(e){
    	e.stopPropagation();
        var _this = this;
        var id=$(this).data('id');
        var type = $(this).data('type');
        showDelProver($(_this),function(){
            $.ajax({
                type:"post",
                url:type==0 ? '/merchants/message/send?id='+id : '/merchants/message/sendWeixinTemp?id='+id,
                data:{
                    id:id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    console.log(res);
                    tipshow(res.info,'info');
                },
                error:function(){
                    tipshow("数据异常",'warning');
                }
            }); 
        }, "确定发送模版消息？")
    });
})

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
