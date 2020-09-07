$(function(){
    // 备注
    $('.action').click(function(){
        show();
    })
    // 备注弹窗关闭
    $('.close').click(function(){
        hide();
    })
    // 备注提交
    $('.submit_info').click(function(){
        var text = $(this).parents('.modal-content').find('textarea').val();
        if(text==""){
            tipshow('请填写商家备注！','wran');
            return;
        }
        $.post('/merchants/order/setSellerRemark',$('#seller_remark_form').serialize(),function(data){
            if(data.status == 1){
                $('.info_detail').text(text);
                hide();
            }else{
                tipshow(data.info,'warn');
            }
        });
    })
    var hide = function(){
        $('#baseModal').hide();
        $('.modal-backdrop').hide();
    }
    var show = function(){
        $('#baseModal').show();
        $('.modal-backdrop').show();
    }

    //结单操作
    $('.finish-btn-order').click(function(){
        var oid = $(this).attr("data-id");
        hstool.open({
            title:"结单操作",
            btn:["提交"],
            footAlign:"right",
            content:'<p style="padding:0 20px;"><input id="txt_single_node" class="form-control" placeholder="请输入核销号" maxlength="50" /></p>',
            btn1:function(){
                var content = $("#txt_single_node").val();
                $.ajax({
                    url:"/merchants/order/finishOrder",
                    type:"post",
                    data:{"orderId":oid,"content":content},
                    dataType:"json", 
                    headers: {
                        'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                    },
                    success:function(res){
                        if(res.errCode==0){
                            tipshow("结单成功");
                            hstool.close();
                            setTimeout(function(){
                                location.reload();
                            },1000);
                        }else{
                            tipshow(res.errMsg,"wran");
                        }
                    }
                }) 
            }
        });
    });


})
