var tool = {};
tool.confirm = function(info,success,cancel){
    var html = '<div id="TPHbVnTMoH" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;"></div>';
        html += '<div id="v8Pmx5et6t" class="popout-confirm popout-box" style="overflow: hidden; position: absolute; z-index: 1000; transition: opacity 300ms ease; top: 50%; left: 50%; transform: translate3d(-50%, -50%, 0px); visibility: visible; border-radius: 4px; background: white; width: 270px; padding: 15px; opacity: 1;">';
        html += '<div class="confirm-content font-size-14" style="line-height: 20px; padding: 5px 5px 10px;">' + info + '</div><hr style="margin: 9px -15px 10px;">';
        html += '<div class="btn-2-1"><p class="js-cancel center font-size-16 js_confirm_cancel" style="padding-top: 5px;">取消</p></div><div class="btn-2-1"><p class="js-ok center c-green font-size-16 js_confirm_ok" style="padding-top: 5px;">确定</p></div></div>';
        if($('#TPHbVnTMoH') && $('#v8Pmx5et6t')){
            $('#TPHbVnTMoH').remove();
            $('#v8Pmx5et6t').remove();
        }
        $('body').append(html);
        $('.js_confirm_ok').click(function(){
            success();
            $('#TPHbVnTMoH').hide();
            $('#v8Pmx5et6t').hide();
        })
        $('.js_confirm_cancel').click(function(){
            $('#TPHbVnTMoH').hide();
            $('#v8Pmx5et6t').hide();
        })
}
tool.phone = function(success){
    var html = '';
        html += '<div id="0Vnog1babh" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; opacity: 1; transition: none 0.2s ease;"></div>';
        html += '<div id="M0drbS24ZP" class="popout-box" style="overflow: hidden; visibility: visible; display: block; opacity: 1; position: fixed; z-index: 1000; transition: opacity 300ms ease; top: 50%; left: 50%; transform: translate3d(-50%, -50%, 0px); border-radius: 4px; background: white; width: 270px; padding: 15px;">';
        html += '<form class="js-login-form popout-login" method="GET" action=""><div class="header c-green center">';
        html += '<h2>请填写您的手机号码</h2></div><fieldset class="wrapper-form font-size-14"><div class="form-item">';
        html += '<label for="phone">手机号</label><input id="phone" name="phone" type="tel" maxlength="11" autocomplete="off" placeholder="" value="">';
        html += '</div><div class="js-help-info font-size-12 error c-orange"></div></fieldset>';
        html += '<div class="action-container"><a class="js-confirm btn btn-green btn-block font-size-14 phone_confirm">确认手机号码</a></div></form></div>';
    if($('#0Vnog1babh') && $('#M0drbS24ZP')){
        $('#0Vnog1babh').remove();
        $('#M0drbS24ZP').remove();
    }
    $('body').append(html);
    $('.phone_confirm').click(function(){
        success();
        $('#0Vnog1babh').hide();
        $('#M0drbS24ZP').hide();
    })
}

//手机号注册弹框
tool.registered = function(success){
	var html ='<div id="0Vnog1babc" class="board" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; opacity: 1; transition: none 0.2s ease;"></div>';
		html +=	'<div id="qutwNYKeCd" class="popout-box" style="overflow: hidden; position: fixed; z-index: 1000; transition: opacity 300ms ease; top: 50%; left: 50%; transform: translate3d(-50%, -50%, 0px); visibility: visible; border-radius: 4px; width: 270px; padding: 15px; opacity: 1; background: white;">';
		html +=	'<form class="js-login-form popout-login" method="GET" action="">';
		html +=	'    <div class="header c-green center"><h2>注册会搜帐号</h2></div>';
		html +=	'   	<fieldset class="wrapper-form font-size-14">';
		html +=	'        <div class="form-item">';
		html +=	'            <label for="phone">手机号</label>';
		html +=	'            <input id="phone" name="phone" type="tel" maxlength="11" autocomplete="off" placeholder="请输入你的手机号" disabled="disabled" value="15855555555">';
		html +=	'        </div>';
		html +=	'        <div class="form-item js-image-verify hide">';
		html +=	'            <label for="verifycode">身份校验</label>';
		html +=	'            <input id="verifycode" name="verifycode" class="js-verify-code item-input" type="tel" style="width:178px" maxlength="6" autocomplete="off" placeholder="输入右侧数字">';
		html +=	'            <img class="js-verify-image verify-image" src="">';
		html +=	'        </div>';
		html +=	'        <div class="form-item">';
		html +=	'            <label for="code">验证码</label>';
		html +=	'            <input id="code" name="code" type="text" style="width:178px" maxlength="6" autocomplete="off" placeholder="输入短信验证码">';
		html +=	'            <button type="button" class="js-auth-code tag btn-auth-code tag-green font-size-12" data-text="获取验证码"> 获取验证码 </button>';
		html +=	'        </div>';
		html +=	'        <div class="form-item">';
		html +=	'            <label for="password">密码</label>';
		html +=	'            <input id="passsword" name="password" type="password" autocomplete="off" maxlength="20" placeholder="请输入8-20位数字和字母组合">';
		html +=	'        </div>';
		html +=	'        <div class="js-help-info font-size-12 error c-orange"></div>';
		html +=	'    </fieldset>';
		html +=	'    <div class="action-container">';
		html +=	'        <button type="button" class="js-confirm btn btn-green btn-block font-size-14">确认</button>';
		html +=	'    </div>';
		html +=	'    <div class="bottom-tips font-size-12">';
		html +=	'        <span class="c-orange">如果您忘了密码，请</span><a href="javascript:;" class="js-change-pwd c-blue">点此找回密码</a>';
		html +=	'        <a href="javascript:;" class="js-change-phone c-blue pull-right" data-id="1">更换手机号</a>';
		html +=	'    </div>';
		html +=	'</form>';
		html +=	'</div>';
        if($('#0Vnog1babc') && $('#qutwNYKeCd')){
            $('#0Vnog1babc').remove();
            $('#qutwNYKeCd').remove();
        }
    $('body').append(html);
    $('.js-confirm').click(function(){
        success();
    })
}

//找回帐号密码
tool.findPwd = function(success){
	var html ='<div id="0Vnog1btyc" class="board" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; opacity: 1; transition: none 0.2s ease;"></div>';
		html +=	'<div id="qutwNYtyCd" class="popout-box" style="overflow: hidden; position: fixed; z-index: 1000; transition: opacity 300ms ease; top: 50%; left: 50%; transform: translate3d(-50%, -50%, 0px); visibility: visible; border-radius: 4px; width: 270px; padding: 15px; opacity: 1; background: white;">';
		html +=	'<form class="js-login-form popout-login" method="GET" action="">';
		html +=	'    <div class="header c-green center"><h2>找回帐号密码</h2></div>';
		html +=	'   	<fieldset class="wrapper-form font-size-14">';
		html +=	'        <div class="form-item">';
		html +=	'            <label for="phone">手机号</label>';
		html +=	'            <input id="phone" name="phone" type="tel" maxlength="11" autocomplete="off" placeholder="请输入你的手机号" disabled="disabled" value="15855555555">';
		html +=	'        </div>';
		html +=	'        <div class="form-item js-image-verify hide">';
		html +=	'            <label for="verifycode">身份校验</label>';
		html +=	'            <input id="verifycode" name="verifycode" class="js-verify-code item-input" type="tel" style="width:178px" maxlength="6" autocomplete="off" placeholder="输入右侧数字">';
		html +=	'            <img class="js-verify-image verify-image" src="">';
		html +=	'        </div>';
		html +=	'        <div class="form-item">';
		html +=	'            <label for="code">验证码</label>';
		html +=	'            <input id="code" name="code" type="text" style="width:178px" maxlength="6" autocomplete="off" placeholder="输入短信验证码">';
		html +=	'            <button type="button" class="js-auth-code tag btn-auth-code tag-green font-size-12" data-text="获取验证码"> 获取验证码 </button>';
		html +=	'        </div>';
		html +=	'        <div class="form-item">';
		html +=	'            <label for="password">密码</label>';
		html +=	'            <input id="passsword" name="password" type="password" autocomplete="off" maxlength="20" placeholder="设置新的8-20位数字和字母组合">';
		html +=	'        </div>';
		html +=	'        <div class="js-help-info font-size-12 error c-orange"></div>';
		html +=	'    </fieldset>';
		html +=	'    <div class="action-container">';
		html +=	'        <button type="button" class="js-confirm btn btn-green btn-block font-size-14">确认</button>';
		html +=	'    </div>';
		html +=	'    <div class="bottom-tips font-size-12">';
		html +=	'        <span class="c-orange"></span><a href="javascript:;" class="js-change-pwd c-blue" data-id="2">已有帐号登录</a>';
		html +=	'        <a href="javascript:;" class="js-change-phone c-blue pull-right" data-id="3">注册新帐号</a>';
		html +=	'    </div>';
		html +=	'</form>';
		html +=	'</div>';
        if($('#0Vnog1btyc') && $('#qutwNYtyCd')){
            $('#0Vnog1btyc').remove();
            $('#qutwNYtyCd').remove();
        }
    $('body').append(html);
    $('.js-confirm').click(function(){
        success();
    })
}

tool.login = function(success){
    var html = '';
        html += '<div id="uggE9pVEmv" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;"></div>';
        html += '<div id="s4X11yDha3" class="popout-box" style="overflow: hidden; position: fixed; z-index: 1000; transition: opacity 300ms ease; top: 50%; left: 50%; transform: translate3d(-50%, -50%, 0px); visibility: visible; border-radius: 4px; background: white; width: 270px; padding: 15px; opacity: 1;">';
        html += '<form class="js-login-form popout-login" method="GET" action=""><div class="header c-green center">';
        html += '<h2>该号码注册过，请直接登录</h2></div><fieldset class="wrapper-form font-size-14"><div class="form-item">';
        html += '<label for="phone">手机号</label><input id="phone" name="phone" type="tel" maxlength="11" autocomplete="off" placeholder="请输入你的手机号" disabled="disabled" value="15236271883"></div>';
        html += '<div class="form-item"><label for="password">密码</label><input id="passsword" name="password" type="password" autocomplete="off" placeholder="请输入登录密码"></div><div class="js-help-info font-size-12 error c-orange"></div><div class="bottom-tips font-size-12">';
        html += '</fieldset><div class="action-container"><button type="button" class="js-confirm btn btn-green btn-block font-size-14 login_confirm">确认</button></div><div class="bottom-tips font-size-12">';
        html += '<span class="c-orange">如果您忘了密码，请</span><a href="javascript:;" class="js-change-pwd c-blue">点此找回密码</a><a href="javascript:;" class="js-change-phone c-blue pull-right">更换手机号</a></div>';
        html += '</form></div>';
        if($('#uggE9pVEmv') && $('#s4X11yDha3')){
            $('#uggE9pVEmv').remove();
            $('#s4X11yDha3').remove();
        }
    $('body').append(html);
    $('.login_confirm').click(function(){
        success();
    })
}
tool.tip = function(info){
    if(timer)return;
    var html = '<div class="motify" id="motify_tip"><div class="motify-inner">'+ info +'</div></div>';
    if($('#motify_tip')){
        $('#motify_tip').remove();
    }
    $('body').append(html);
    $('#motify_tip').show();
    var timer = setTimeout(function(){
        $('#motify_tip').remove();
    },'2000')
}
tool.add_address = function(success){
    var html = '';
        html += '<div id="7yBNPekNdX" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;"></div>';
        html += '<div id="GkLmo6UNYU" class="popup" style="overflow: hidden; position: fixed; z-index: 1000; left: 0px; right: 0px; bottom: 0px; background: white; visibility: visible; transform: translate3d(0px, 0px, 0px); transition: all 300ms ease; opacity: 1;">';
        html += '<form class="js-address-fm address-ui address-fm"><h4 class="address-fm-title">新增收货地址</h4>';
        html += '<div class="js-address-cancel cancel-img"></div><div class="block form" style="margin:0;"><div class="block-item no-top-border">';
        html += '<label>收货人</label><input type="text" name="user_name" value="" placeholder="名字" class="c-red" style="color: red;">';
        html += '</div><div class="block-item"><label>联系电话</label><input type="tel" name="tel" value="" placeholder="手机或固定电话"></div><div class="block-item"><label>选择地区</label>';
        html += '<div class="js-area-select area-layout"><span>'
        html += '<select class="js-province address-province"><option value="-1">选择省份</option><option value="110000">北京市</option></select>';
        html += '</span><span><select class="js-city address-city"><option value="-1">选择城市</option></select></span>';
        html += '<span><select class="js-county address-county"><option value="-1">选择地区</option></select></span>';
        html += '</div></div> <div class="block-item"><label>详细地址</label><div class="address-detail-wrap "> <textarea type="text" class="js-address-detail address-detail" name="address_detail" placeholder="如街道，楼层，门牌号等" rows="1"></textarea>';
        html += '<i class="cancel-input-icon js-cancel-input hide"></i><i class="cancel-input-icon-trigger js-cancel-input hide"></i><div class="address-prompt js-address-prompt"></div>';
        html += '</div></div><div class="block-item"><label>邮政编码</label><input type="tel" maxlength="6" name="postal_code" value="" placeholder="邮政编码(选填)"></div></div>';
        html += '<div class="action-container"><a class="js-address-save btn btn-block btn-green">保存</a></div></form></div>';
    if($('#7yBNPekNdX') && $('#GkLmo6UNYU')){
        $('#7yBNPekNdX').remove();
        $('#GkLmo6UNYU').remove();
    }
    $('body').on('click','.js-address-cancel',function(){
    	tool.confirm("确定放弃此次编辑吗？", success)
    	function success(){
	        $('#7yBNPekNdX').remove();
	        $('#GkLmo6UNYU').remove();
    	}
    })
    $("body").on('click','.js-address-save',function(){
        success();
        $('#7yBNPekNdX').remove();
        $('#GkLmo6UNYU').remove();
        //选择收货地址的隐藏
    	$("#8gLHsKbT3b").remove();
		$("#BSV0Sv44Sr").remove();
    })
    $('body').append(html);
}

//选择收货地址
tool.choose_address = function(addNewAdd){
	var	html = '<div id="8gLHsKbT3b" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;"></div>';
		html +='<div id="BSV0Sv44Sr" class="popup" style="overflow: hidden; position: fixed; z-index: 1000; left: 0px; right: 0px; bottom: 0px; background: white; visibility: visible; transform: translate3d(0px, 0px, 0px); transition: all 300ms ease; opacity: 1;">';
		html +='	<div class="js-scene-address-list">';
		html +='		<div class="address-ui address-list">';
		html +='			<h4 class="address-title">选择收货地址</h4>';
		html +='			<div class="cancel-img js-cancel"></div>';
		html +='			<div class="js-address-container address-container block block-list border-top-0">';
		html +='				<div id="js-address-item-56635751" class="js-address-item block-item ">';
		html +='					<div style="padding-left:40px">';
	    html +='						<div class="icon-check icon-checked"></div>';
	    html +='						<p>';
		html +='				        	<span class="address-name" style="margin-right: 5px;">yu</span>';
	    html +='							<span class="address-tel">18937107705</span>';
	    html +='						</p>';
	    html +='						<span class="address-str address-str-sf">收货地址：浙江省杭州市江干区九堡镇九和路17号 九和商务楼</span>';
	    html +='						<div class="address-opt  js-edit-address ">';
	    html +='							<i class="icon-circle-info"></i>';
	    html +='						</div>';
		html +='					</div>';
		html +='				</div>';
		html +='				<div id="js-address-item-56635751" class="js-address-item block-item ">';
		html +='					<div style="padding-left:40px">';
	    html +='						<div class="icon-check"></div>';
	    html +='						<p>';
		html +='				        	<span class="address-name" style="margin-right: 5px;">yu</span>';
	    html +='							<span class="address-tel">18937107705</span>';
	    html +='						</p>';
	    html +='						<span class="address-str address-str-sf">收货地址：浙江省杭州市江干区九堡镇九和路17号 九和商务楼</span>';
	    html +='						<div class="address-opt  js-edit-address ">';
	    html +='							<i class="icon-circle-info"></i>';
	    html +='						</div>';
		html +='					</div>';
		html +='				</div>';
		html +='			</div>';
		html +='			<div class="add-address js-add-address">';
		html +='				<span class="icon-add"></span>';
		html +='				<a class="" href="javascript:;">新增地址</a>';
		html +='				<span class="icon-arrow-right"></span>';
		html +='			</div>';
		html +='		</div>';
		html +='	</div>';
		html +='</div>';
	$('body').append(html);
	$('body').on("click", ".js-cancel", function(){
		$("#8gLHsKbT3b").remove();
		$("#BSV0Sv44Sr").remove();
	})
	$("body").on("click", ".js-add-address", function(){
		addNewAdd()
	})
}
       


// 自定义弹窗
tool.custom = function(title,content,customCancelname, sureBtn, cancelHrefUrl, sureHrefUrl){
    // if(typeof(customCancelname) != "string" || customCancelname == ""){
        customCancelname = customCancelname || "取消";
        sureBtn = sureBtn || "确定";
        cancelHrefUrl = cancelHrefUrl || "##";
        sureHrefUrl = sureHrefUrl || "##";
    // }
    var html = "";
    html += '<div id="mask" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;"></div>';
    html += '<div id="content" class="popout-confirm popout-box" style="overflow: hidden; position: fixed; z-index: 1000; transition: opacity 300ms ease; top: calc(50% - 40px); left: 50%; transform: translate3d(-50%, -50%, 0px); visibility: visible; border-radius: 4px; background: white; width: 270px;opacity: 1;">';
    html += '<div class="center" style="padding: 15px 15px 5px 10px">' + title + '</div>';
    html += '<div class="confirm-content font-size-14 c-gray-dark" style="line-height: 20px; padding: 15px;">' +  content + '</div>'
    html += '<div class="btn-1" style="border-top: 1px solid #d5d5d5;padding: 10px;">';
    html += '<p class="js-cancel center font-size-16 js_confirm_cancel" style="padding-top: 5px;display: inline-block;float: left;width: 50%">' + customCancelname + '</p>';
    html += '<p class="js-ok center c-green font-size-16 js_confirm_ok" style="padding-top: 5px;display: inline-block;width: 50%">'+sureBtn+'</p>';
    html += '</div></div>';   
    $('body').append(html);
    $("#mask").on("click",function(){
        $('#mask').remove();
        $('#content').remove();
    })
    $(".js-cancel").on("click",function(){
        $('#mask').remove();
        $('#content').remove();
        location.href = cancelHrefUrl;
    });
    $(".js-ok").on("click",function(){
    	$('#mask').remove();
        $('#content').remove();
        window.location.href = sureHrefUrl
    })
};

//遮罩及二维码弹窗
tool.qrCode = function(){
	var html = '<div id="rnHzDb7ym0" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; transition: none 0.2s ease; opacity: 1;"></div>';
		html +='<div id="dJOOkY4yOt" class="" style="overflow: hidden; position: absolute; z-index: 1000; transition: opacity 300ms ease; top: 50%; left: 50%; transform: translate3d(-50%, -50%, 0px); width: 90%; visibility: visible; opacity: 1;"><div class="member-popout">';
		html +='	<div class="popout-header" style="background-color:#55bd47;">';
		html +='	<div class="clearfix">';
		html +='		<h3 class="title"><span class="logo" style="background-image:url(https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/8f9c442de8666f82abaf7dd71574e997.png?imageView2/2/w/60/h/60/q/75/format/webp)"></span>汇好</h3>';
		html +='		<button class="close js-close">×</button>';
		html +='	</div>';
		html +='	<div class="clearfix card-region-popout">';
		html +='		<h2 class="card-name">福建阿萨德会计法</h2>';
		html +='		<h4 class="card-discount"></h4>';
		html +='	</div>';
		html +='</div>';
		html +='<div class="popout-main">';
		html +='	<div class="qrcode">';
		html +='		<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAQ8AAAEPAQMAAABm4dN9AAAABlBMVEUAAAD///+l2Z/dAAABT0lEQVRoge3YMRaCMAwG4PAcHD0CR+FoeDSOwhEcHXzUJmlrqgWGVnT4M9X6wUBekwC5vVgIBORPyY1iDH5nOjm6PNLWBeQ/iObrIcQHk7QEqSYTP2q/z0tNwMBpOa0SuSHIl8hmAkwaQdqThfo7gfySEJVJKmRCRlvTQGpJjCEst/r0q0qBHEdMxE5iA6SKhGKjIQmgNJGCHE3SYWDCBSsrZHqJ7yTj3MkPkCaEY5xTD5Bl7Bh6jpZ3EppKH24M0pLoCQgDUSKmevlZirp4IUgt0RJkBqXPBKzMUvaQgLQg5pDEpVtp5SDNyNt7QDaGrhPnrn7Vy51BWhNNALfswiGJaaSz45oG0oBMlH9/YBI+XIAcTfIoftLTYfZKlM+7INXkpnVnlmevuxvkLGcLpC15RU5sIeN/O/OSB1JJYgylll0k6WUZ5BCyEyAgPyBPQAacuSJLbPcAAAAASUVORK5CYII=">';
		html +='	</div>';
		html +='	<div class="barcode">';
		html +='		<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAQwAAABGCAIAAAC2Wj16AAAA50lEQVR42u3TQQrEIAxA0dj739kuCkUIkdhFV++thoxY68wfsZhzRsQY4/38eCZ55f7bdZ4nnXneP8+rHfZvtN+hf/7qKdWZOzeW13dOm9dUt9d/yunve3rOf/5R3+5/XXMFsCUSEAmIBEQCIgGRgEhAJCASQCQgEhAJiAREAiIBkYBIAJGASEAkIBIQCYgERAIiAUQCIgGRgEhAJCASEAmIBEQCiAREAiIBkYBIQCQgEhAJIBIQCYgERAIiAZGASEAkgEhAJCASEAmIBEQCIgGRgEgAkYBIQCQgEhAJiAREAiIBRAInbscobIyrrF5QAAAAAElFTkSuQmCC">';
		html +='		<h3 class="code">2329 9889 4367 7116 12</h3>';
		html +='	</div>';
		html +='	<div class="term-popout">有效期：无限期</div>';
		html +='</div>';
		html +='<p class="tip">可截图保存至相册</p>';
		html +='</div></div>';
	$('body').append(html);
	
	$("#rnHzDb7ym0, .js-close").on("click",function(){
        $('#rnHzDb7ym0').remove();
        $('#dJOOkY4yOt').remove();
    })
}

//点击客服电话出现选项弹框
tool.customPhone = function(){
    var html = '';
        html += '<div id="0Vnog1tybh" style="height: 100%; position: fixed; top: 0px; left: 0px; right: 0px; background-color: rgba(0, 0, 0, 0.701961); z-index: 1000; opacity: 1; transition: none 0.2s ease;"></div>';
        html += '<div id="M0drb664ZP" class="popout-box" style="overflow: hidden; visibility: visible; display: block; opacity: 1; position: fixed; z-index: 1000; transition: opacity 300ms ease; top: 50%; left: 50%; transform: translate3d(-50%, -50%, 0px); border-radius: 4px; background: white; width: 270px; padding: 15px;">';
        html += '<a class="call" href="tel:158888888888" style="display:block; padding-bottom: 15px; font-size: 18px;">呼叫</a>';
		html += '<p class="addToList" style="padding-top: 15px; font-size: 18px; border-top: 1px solid #ccc;">添加到手机通讯录</p>';
        html += '</div>';
    $('body').append(html);
    
    $("#0Vnog1tybh").on("click",function(){
        $('#0Vnog1tybh').remove();
        $('#M0drb664ZP').remove();
    })
    $(".addToList").click(function(){
    	console.log("添加至通讯录")
    })
}

//tool.custom("关闭退款申请","如您主动关闭正在处理的退款后，您无法再次发起退款申请，请务必谨慎操作。","关闭退款")
        
            
            
        
        
            
            
        
        
            
            
                
                    
                
                
                    
                
                
                    
                
            
        
       
            
            
               
                
                    
                    
                
                
            
        
        
            
            
        
    
    
        
        
    



    



    

    
