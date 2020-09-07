$(function(){
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
                'simpleupload', //单图上传
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
        autoHeightEnabled: false,
        autoFloatEnabled: true,
        initialFrameHeight:500
    });
    ue.ready(function() {
        ue.execCommand('serverparam', {
            '_token':$('meta[name="csrf-token"]').attr('content'),
        });
    });


   // 提交
    $(".btn-primary").click(function () {
        $.ajax({
            url:'/staff/BusinessManage/affiche',// 跳转到 action
            data:$(".myForm").serialize(),
            type:'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (res) {
                if(res.status == 1){
                    tipshow(res.info,'info');
                    location.reload()
                }else{
                    tipshow(res.info);
                }
            },
            error : function() {
                tipshow("异常！");
            }
        });
    })

})