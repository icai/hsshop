!function(){
    $('.pub_key').mouseenter(function(e){
        e.stopPropagation();
        e.preventDefault();
        layer.tips($(this).text(), $(this), {
            tips: 3
        });
        var a ={ali_rsa_pub_key:'',
        id:''}
    })
    $('.update_key').click(function(e){
        var content = $.trim($(this).parents('tr').find('.pub_key').text());
        var obj = {}
        content == ''?obj.title = '添加公钥':obj.title = '修改公钥';
        obj.content = content;
        obj.id = $(this).parents('tr').attr('data-id')
        pop(obj)
    })

    function pop(pa){
        layer.open({
            type: 1,
            title:[pa.title,'font-size:15px;line-height:48px;height:48px;background:white;font-weight:bold'],
            closeBtn:true, 
            btn:['确认'],
            shadeClose:true,
            yes:function(index, layero){
                getKey({id:pa.id,ali_rsa_pub_key:$('textarea[name="pub_key"]').val()})
            },
            move: false, //不允许拖动 
            area: ['600px', '400px'], //宽高
            content: '<div id="pop">\
            <p><span class="red">注：</span>小程序公钥获取，需要你登录企业支付宝开发者中心-小程序-查看-设置-应用网关，然后下载生成的支付宝公钥，复制填写</p>\
            <div class="clearfix">\
            <span class="pull-left">小程序公钥：</span>\
            <textarea name="pub_key" cols="62" rows="7">'+ pa.content +'</textarea>\
            </div></div>'
        }); 
    }
}()



function getKey(params){
    $.ajax({
        url:'/merchants/marketing/alixcx/list',
        data:params,
        type:'post',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(res){
            if(res.status == 1){
                tipshow('修改成功')
                location.reload()
            }
        }
    })
}