'use strcit'
$(function(){
	$(".balance_detail").click(function(){
		var mid = $(this).parent().parent().attr("data-mid");
		var str = getBalanceInfo(mid);
		hstool.open({
			title:"余额收支明细",
			area:["600px","300px"],
			content:'<div style="height:300px;overflow:auto;">'+str+'</div>',
		});
	});

	function getBalanceInfo(mid){
		var result = "";
		hstool.load();
		$.ajax({
			url:"/merchants/member/getMemberBalaceLog",
			type:"get",
			data:{mid:mid},
			dataType:"json",
			async:false,
			headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
            },
			success:function(res){ 
				if(res.status==1){
					var list = res.data;
                    result='<table class="gridtable"><thead><tr><th style="width:150px;">时间</th><th style="width:150px;">类型</th><th>金额</th><th>余额</th><th style="width:150px;">描述</th></tr></thead><tbody>';
					for(var i=0;i<list.length;i++){
						result+='<tr><td>'+list[i].created_at+'</td><td>'+list[i].pay_way_name+'</td><td>'+list[i].type_name+list[i].money+'</td><td>'+list[i].money_total/100+'</td><td>'+list[i].pay_desc+'</td></tr>';
					}
					result +='</tbody></table>'; 
				}
			}
		}); 
		hstool.closeLoad();
		return result;
	}
	//初始化搜索框
	$(".chzn-select").chosen({
	    width:'150px',
	    no_results_text: "没有找到",
	    allow_single_de: true
	}).change(function(){
	    //$('form[name="storageForm"]').submit();
	});


});
