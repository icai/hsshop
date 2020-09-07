$(function(){
	$('.copy_btn').click(function(){
        var obj = $(this).siblings('.link_copy');
        copyToClipboard( obj );
        layer.msg('链接复制成功',{
            skin: 'success_tip',
            offset: '40px',
            time:2000
        });
    });
    // 富文本编辑器
    var ue = UE.getEditor('container',{
        toolbars: [
            ['bold', //加粗
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
            'blockquote', //引用
            ],
            [
            'emotion', //表情
            'simpleupload', //单图上传
            'insertvideo', //视频
            'link', //超链接
            'removeformat', //清除格式
            'rowspacingtop', //段前距
            'rowspacingbottom', //段后距
            'lineheight', //行间距
            'paragraph', //段落格式
            'fontsize', //字号
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
        zIndex:2,
        initialFrameHeight:300,
        elementPathEnabled:false,
        maximumWords:1000,          // 最多输入的字符数
    });

    // 实时监听编辑器的动态变化 ，并把内容添加到左侧的显示区域
    ue.addListener( 'selectionchange', function( editor ) {
        var content = ue.getContent(); 
        $('.app_preview .group_content').html('').append('<div class="group_custom">'+content+'</div>') ;
     });

    // 编辑标题
    $('.app_edit .app_title').on('blur',function(){
        var _val = $(this).val();
        $('.app_preview .app_title').text( _val );
    });
})
/**
 * [copyToClipboard 复制到粘切板函数]
 * @param  {[type]} obj [ 要复制的对象 ]
 * @return {[type]}     [无]
 */
function copyToClipboard( obj ) {
    var aux = document.createElement("input");                  // 创建元素用于复制
    // 获取复制内容
    var content = obj.text() || obj.val();
    // 设置元素内容
    aux.setAttribute("value", content);
    // 将元素插入页面进行调用
    document.body.appendChild(aux);
    // 复制内容
    aux.select();
    // 将内容复制到剪贴板
    document.execCommand("copy");
    // 删除创建元素
    document.body.removeChild(aux);
}