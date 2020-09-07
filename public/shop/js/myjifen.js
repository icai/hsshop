$(function(){
	$('.rule_tip').click(function(){
		$('#Xms3Sq4JR6').show();
        $('#jifenRule').show();
	})
	$('.js-cancel').click(function(){
        $('#Xms3Sq4JR6').hide();
        $('#jifenRule').hide();
    })
	var listPage = {
		pageNum:1,
		addData:function(){
			$.ajax({
				type:"get",
				data:{page:listPage.pageNum++},
				url:"/shop/point/selectPointRecord",
				dataType:"json",
				success:function(data){
					if(listPage.pageNum == 2){
						$('.points-usable').text(data.totalScore);
					}
					console.log(data);
					var html = '';
					$.each(data.data, function(inx,obj) {
						html += "<div class='list_cont'><div class='list_cont_chlid'><ul><li class='list_li1'>"
									+ obj.type_name +
								"</li><li class='list_li2'><span>"
									+ obj.created_at +
								"</span></li></ul><p class='col"
									+ obj.is_add +
								"'>"
									+ obj.score +
								"</p></div></div>"
								
					});
					$('#list_container').append(html)
				}
			});
		}
	}
	listPage.addData();
	$(document).scroll(function(){
		var clientHeight = $(window).height();
		var bodyScroll = $('body').scrollTop();
		var bodyHeight = $('body').height();
		if(bodyScroll + clientHeight >= bodyHeight){
			listPage.addData();
		}
	})
})
