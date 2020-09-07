require(["chosen", "layer", "echarts"], function(chosen, layer, echarts) {
    $('.grant-type-tip').mouseover(function() {
        var position = $(this).offset();
        $('#delete_prover').show();
        $('#delete_prover').css({
            top: position.top - $('#delete_prover').height() / 2 + $(this).height() / 2,
            left: position.left + $(this).width() + 10
        })
    })
    $('.grant-type-tip').mouseout(function() {
        $('#delete_prover').hide();
    })
    // $('.grant-type-title').popover('show')
    // 搜索
    $('#search').keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            var url = window.location.href
            var arr = url.split('?');
            var search_url = arr[0];
            window.location.href = search_url + '?card_no=' + $(this).val();
        }
    });
    // 鼠标移入权益框
    $('.js-rights-area').mouseover(function() {
        $(this).children('.member_equity_prover').show();
    })
    // 鼠标移出权益框
    $('.js-rights-area').mouseout(function() {
        $(this).children('.member_equity_prover').hide();
    })
    // 删除确定
    $(document).on('click', '.sure_btn', function() {
        hideDelProver(); //隐藏删除prover
    })
    // 删除确定
    $(document).on('click', '.cancel_btn', function() {
        hideDelProver(); //隐藏删除prover
    })
    //展开收缩点击
    $('.js-hide-list').click(function() {
        if ($(this).hasClass('retract')) {
            $(this).removeClass('retract');
            $(this).parent().parent().children('.grant-type-region').show(500);
            $(this).html('收起');
        } else {
            $(this).addClass('retract');
            $(this).parent().parent().children('.grant-type-region').hide(500);
            $(this).html('展开');
        }
    })
    // 发卡点击
    $('.js-dispense-card').click(function(e) {
        var position = $(this).offset();
        var cardId = $(this).data('bind');
        var id = $(this).data('id');
        var _token = $('meta[name="csrf-token"]').attr('content');
        var that = $(this);
        $.get('/merchants/member/memberCard/putCard', { card_id: cardId, id: id }, function(data) {
            var data = JSON.parse(data);
            var url = data.show_qrcode_url;
            $('.form-control').eq(0).val(url);
            $('.input-append').eq(0).empty().append(data.qrcodeStr);
        });
        // 获取小程序二维码
        $.post('/merchants/member/putXcxMemberCard', { card_id: id, _token:_token }, function(data) {
            
            if(data.status == 0){
                $('.tab').parent().remove();
            }else{
                $('.input-append').eq(1).find('img').attr('src','data:image/png;base64,'+ data.data.code);
            }
            $('#give_card').show();
            $('.down_qrcode').attr('data-id', id);
            $('#give_card').css({
                top: position.top + that.height(),
                left: position.left - $('#give_card').width() / 2 + that.width() / 2
            })
            e.stopPropagation();
        });
    })
    // 发卡弹窗小程序，和微商城切换
    $('.wsc-tab').click(function(e){
        var index = $(this).index();
        $('.wsc-tab').removeClass('active');
        $(this).addClass('active');
        $('.qrcode').eq(index).removeClass('hide');
        $('.qrcode').eq(index).siblings('.qrcode').addClass('hide');
        e.stopPropagation();
    })
   
    $('.delete').click(function() {
        showDelProver($(this)); //显示删除按钮
    })

    // 消失发卡弹窗
    $(document).click(function(){
        $('#give_card').hide();
    })
    // 复制链接
    $('.js-btn-copy').click(function(e) {
        e.stopPropagation(); //组织事件冒泡
        var obj = $(this).siblings('input');
        copyToClipboard(obj);
        tipshow('复制成功', 'info');
        $('#give_card').hide();
    });
    //复制内容的方法；
    function copyToClipboard(obj) {
        var aux = document.createElement("input"); // 创建元素用于复制
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
})