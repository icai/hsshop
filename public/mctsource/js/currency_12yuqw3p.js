$(function(){
	//添加管理员页面；
	//帐号输入框的验证；
	$("#inputTel").blur(function(){
		var lstNum = $("#inputTel").val();
		var reg = /(^[1-9][2-4][0-9]{6}$)|(^1[1-9]{10}$)/;    //电话号码验证；          
		if (!reg.test(lstNum)) {
			$("#inputTel").val("");
			$(".errMsg").show();
			$(this).parent().addClass("has-error");
		}else{
			$(".errMsg").hide();
			$(this).parent().removeClass("has-error");
		}
	});
	$("#phone").blur(function(){
		flag_adminAdd = true;
	})
	//单选按钮的事件；
	function showMsg(clickEle, showEle){
		$(document).on("click", clickEle, function(){
			for (var i=0; i<$("._msg").length; i++) {
				$("._msg").eq(i).removeClass("showed");
			}
			$(showEle).addClass("showed");
			if ($(showEle).html() == "") {
				$(showEle).removeClass("showed");
			}
		})
	}
	$("#highUser").click(function(){showMsg("#highUser", ".highUserMsg");})
	$("#normalUser").click(function(){showMsg("#normalUser", ".normalUserMsg");})
	$("#servicePeople").click(function(){showMsg("#servicePeople", ".servePeopleMsg");});
	$("#checkPeople").click(function(){showMsg("#checkPeople", ".checkPeople");});
	
	//单选按钮的checked选择；
	$("input[type='radio']").each(function(index, ele){
	  	$(ele).click(function(){
	  		$(this).attr("checked", true);
	  		flag_adminAdd = true;
	  	})
	});


})