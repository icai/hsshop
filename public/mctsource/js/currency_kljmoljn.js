$(function(){
	judge();
	var length =$(".dataDiv .dataDiv_content").length;
	$(".dataDiv span an").text(length)
	//删除
	$(document).on("click", ".dataDiv_content .del", function(){
		//计数
		var n = $(".dataDiv .dataDiv_content").length;
		var delEle = $(this).parents(".dataDiv_content");
		var id = $(this).attr('data');
		//确定事件；
		var successCallback = function(){
		 	delEle.remove();
			judge();
			n --;
			$(".dataDiv span an").text(n);
			delPartner(id);
		}
		showDelProver($(this),successCallback, null,1,0,0);
	});
	
	
	function judge(){
		if ($(".dataDiv .dataDiv_content").length<=0) {
			$(".content .dataDiv").html("还没有相关数据");
			$(".content .dataDiv").css({"textAlign": "center", "lineHeight": "100px", "border": "1px solid #e8e8e8"})
		}
	}
	
	function delPartner(id) {
        url = '/merchants/currency/partnerDel';
        datas = {
            //'id': $(this).attr('data'),
			'id': id,
            '_token': $("meta[name='csrf-token']").attr('content')
        };
        $.post(url, datas, function (data) {
            if (data.status == 1) {
                tipshow(data.info);
				/* 后台验证通过 */
                if (data.url) {
					/* 后台返回跳转地址则跳转页面 */
                  //  window.location.href = data.url;
                } else {
					/* 后台没有返回跳转地址 */
                    // to do somethings
                }
            } else {
                tipshow(data.info);
				/* 后台验证不通过 */
                $('input[type="submit"]').prop('disabled', false);
                // to do somethings
            }
        }, 'json');
        return false;
    }
})