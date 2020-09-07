$(function(){
	var judge=window.location.search.slice(window.location.search.lastIndexOf("id"));

	if (judge==1) {
		//已填写会员资料，手机号和微信号禁止修改
		$("#weixin, #phoneNum").attr("disabled", "disabled")
	}
	
	//点击立即激活会员卡  按钮
	$(".js-activate-card").click(function(){
            $.get('/shop/member/save',$('#myform').serialize(),function(data){
                if(data.status == 1){
                    tool.tip(data.info);
                    setInterval(function(){
                        window.location.href = data.url;
                    },2000);
                }
                tool.tip(data.info);
            });
            return false;
	})
	
	//新增收货地址
	$(".js-edit-address").click(function(){
		tool.choose_address(addNewAdd)
		function addNewAdd(){
			tool.add_address(success);
			function success(){
				$('body').off("click", ".js-address-save");
			}
		}
	})
	
	//地址点击效果
	$(document).on("click", ".icon-check", function(){
		$(".icon-check").removeClass("icon-checked")
		$(this).addClass("icon-checked")
	})
	//点击地址编辑
	$(document).on("click", ".icon-circle-info", function(){
		$("#8gLHsKbT3b").remove();
		$("#BSV0Sv44Sr").remove();
		tool.add_address(success);
		function success(){
			$('body').off("click", ".js-address-save");
		}
	})

    var county = "<option value=''>选择地区</option>";
	/*省市区三级联动*/
    $('.js-province').change(function(){
        var dataId = $('.js-province option').not(function(){ return !this.selected }).val();
        var province = json[dataId];
        var city = "<option value=''>选择城市</option>";
        for(var i = 0;i < province.length;i ++){
            city += '<option value ="'+province[i]['id']+'"">'+province[i]['title']+'</option>';
        }
        $('.js-city').html(city);
        $('.js-county').html(county);
    });
    $('.js-city').change(function(){
        var dataId = $('.js-city option').not(function(){ return !this.selected }).val();
        var city = json[dataId];

        for(var i = 0;i < city.length;i ++){
            county += '<option value ="'+city[i]['id']+'"">'+city[i]['title']+'</option>';
        }
        $('.js-county').html(county);
    });
})