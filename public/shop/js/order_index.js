$(function(){
    
    // 取消订单
    $(document).on('click','.cancle_order',function(){
        var _this = $(this); 
        var oid = _this.data('kdtid');
        tool.confirm('订单还未付款，确定取消吗',function(){ 
            $.post("/shop/order/cancle/"+wid+"/"+oid,{'_token':$('meta[name="csrf-token"]').attr('content')},function(data){
            	console.log(data)
                if (data.status == 1) {
                    tool.tip("取消订单成功");
                    _this.parents('.js-block-order').find('.order-state-str').text('交易关闭');
                    _this.parents('.js-block-order').find('.bottom').empty();
                }else{
                    tool.tip(data.info);
                }
            })
        });
    });
    //下拉加载更多
    var str = '';
    var page='';
    if(page==""){
        page=1;
    }
    var stop=true;//触发开关，防止多次调用事件
    $(window).scroll( function(event){
        if ($(this).scrollTop() + $(window).height() + 100 >= $(document).height() && $(this).scrollTop() > 100) {
            if (stop == true) {
                stop = false;
                var wid = $("#wid").val();
                var status = $("#status").val();
                var url = "/shop/order/index/" + wid+'?status='+status;
                var _token = $('meta[name="csrf-token"]').attr('content');
                page = page + 1;//当前要加载的页码
                var parm = {'page': page, '_token':_token};
                $("#showlists").append("<li class='ajaxtips'><div style='font-size:2em'>Loding…..</div><>");
                $.post(url, parm, function (data) {
                    //console.log(data);
                    if (data.data == '') {
                        return;
                    }
                    var data = data.data;
                    for(var i = 0;i < data.length;i ++){
                        var status='';
                        if(data[i].status == 0){
                            status = '待付款';
                        }else if(data[i].status == 1){
                            status = '买家已付款';
                        }else if(data[i].status == 2){
                            status = '商家已发货';
                        }else if(data[i].status == 3){
                            status = '交易完成';
                        }else if(data[i].status == 4){
                            status = '交易关闭';
                        }
                        var status_html = '';
                        if (data[i].status == 0){
                            status_html='<a class="btn btn-default cancle_order" href="javascript:void(0);">取消</a><a class="js-extend-receive btn btn-default btn-in-order-list" href="/shop/order/detail/'+data[i].id+'" data-orderno="" data-kdtid="'+data[i].id+'">去付款</a>'
                        }else if(data[i].status == 1){
                            // status_html = '<a class="js-extend-receive btn btn-default btn-in-order-list refundApply" href="##" data-orderno="" data-kdtid="'+val.id+'">申请退款</a> '
                        } else if(data[i].status == 2) {
                            status_html = '<a class="logistics btn btn-default" href="/shop/order/getLogistics/"'+wid+'/'+data[i].oid+'>物流</a> ' +
                                          '<a class="btn btn-default btn-in-order-list receiveDelay" href="##" data-orderno="" data-kdtid="'+data[i].id+'">延长收货</a> ' +
                                          '<a class="js-confirm-receive btn btn-default btn-in-order-list received" href="#" data-orderno="" data-kdtid="'+data[i].id+'">确认收货</a>'
                        }else if(data[i].status == 3) {
                            if (data[i]['evaluate'] == 0){
                                if (data[i]['count'] == 1){
                                    status_html = '<a class="logistics btn btn-default" href="/shop/order/getLogistics/"'+wid+'/'+data[i].oid+'">物流</a><a class="js-confirm-receive btn btn-default btn-in-order-list" href=" /shop/order/comment/'+wid+'?odid='+data[i].orderDetail[0].id+'" data-orderno="" data-kdtid="">评价</a>'
                                }else{
                                    status_html = '<a class="logistics btn btn-default" href="/shop/order/getLogistics/"'+wid+'/'+data[i].oid+'">物流</a><a class="js-confirm-receive btn btn-default btn-in-order-list" href=" /shop/order/commentList/'+wid+'/'+data[i].id+'" data-orderno="" data-kdtid="">评价</a>'
                                }
                            }else{
                                status_html = '<a class="logistics btn btn-default" href="/shop/order/getLogistics/"'+wid+'/'+data[i].oid+'">物流</a><a class="js-confirm-receive btn btn-default btn-in-order-list" href="/shop/order/commentList/'+wid+'/'+data[i].id+'" data-orderno="" data-kdtid="">查看评价</a>'
                            }
                        }else if(data[i].status == 5 && data[i].refund_status==1) {
                            status_html = '<a class="js-confirm-receive btn btn-default btn-in-order-list refundDel" href="##" data-orderno="" data-kdtid="'+data[i].id+'">取消退款</a>'
                        }
                        var _html ='<li class="js-block-order block block-order animated">' +
                            '<div class="header"> ' +
                            '<div>' +
                            '<a href="/shop/index/"'+wid+'><span class="font-size-14">店铺：'+data[i].weixin.shop_name+'</span></a>' +
                            '<a class="order-state-str pull-right font-size-14" href="javascript:;">'+ status+'</a> ' +
                            '</div> ' +
                            '<div class="order-no font-size-12">订单编号：'+data[i].oid+'</div>' +
                            '</div>' +
                            '<a class="name-card name-card-3col clearfix" href=/shop/order/detail/'+data[i].id+" " + '>' +
                            '<div class="thumb"> <img src="'+$("#source").val()+data[i].orderDetail[0].img+'"> ' +
                            '</div> ' +
                            '<div class="detail"> ' +
                            '<h3 class="font-size-14 l2-ellipsis">'+data[i].orderDetail[0].title+'</h3> ' +
                            '<p class="sku-detail ellipsis js-toggle-more"> <span class="c-gray-darker">'+data[i].orderDetail[0].spec+'&nbsp;</span>' +
                            '</p> </div> ' +
                            '<div class="right-col"> ' +
                            '<div class="price c-black">￥<span>'+data[i].orderDetail[0].price+'</span></div> ' +
                            '<div class="num c-gray-darker">×<span class="num-txt c-gray-darker">'+data[i].orderDetail[0].num+'</span> </div> ' +
                            '</div> ' +
                            '</a> ' +
                            '<div class="bottom-price  has-bottom-btns"> ' +
                            '<div class="pull-right">合计： <span class="c-orange">￥'+data[i].pay_price+'</span> </div> ' +
                            '</div> ' +
                            '<div class="bottom"> ' +
                            '<div class="opt-btn pull-right">' +status_html+'</div> </div> </li>';

                        $('.js-list').append(_html);

                    }
                    stop = true;
                })
            }
        }
    });


    $('body').on('click','.refundDel',function () {
        var obj = $(this);
        
        var oid = obj.data('kdtid');
        $.ajax({
            url:'/shop/order/refundDel/'+wid+'/'+oid,// 跳转到 action
            data:{},
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    obj.html('申请退款')
                    obj.removeClass('refundDel');
                    obj.addClass('refundApply');
                    return false;
                }else{
                    tool.tip(response.info);
                    return false;
                }
            },
            error : function() {
                // view("异常！");
                tool.tip("异常！");
            }
        });

    })


    $('body').on('click','.refundApply',function () {
        var obj = $(this);
        var wid = $("#wid").val();
        var oid = obj.data('kdtid');
        $.ajax({
            url:'/shop/order/refundApply/'+wid+'/'+oid,// 跳转到 action
            data:{},
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    obj.html('取消退款')
                    obj.addClass('refundDel');
                    obj.removeClass('refundApply');
                    return false;
                }else{
                    tool.tip(response.info);
                    return false;
                }
            },
            error : function() {
                // view("异常！");
                tool.tip("异常！");
            }
        });

    })

    $('body').on('click','.received',function () {
        var obj = $(this);
        var wid = $("#wid").val();
        var oid = obj.data('kdtid');
        tool.notice(1,'确认收货','确认收货后，订单交易完成，钱款将立即到达商家账户。','确认收货',function(){
            $.ajax({
                url:'/shop/order/received/'+wid+'/'+oid,// 跳转到 action
                data:{},
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (response) {
                    if (response.status == 1){
                        tool.tip(response.info);
                        var src = '/shop/order/commentList/'+wid+'/'+oid;
                        obj.removeClass('received');
                        obj.html('评价');
                        obj.attr('href',src);
                        obj.siblings().remove();
                        return false;
                    }else{
                        tool.tip(response.info);
                        return false;
                    }
                },
                error : function() {
                    // view("异常！");
                    tool.tip("异常！");
                }
            });
        });

    })
    $('body').on('click','.receiveDelay',function () {
        //查看是否超过三天
        var obj = $(this);
        var wid = $("#wid").val();
        var oid = obj.data('kdtid');
        if($('.three_day').val() == 0){
           tool.notice(0,'延长收货时间','距离结束时间前三天才能申请哦。','我知道了')
            return false;
        }else{
            tool.notice(1,'延长收货时间','每笔订单只能延长一次收货时间，如需多次延长请联系商家','确定延长',success)
        }
        function success(){
            $.ajax({
                url:'/shop/order/receiveDelay/'+wid+'/'+oid,// 跳转到 action
                data:{},
                type:'post',
                cache:false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                success:function (response) {
                    if (response.status == 1){
                        tool.tip(response.info);
                        return false;
                    }else{
                        tool.tip(response.info);
                        return false;
                    }
                },
                error : function() {
                    // view("异常！");
                    tool.tip("异常！");
                }
            });
        }

    })


});