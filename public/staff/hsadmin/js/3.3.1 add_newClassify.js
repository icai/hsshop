$(function(){
	var judge=window.location.search.slice(window.location.search.lastIndexOf("id"));
	if (judge==1) {
		$(".main_content .sorts a").eq(1).text("修改分类")
	}

    $("#one").on('change',function () {
        var id = $(this).val();
        if ( id ) {
            var secdata = categoryData[id];
            var sec = '<option value="">二级分类</option>';
            if (secdata){
                for ( var i = 0; i < secdata.length; i++ ) {
                    sec +='<option value="' + secdata[i]['id'] + '">' + secdata[i]['name'] + '</option>';
                }
            }
            $('#sec').html(sec);
        }else{
            $('#sec').html('<option value="">二级分类</option>');
        }
        $('#three').html('<option value="">三级分类</option>');
    })
	
	
	$("#sub").click(function () {

		var one = $('#one').val();
		var two = $('#sec').val();
		var request = new Array();
		var id = $('#id').val();
		if (two){
         	request['parent_id'] = two;
		}else if(one){
			request['parent_id'] = one;
		}
		request['type_path'] = one+','+two;
		request['name'] = $('#thirdClassify').val();
        var str = 'type_path='+request['type_path']+'&name='+request['name'];
        if (request['parent_id']){
            str = str+'&parent_id='+request['parent_id'];
        }
        if (id){
            str = str+'&id='+id;
        }
        $.ajax({
            url:'/staff/addInfoType',// 跳转到 action
            data:str,
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    window.location.reload();
                }else{
                   tipshow(response.info);
                }
            },
            error : function() {
                tipshow("异常！");
            }
        });
    })
})