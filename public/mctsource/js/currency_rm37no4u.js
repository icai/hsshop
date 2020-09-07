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
		$(this).attr("checked", false);
	  	$(ele).click(function(){
	  		$(this).attr("checked", true);
	  	})
	});


	//提交按钮；
	$(".content_center_2 button").click(function(){
		if ($("#inputTel").val() != "") {
			var accountNum = $("#inputTel").val();
			var limit = $("input[checked='checked']").val();
			var time = new Date();
			var year = time.getFullYear();
			var month = time.getMonth()+1;
			var day = time.getDate();
			var hour = time.getHours();
			var minute = time.getMinutes();
			var second = time.getSeconds();
			var nowTime = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+second;
			var addHtml = "<ul class='tatilMsg'><li>"+accountNum+"</li><li>"+accountNum+"</li><li>店铺</li><li>"+accountNum+"</li><li></li><li>"+accountNum+"</li><li>"+nowTime+"</li><li>"+limit+"</li><li><a href='##'>编辑</a>/<a href='##' class='liDele'>删除</a></li></ul>";
			$(".content_center").append(addHtml);
			$(".content_1").show();
			$(".content_2").hide();
			$(".errMsg").hide();
			$(".account .form-group").addClass("has-error");
		}else{
			$(".errMsg").show();
			$(".account .form-group").addClass("has-error");
		}
	})

	$(document).on("click", ".liDele", function(event){
		confirm("确定删除该帐号？");
		//event.stopPropagation();    //阻止冒泡事件
		if (confirm("确定删除该帐号？")) {
			$(this).parent().parent(".tatilMsg").remove();
		}else{
			return false;
		}
	})

})