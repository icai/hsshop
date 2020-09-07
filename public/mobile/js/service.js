$(function() {
	$('.fuwu').hide();
	$('.dianhua').hide();
	var kaiqiYuyue = $('.start_reservation');
	var $name = $('.s_input').eq(0).find('input');
	var $phone = $('.s_input').eq(1).find('input');
	var $industry = $('.s_input').eq(2).find('input');
	
	$(".get-focus").on("input focus", function () {
	    $('.start_reservation').attr("disabled",false);
	});	
	kaiqiYuyue.on('click', function(e) {
		//禁止重复提交
    	$('.start_reservation').attr("disabled","disabled");		
		e.stopPropagation();
		var $phoneVal = $phone.val();
		var $nameVal = $name.val();
		$.post('/home/index/reserve', {
			"name": $nameVal,
			"phone": $phoneVal,
			"type": sel_index,
			"industry": $industry.val(),
			"_token":$('meta[name="csrf-token"]').attr('content')
			//"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
		}, function(data, status) {	
			console.log(data)
			if(data.status == 1) {
				var shade = '<div class="shade"></div>';
				$("body").append(shade);
				$('.sel_suc').css('left', ($(window).width() - $('.sel_suc').width()) / 2 + 'px').show();
			} else if(data.status == 0) {
				alert(data.info)
			}
		})
	});
	
	$('.sel_close').click(function(e) {
		e.stopPropagation();
		$(".shade").remove();
		$('.sel_suc').hide()
	});
	$('.sel_close2').click(function(e) {
		e.stopPropagation();
		window.history.go(-1)
	});
	var url = window.location.href;
	var lastDigits = url.substring(url.lastIndexOf('?') + 1).match(/[0-9]*$/)[0];

});