$(function(){
	$(".accredit").click(function(){
		$.ajax({
			type: "GET",
			url: "/merchants/xcx/authorizer",
			data:"",
			async: true,
			success: function(res) {
				console.log(res)
//				$('.set').attr('href',res.data);
				window.open(res.data)
				$('.modal').modal('show');
				$(".in").show();
			},
			error:function(){
				alert("数据访问错误")
			}
		})			
	})
	
	
})
