$(function(){
	var index;
	var storeId = "";
	$('body').on('click','.pop',function(event){
		index = $(this).parents("tr").index();
		storeId = $(this).data("id");
		$('.pop').not($(this)).removeClass('active');
		$(this).toggleClass('active');
		$('.popover').hide();
		$('.'+$(this).data('toggle')).css({
			'top':$(this).offset().top - $('.'+$(this).data('toggle')).height()/2 -43,
			'left':$(this).offset().left - $('.'+$(this).data('toggle')).width() - 212,
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
        $('.code').hide()
	});
	function popoverHidden(){//使用此方法必须用此方法隐藏popover
		$('.popover').hide();
		$('.pop').removeClass('active');
	}
	$('.popover .sure_btn').click(function(){//确定删除
		$.get(" /merchants/currency/delStore/"+storeId,function(data){
			if(data.status == 1){
				tipshow(data.info,'info');
				$('.table tbody tr:eq('+index+')').remove();
			}else{
				tipshow(data.info,'warn');
			}
		});
		popoverHidden();//隐藏pop
	});
	$('.popover .cancel_btn').click(function(){//取消删除
		popoverHidden();//隐藏pop
	});
	var shop_code = null;
	var xcx_code = null;
	var link_code = null;
	$("#downCode").on('click',function (e) {
        e.stopPropagation(); //阻止事件冒泡
		$.ajax({
			url:'/merchants/currency/outlets/getStoreCode',
			success:function (res) {
                console.log(res);
                if(res.status == 1){
                    shop_code = res.data.code;
                    xcx_code = res.data.xcxCode;
                    link_code = res.data.url;
                    $(".code_img").find("div").html(shop_code);
                    $(".code_img").find("p").attr("data-id","0");
                    $(".code_img").find("p").html("下载二维码");
                    $(".copy input").val(link_code)
                    if(!xcx_code){
						$(".xcx_code").hide();
						$(".code ul").css('padding',"0 60px");
					}
                    if(!shop_code){
                        $(".shop_code").hide();
                        $(".code ul").css('padding',"0 60px");
                    }
                    if(!link_code){
                        $(".link_code").hide();
                        $(".code ul").css('padding',"0 60px");
					}
					$('.code').show();
				}
            }
		})
    })
    $(".code_img").find("p").on('click',function (e) {
        e.stopPropagation(); //阻止事件冒泡
		var id = $(this).attr('data-id');
		if(id == 0){
			window.location.href = '/merchants/member/memberCard/down_qrcode?qrcode_type=store'
		}else if(id == 1){
            window.location.href = '/merchants/currency/outlets/downloadStoreXcxCode'
		}
    })
	$(".code").find('li').on('click',function (e) {
        e.stopPropagation(); //阻止事件冒泡
		$(this).addClass('li_code_active').siblings('li').removeClass('li_code_active')
		var id = $(this).attr('data-id');
		if(id == 0){
            $(".code_img").show()
            $(".copy").hide()
            $(".code_img").find("div").html(shop_code);
            $(".code_img").find("p").attr("data-id","0");
            $(".code_img").find("p").html("下载二维码");
		}else if(id == 2){
            $(".code_img").show()
            $(".copy").hide()
			var html = '<img src="data:image/png;base64,'+xcx_code+'"/>'
            $(".code_img").find("div").html(html);
            $(".code_img").find("p").attr("data-id","1");
            $(".code_img").find("p").html("下载小程序码");
		}else if(id == 1){
            $(".code_img").hide()
            $(".copy").show()
		}
    })
    $('#copy_span').click(function(e){
        e.stopPropagation(); //阻止事件冒泡
        var obj = $(this).siblings('input');
        copyToClipboard( obj );
        tipshow('复制成功','info');
        setTimeout(function () {
            $('.code').hide()
        },1000)
    });
})

function copyToClipboard( obj ) {
    var aux = document.createElement("input");                  // 创建元素用于复制
    // 获取复制内容
    var content = obj.text() || obj.val();
    // 设置元素内容
    aux.setAttribute("value", content);
    // 将元素插入页面进行调用
    document.body.appendChild(aux);
    // 复制内容
    aux.select();
    // 将内容复制到剪贴板
    document.execCommand("copy");
    // 删除创建元素
    document.body.removeChild(aux);
}