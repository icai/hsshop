$(function(){
	// 提示信息
	$(".tip_show").popover({ 
        trigger:'hover',
        container:'body',  
        placement : 'bottom',   
        html: 'false', 
        template:'<div class="popover f12 yellow_bg" role="tooltip"><h3 class="popover-title"></h3><div class="popover-content"></div></div>' ,
        content: function() {  
            return $(this).next('.js_tip').html();  
        },  
        animation: false,
    });

    // 结算方式
	$('.settlement_method').click(function(){
		var _cls = $('.'+ $(this).data('class') );
		$(this).next('.js_tip').removeClass('no');				// 该方式的提示框显示	
		var _obj = $(this).parent('label').siblings('label').find('.settlement_method');
		_obj.next('.js_tip').addClass('no');					//  兄弟元素的提示框隐藏
		if( _cls.hasClass('settlement_time') ){
			$('.settlement_time').removeClass('no');		// 结算时间显示
		}else{
			$('.settlement_time').addClass('no');			// 结算时间隐藏
		}
	});

	// 选择佣金
	$('.commission').click(function(){
		$(this).siblings('.commission_input').prop('disabled',false);
		var _obj = $(this).parent('label').siblings('label').find('.commission');
		_obj.siblings('.commission_input').prop('disabled',true);
	});
})