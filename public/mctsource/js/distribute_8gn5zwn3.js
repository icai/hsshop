$(function(){
	var result = null;
	//获取分销模版
	$.get('/merchants/distribute/getTemplate?from=1',function(data){
		console.log(111)
		var html = '';
		var arr = '';
		var obj = data.data;
		for(var i=0;i<data.data.length;i++){
			arr = obj[i].created_at.split(" ");
			html += "<tr><td>"
						+ obj[i].title +
					"</td><td>"
						+ arr[0] + arr[1] +
					"<td" 
						+ " data_id=" + obj[i].id +
					" style='text-align: right;'><input type='button' class='btn btn-default btn-sm xuanqu' data-index='"+i+"' value='选取'></td>" +
					"</td></tr>";
		}
		$('.table-wrap tbody').append(html);
		$('.xuanqu').click(function(){
			var index = $(this).attr("data-index");
			result = obj[index]; 
		});
		
	})
	//关闭弹窗
	$(".header-close").click(function(){
		parent.layer.closeAll();
	});  
	$("#btn_ok").click(function(){  
//		获取url中的fn
		var url = window.location.href;
		var att = url.split("?");
		var att1 = att[1].split("=");
		var fn =att1[1];
		parent[fn](result);
	});
});
