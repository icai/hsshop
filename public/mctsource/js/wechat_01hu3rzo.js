//按钮启用
$('.handle_title .btn1 button').on('click',function(){
	if ($('.handle_title .btn1 button').hasClass('active')) {
		$('.handle_title .btn1 button').removeClass('active');
		$('.ctl').show();
	} else{
		$('.handle_title .btn1 button').addClass('active');
		$('.ctl').hide();
	}
});
//实例化编辑器
var ue = UE.getEditor('editor', {
    toolbars: [
        ['link']
    ],
    elementPathEnabled:false,
    maximumWords:200,
    enableAutoSave: false,
    autoHeightEnabled: true,
    autoFloatEnabled: true
});