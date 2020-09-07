$(function(){
	//存在数据时
	if($.isEmptyObject(info)==false){
		console.log("made")
		$('.switch_items').find('label').removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");
		$("input[name='account_sid']").val(info.account_sid)
		$("input[name='account_token']").val(info.account_token)
		$("input[name='phone']").val(info.phone)
		$("input[name='app_id']").val(info.app_id)
		$("input[name='code']").val(info.code)
		$("input[name='id']").val(info.id)
		$(".contentDiv").show();
		$(".weiXin_title").css({'border-bottom':'1px','border-bottom-color':'#cccccc','border-bottom-style':'solid'})
	}
	
    // 按钮样式
    $('.switch_items').click(function(event){
    	//alert($("input").prop("checked"));   //触发的事件；
        $(this).find('label').addClass('loadding');
        var _this = $(this);
        var open = $(this).find('label').attr("data-is-open");  
        var status = open=="1"?0:1;
        setTimeout(function(){
            _this.find('label').removeClass('loadding');
        },80);
        event.stopPropagation();    //  阻止事件冒泡
        //提交修改
        $.ajax({
        	type:"get",
        	url:"/merchants/currency/delSmsConf",
        	async:true,
        	success:function(res){
        		console.log(res)
        		//保存成功后 移除新增栏目 插入新的ul 
                if(res.status==1){
                    tipshow(res.info);
                    if (open == "1") {
                        //切换成关闭状态
                        $(_this).find('label').removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-open", "0");
                     
                    } else {
                        //切换成开启状态
                        $(_this).find('label').removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");
                        
                    }
                }else{
                   tipshow(res.info,"wram"); 
                }
        	},
        	error:function(){
        		alert("数据访问错误")
        	}
        });
    });
    
    //点击出现隐藏内容；
//  $(".title").each(function(index, ele){
	$('.switch_items').click(function(){
		$(".title").css("border-bottom", "1px solid #ccc");
		$(".title").next().toggle();
		if ($(".title").next().css("display") != "none") {
    		$(".title").find(".rowImg").css({"transform": "rotate(180deg)", "transitionDuration": "0.2s"})
		}else{
			$(".title").css("border-bottom", "1px solid transparent");
			$(".title").find(".rowImg").css({"transform": "rotate(0deg)", "transitionDuration": "0.2s"})
		}
	})
//  });
    
    
    //提交表单
    $(".btn-default").click(function(){
    	$.ajax({
    		type:"POST",
    		url:"/merchants/currency/setSmsConf",
    		data:$("#form").serialize(),
    		headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        },
    		async:true,
    		success:function(res){
    			if(res.status == 1){
    				tipshow(res.info)
    			}else{
    				tipshow(res.info,'warn')
    			}
    		},
    		error:function(){
    			
    		}
    	});    	
    })
})