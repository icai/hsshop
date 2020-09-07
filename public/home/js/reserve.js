

$(function(){
    $(".ding-hangye li:gt(10)").css("display","none");
    $(".ul-last").html("更多...");
    $(".ul-last").click(function(){
        var a = $(".ding-hangye li").length;
        if($(".ding-hangye li:gt(10)").css("display")=="block"){
            $(".ding-hangye li:gt(10)").css("display","none")
            $(".ul-last").html("更多...");
        }
        else{
            $(".ul-last").html("收起...");
            $(".ding-hangye li:gt(10)").css("display","block")
        }
    });
    
    /**
	 * Created by admin on 2017/4/18.
	 */
	$('#myTab a').click(function (e) {
	    e.preventDefault();
	    $(this).tab('show');
	});
	    
    $(".xue-ldiv li").click(function(){
        $(".xue-ldiv li").css("color","#999999")
        $(this).css("color","black");

    })
    
    $(":text").focus(function(){
	    $(this).blur(function(){
	        if($(this).val()==""){
	            $(this).css("border-color","red")
	        }else{
	            $(this).css("border-color","#e6e6e6")
	        }
	    })
		})
	$(".ding-hangye li").click(function(){
	    $(".hangy").val("");
	    $(".hangy").val($(this).html());
	});
	
    $(".yuyue").click(function () {
    	//禁止重复提交
    	$(".yuyue").attr("disabled","disabled");	
    	$(".get-focus").on("input focus", function () {
		    $(".yuyue").attr("disabled",false);
		});	
		
        $.ajax({
            url:'/home/index/reserve',// 跳转到 action
            data:$('#myform').serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    $("#myform")[0].reset();
                    $(".suc-box").show();
                    var url = '';
                    if (type == 1){
                        url = '/home/index/distribution';
                    }else if (type == 2){
                        url = '/';
                    }else if(type==3){
                        url = '/home/index/applet';
                    }else if (type == 4){
                        url = '/home/index/microMarketing';
                    }
                    setTimeout(function(){
//                  	window.location.href=url;
                    },2000);
//                  window.location.href=url;
                    setTimeout("none()",1000);
                    setTimeout(function(){
                    	$(".suc-box").hide();
                    },3000);
                }else{
                    alert(response.info);
                }
            },
            error : function() {
                // view("异常！");
                alert("异常！");
            }
        });
    })   
    
});
