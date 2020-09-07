$(function(){
	var groupList_tem = "";
	var userIdArr = [], groupId="";
	$.get("/staff/seller/updateGroup", function(res){
		var groutList = res.data.data;
		groupList_tem = '<ul>';
		for(var i=0; i<groutList.length; i++){
			groupList_tem +=	'<li><input type="radio" name="chooseG" id="" value="" data-gid="'+groutList[i].id+'" />'+groutList[i].name+'</li>';
		}
		groupList_tem +='</ul>'
	})
	
	//全选
	$(".allSel").click(function(){
		userIdArr = []
		if ($(this).prop("checked")) {
			$(".table_body input[type='checkbox']").prop("checked", true)
			var ulList = $(".ulDiv.table_body");
			for(var j=0; j<ulList.length; j++){
				var userId = $(".getID")[j].dataset.id
				userIdArr.push(userId)
			}
		}else{
			$(".table_body input[type='checkbox']").prop("checked", false)
		}
	})
	//单选
	$(".signlChoose").click(function(){
		var _id = $(this).data('id');
		if ($(this).prop("checked")) {
			userIdArr.push(_id)
		}else{
			var idIndex = userIdArr.indexOf(_id)
			userIdArr.splice(idIndex, 1)
		}
	})
	
	//修改订单是否有效
	$(document).on("click",".main_content .changeOrder", function(){
		var obj =  $(this).parent();
		var id = obj.data('id');
	});
	
	//修改分组
	$(document).on("click", ".main_content .changeGroup", function(evt){
        var obj =  $(this).parent();
        var id = obj.data('id');
		clearEventBubble(evt);
		//避免使用复选框和点击‘改分组’的冲突
		userIdArr = [];
		$(".table_body input[type='checkbox']").prop("checked", false)
		
		userIdArr.push(id);
		$(".group_modal .modal-body").css({'max-height':"160px", "overflow-y":"auto"}).html(groupList_tem)
	});
	$(document).on("click", ".group_modal input[name='chooseG']", function(){
        groupId = $(this).data("gid")
	})
	//隐藏弹框时触发
	$('.group_modal').on('hidden.bs.modal', function (e) {
	  	userIdArr = [];
	})
	//保存提交分组
	$(".chooseGsave").click(function(){
		postGroup(userIdArr, groupId)
		$('.group_modal').modal('hide')
	})
	
	//批量修改分组
    $(".modify_group").click(function(){
        var formData  = $(".listForm").serialize();
        if( !formData ){
            tipshow("请先选择要修改的数据");
            return false;
        }
        $(".group_modal .modal-body").css({'max-height':"160px", "overflow-y":"auto"}).html(groupList_tem)
        $('.group_modal').modal('show')
    })
    //取消选择
    $(".chooseCancle").click(function(){
    	$(".groupsChoose").addClass("hide")
    })
    //保存选择
    $(".chooseSave").click(function(){
    	$(".groupsChoose").addClass("hide")
    	
    })
    
    //标记有效无效订单
    $(".changeOrder").click(function(){
    	var pId = $(this).parents("li").data("id")
    	modityOrder($(this), pId)
    })


	
	$(".getInfo").click(function () {
		var mid = $(this).data('mid');
        var load = layer.load();
		layer.open({
			type: 2,
			title: '参团信息',
			maxmin: true,
			shadeClose: true, //点击遮罩关闭层
			area : ['1000px' , '520px'],
			content: '/staff/seller/joinInfo?mid='+mid
		});
		layer.close(load);

    })
	
})


//修改有效、无效订单
function modityOrder(ele, id){
	var status = ele.data("status");
	
	$.post("/staff/seller/updateValid", {
		_token: $('meta[name="csrf-token"]').attr('content'),
		id   : id,
		valid: status==0 ? 0:1
	}, function(res){
		if(res.status==1){
			tipshow(res.info, "info");
			setTimeout(function(){
				location.reload()
			},1000)
		}
	})
}

//修改分组
function postGroup(userIdArr, groupId){
	if(!groupId) {
		tipshow("请选择分组");
		return false;
	}
	$.post("/staff/seller/updateGroup",{
		_token  : $('meta[name="csrf-token"]').attr('content'),
		sellerId: groupId,
		ids     : userIdArr
	}, function(res){
		if(res.status==1){
			userIdArr = [];
            groupId="";
			tipshow(res.info, 'info');
			setTimeout(function(){
				location.reload()
			},1000)
		}else{
			tipshow(res.info);
		}
		
	})
}

