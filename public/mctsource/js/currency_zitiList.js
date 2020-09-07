$(function(){
	var index;
	var storeId = "";
	$('body').on('click','.pop',function(event){
		if(count == 1){
			tipshow('商品开启了自提，删除地址可能会导致下单失败，请谨慎操作','warn');
		}
		index = $(this).parents("tr").index();
		storeId = $(this).data("id");  
		$('.pop').not($(this)).removeClass('active');
		$(this).toggleClass('active');
		$('.popover').hide();
		$('.'+$(this).data('toggle')).css({
			'top':$(this).offset().top - $('.'+$(this).data('toggle')).height()/2 -56,
			'left':$(this).offset().left - $('.'+$(this).data('toggle')).width() - 230,
		});
		$('.'+$(this).data('toggle')).show();
	});
	//给Body加一个Click监听事件
	$('body').on('click', function(event) {
		var target = $(event.target);
		if (!target.hasClass('active')
				&& target.parents('.popover').length === 0
		        && target.parents('.active').length === 0) {
		        //弹窗触发列不关闭，否则显示后隐藏
		    popoverHidden();
		}
	});
	function popoverHidden(){//使用此方法必须用此方法隐藏popover
		$('.popover').hide();
		$('.pop').removeClass('active');
	}
	$('.popover .sure_btn').click(function(){//确定删除
		$.get(" /merchants/currency/delZiti",{id:storeId},function(data){
			if(data.status == 1){
				tipshow(data.info,'info');
				$('.table tbody tr:eq('+index+')').remove();
				window.location.reload();
			}else{
				tipshow(data.info,'warn');
			}
		});
		popoverHidden();//隐藏pop
	});
	$('.popover .cancel_btn').click(function(){//取消删除
		popoverHidden();//隐藏pop
	});	

	// 按钮样式
    $('.switch_items').click(function(event){
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
			url:"/merchants/currency/startZiti",
			data: {
				is_on: status
			},
        	// async:true,
        	success:function(res){
        		//保存成功后 移除新增栏目 插入新的ul 
                if(res.errCode==0){
                    if (open == "1") {
						tipshow('已关闭');
                        //切换成关闭状态
                        $(_this).find('label').removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-open", "0");
                     
                    } else {
						tipshow('已开启');
                        //切换成开启状态
                        $(_this).find('label').removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");
                    }
                }else{
                   tipshow(res.errMsg,"warn"); 
                }
        	},
        	error:function(){
        		alert("数据访问错误")
        	}
        });
    });
})