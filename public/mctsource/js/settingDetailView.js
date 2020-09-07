$(function(){
    $('.iblock input').click(function(){
        var val = $(this).val();
        if($(this).prop("checked")){
            if(val == 0){
                $('.wechat_news').prop('checked','checked');
            }else if(val == 1){
                $('.admin_news').prop('checked','checked');
            }
        }else{
            if(val == 0){
                $('.wechat_news').removeAttr('checked');
            }else if(val == 1){
                $('.admin_news').removeAttr('checked');
            }
        }
    })
    $('.btn-sm').click(function(){
        if($('.wechat_news').prop("checked") && $('.admin_news').prop("checked")){
            var subscriber_id_type = 2;
        }else if($('.wechat_news').prop("checked") && !$('.admin_news').prop("checked")){
            var subscriber_id_type = 0;
        }else if(!$('.wechat_news').prop("checked") && $('.admin_news').prop("checked")){
            var subscriber_id_type = 1;
        }else if(!$('.wechat_news').prop("checked") && !$('.admin_news').prop("checked")){
            var subscriber_id_type = -1;
        }
        console.log(subscriber_id_type);
        if(notification_type == 1 || notification_type == 2 || notification_type == 3 || notification_type == 4){
            if(!$('.admin_news').prop("checked")){
                alert('后台消息提醒属于必填项，请勾选')
                return false
            }
        }
        $.ajax({
            url: '/merchants/notification/settingDetailView',
            type: 'POST',
            data: {
                'id': id,
                'subscriber_id_type': subscriber_id_type,
                '_token':$('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function(res) {
                if (res.status == 1) {
                    tipshow(res.info);
                    setTimeout(function(){
                        window.location.href = '/merchants/notification/settingListView';
                    },1000);
                } else {
                    tipshow(res.info,'warn');
                }
            }
        })
    })
})