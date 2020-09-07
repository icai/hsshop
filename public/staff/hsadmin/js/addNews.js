$(function(){
	var judge = window.location.search.slice(window.location.search.lastIndexOf("id"));
	if (judge == 1) {
		$(".first_nav li").removeClass("hover");
		$(".first_nav li").eq(1).addClass("hover");
		$(".content_top span").text("资讯管理-资讯列表");
		$(".main_content .sorts a").text("修改资讯");
		$(".addNews_list .sure").text("确认修改").removeClass("sure").addClass("modify")
	}
	
	//选择资讯分类
	newsClassify(".classify_1 p");
	newsClassify(".classify_2 p");
	newsClassify(".classify_3 p");
	
	//显示图片缩略图
	var urlVal;
    $(".filepath").on("change",function() {
        var srcs = getObjectURL(this.files[0]);   //获取路径
        urlVal = srcs;
        $(this).parents(".imgDiv").find("img").attr("src",srcs);    //this指的是input
        $(this).val("");    //必须制空
    });

    //富文本编辑区

    var ue = UE.getEditor('editor',{
    	initialContent:$('#content').html(),
    	toolbars: [
			['source', //源代码
				'undo', //撤销
				'redo', //重做
				'selectall', //清空内容
				'preview', //预览
				'print', //打印
				'bold', //加粗
				'italic', //斜体
				'underline', //下划线
				'strikethrough', //删除线
				'forecolor', //字体颜色
				'backcolor', //背景色
				'justifyleft', //居左对齐
				'justifycenter', //居中对齐
				'justifyright', //居右对齐
				'insertunorderedlist', //无序列表
				'insertorderedlist', //有序列表
				'rowspacingtop', //段前距
				'rowspacingbottom', //段后距
				'lineheight', //行间距
                'fontfamily',
            ],
			[
				'blockquote', //引用
				'emotion', //表情
				'insertimage', //多图上传
				'insertvideo', //视频
				'link', //超链接
				'removeformat', //清除格式
				'anchor', //插入锚点
				'paragraph', //段落格式
				'fontsize', //字号
				'insertcode', //插入代码啊
				'fontfamily',
			],
			[
				'inserttable', //插入表格
				'deletetable', //删除表格
				'insertparagraphbeforetable', //"表格前插入行"
				'insertrow', //前插入行
				'deleterow', //删除行
				'insertcol', //前插入列
				'deletecol', //删除列
				'mergecells', //合并多个单元格
				'mergeright', //右合并单元格
				'mergedown', //下合并单元格
				'splittocells', //完全拆分单元格
				'splittorows', //拆分成行
				'splittocols', //拆分成列
			]
		],
		zIndex: 2,
		wordCount: false,
		elementPathEnabled: false,
		maximumWords: 200,
		enableAutoSave: false,
		autoHeightEnabled: true,
		autoFloatEnabled: true,
        initialFrameHeight:500,
        topOffset:160,
    });
    ue.ready(function() {
        ue.execCommand('serverparam', {
            '_token':$('meta[name="csrf-token"]').attr('content'),
        });
    });
    
    //确认提交
    $(".sure").click(function(){
    	var selectEle = $(".classify_1 p").hasClass("choosed");
    	var title = $(".news_detail #title").val();
    	var subtitle = $(".news_detail #subtitle").val();
    	if (selectEle && title!="" && subtitle!="") {
    		window.location.href = "3.1.1 发布资讯.html"
    	}else{
    	}
    });
    //确认修改
    $(".modify").click(function(){
    	var selectEle = $(".classify_1 p").hasClass("choosed");
    	var title = $(".news_detail #title").val();
    	var subtitle = $(".news_detail #subtitle").val();
    	if (selectEle && title!="" && subtitle!="") {
    		window.location.href = "3.2 资讯列表.html";
    	}else{
    	}
    })



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

    $('#sec').on('change',function () {
        var id = $(this).val();
        if ( id ) {
            var threedata = categoryData[id];
            var three = '<option value="">三级分类</option>';
            if (threedata){
                for (var i = 0; i < threedata.length; i++) {
                    three +='<option value="' + threedata[i]['id'] + '">' + threedata[i]['name'] + '</option>';
                }
			}

            $('#three').html(three);
        } else {
            $('#three').html('<option value="">三级分类</option>');
        }
    })
    
	$("#sub").click(function () {
        $('#status').attr('value',1);
        var id = $("#one").val();
        var param = '';
        for(var i = 0;i < json.length;i ++){
            if(id == json[i].id){
                if(json[i].name == "帮助中心"){  
                    param = 'help';
                }else{
                    param = 'news';
                }
            }
        }
        $.ajax({
            url:'/staff/addInformation',// 跳转到 action
            data:$('#myForm').serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function (response) {
                if (response.status == 1){
                    tipshow("操作成功！", "info", 1000);
                    var url = '/staff/getInformation';
                    window.location.href=url;
                }else{
                    tipshow(response.info);
                }
            },
            error : function() {
                tipshow("异常")
            }
        });
    })

    $("#sub1").click(function () {
		$('#status').attr('value',2);
        $.ajax({
            url:'/staff/addInformation',// 跳转到 action
            data:$('#myForm').serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    tipshow("操作成功！", "info", 1000)
					$('#edit_id').attr('value',response.data);
					var url = '/home/index/detail/'+response.data;
					window.open(url);
                }else{
                    tipshow(response.info);
                }
            },
            error : function() {
                tipshow("异常")
            }
        });
    })
	
	$("body").on('click','.delImg',function () {
		var obj = $(this);
		for (var i=0;i<attachment.length;i++){
			if (attachment[i] == obj.data('id')){
				attachment.splice(i,1);
				obj.parent().remove();
			}
		}
        var test = attachment.join(',');
        $('#attachment').attr('value',test);

    })

})
function newsClassify(ele){
	$(ele).each(function(index){
		$(this).click(function(){
			$(ele).removeClass("choosed");
			$(this).addClass("choosed")
		})
	})
}
function getObjectURL(file) {
    var url = null;
    if (window.createObjectURL != undefined) {
        url = window.createObjectURL(file)
    } else if (window.URL != undefined) {
        url = window.URL.createObjectURL(file)
    } else if (window.webkitURL != undefined) {
        url = window.webkitURL.createObjectURL(file)
    }
    return url
};