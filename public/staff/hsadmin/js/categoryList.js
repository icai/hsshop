$(function(){
	//全选
	$(".allSel").click(function(){
		if ($(this).prop("checked")) {
			$(".table_body input[type='checkbox']").prop("checked", true)
		}else{
			$(".table_body input[type='checkbox']").prop("checked", false)
		}
	})
	//新增品类
	$(".addCategory").click(function(){
		layer.open({
		  	title: '新增品类',
		  	content: '<div class="addClassifyLayer">'+
						'&nbsp;&nbsp;&nbsp;品类名称： <input id="category_name" type="text" placeholder="请输入品类名称">'+
						'<br /><br />'+
						'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;排序： <input id="listorder" type="number" placeholder="排序" >'+
                        '<br /><br />'+
                        '是否属于其他：<input style="width:25px;" type="checkbox" id="is_other"/> '+
					'</div>',
		  	skin: "addClassify",
		  	btn: ["提交", "取消"],
		  	yes: function(){
		  		var category_name = $("#category_name").val();
		  		var listorder = $("#listorder").val();
                var is_other = $('#is_other').is(':checked');
                var parentID = 0;
                if (is_other) {
                	parentID = 8;
                }
		  		if(category_name!=""&&listorder!=""){
                    $.ajax({
                        url:'/staff/product/add',// 跳转到 action
                        data:'category_name='+category_name+'&listorder='+listorder+'&is_other='+is_other,
                        type:'post',
                        cache:false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType:'json',
                        success:function (response) {
                            if (response.status == 1){
                            	var _html = '  <ul class="table_body  flex-between"> ' +
									'<li><input type="checkbox" name="" value="" /></li>' +
									' <li>'+category_name+'</li> ' +
									'<li>'+listorder+'</li> ' +
									'<li> <a href="javascript:;" data-id="'+response.data+'" data-parent="'+parentID+'" class="modify">修改</a> <a href="javascript:;" data-id="'+response.data+'" class="del">删除</a> </li> ' +
									'</ul>';
                                $('.table_title').after(_html);

                                layer.msg('提交成功', {
                                    icon: 6,
                                    time: 2000
                                }, function(){
                                    layer.closeAll(); //关闭所有层
                                });
                            }else{
                                tipshow(response.info)
                            }
                        },
                        error : function() {
                            tipshow("异常");
                        }
                    });


		  		}else{
		  			layer.msg("须填不可空")
		  		}
		  	}
		}); 
	})
	//修改品类
	$(document).on("click", ".main_content .modify", function(){
		 var data =  $(this).parent().siblings()
		 var id = $(this).data('id');
		 var obj = $(this);

        //是否属于其他
        var checked = '';
        var parentID = $(this).data('parent');
        if (parentID == 8) {
            checked = 'checked';
        }

		layer.open({
		  	title: '修改品类',
		  	content: '<div class="addClassifyLayer">'+
						'&nbsp;&nbsp;&nbsp;填写品类： <input id="category_name" type="text" value="'+data[1].innerText+'" placeholder="请输入品类名称">'+
						'<br /><br />'+
						'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;排序：  <input id="listorder" type="number" value="'+data[2].innerText+'" placeholder="排序" >'+
                        '<br /><br />'+
                        '是否属于其他：<input style="width:25px;" type="checkbox" id="is_other" ' + checked + '/>'+
					'</div>',
		  	skin: "modifyClassify",
		  	btn: ["提交", "取消"],
		  	yes: function(){
		  		var category_name = $("#category_name").val();
		  		var listorder = $("#listorder").val();
                var is_other = $('#is_other').is(':checked');
		  		if(category_name!=""&&listorder!=""){

                    $.ajax({
                        url:'/staff/product/add',// 跳转到 action
                        data:'category_name='+category_name+'&listorder='+listorder+'&id='+id+'&is_other='+is_other,
                        type:'post',
                        cache:false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType:'json',
                        success:function (response) {
                            if (response.status == 1){
                            	obj.parent().parent().remove();
                                var _html = '  <ul class="table_body  flex-between"> ' +
                                    '<li><input type="checkbox" name="" value="" /></li>' +
                                    ' <li>'+category_name+'</li> ' +
                                    '<li>'+listorder+'</li> ' +
                                    '<li> <a href="javascript:;" data-id="'+id+'" data-parent="'+parentID+'" class="modify">修改</a> <a href="javascript:;" data-id="'+id+'" class="del">删除</a> </li> ' +
                                    '</ul>'
                                $('.table_title').after(_html);

                                layer.msg('提交成功', {
                                    icon: 6,
                                    time: 2000
                                }, function(){
                                    layer.closeAll(); //关闭所有层
                                });
                            }else{
                                tipshow(response.info)
                            }
                        },
                        error : function() {
                            // view("异常！");
                            tipshow("异常");
                        }
                    });

		  		}else{
		  			layer.msg("须填不可空")
		  		}
		  	}
		}); 
	});
	
	//删除
	$(document).on("click", ".main_content .del", function(evt){
        var id = $(this).data('id');
		clearEventBubble(evt);
		var delEle = $(this).parents(".table_body");
		var success = function(){
            $.ajax({
                url:'/staff/product/del/'+id,// 跳转到 action
                data:'',
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (response) {
                    if (response.status == 1){
                        delEle.remove();
                        tipshow("删除成功！", "info", 1000)
                    }else{
                        tipshow(response.info)
                    }
                },
                error : function() {
                    // view("异常！");
                    tipshow("异常");
                }
            });
		};
		showDelProver($(this), success,"你确定要删除吗？", true, 1, 7);
	});
})

/*阻止事件冒泡*/
function clearEventBubble(evt) {
     if (evt.stopPropagation) {
          evt.stopPropagation();   // 支持谷歌、火狐
     } else {
          evt.cancelBubble = true;// 支持IE
     }
     if (evt.preventDefault) {
          evt.preventDefault();//  阻止后面将要执行的浏览器默认动作.
     } else {
          evt.returnValue = false;
     }
}

