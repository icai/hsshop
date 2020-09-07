$(function(){
    // 头部全选点击
    $('.js-select-group').click(function(){
        checkAll('.bottom-cart .select-all'); //底部有原先代码
    })
    // 底部全选点击
    $('.bottom-cart .select-all').click(function(){
        checkAll('.bottom-cart .select-all');
    })
    var shopData = data
    // 全选
    function checkAll(dom){
        var price = 0;
        var num = 0;
        if($(dom).children('span').hasClass('checked')){
            $(dom).children('span').removeClass('checked');
            $(dom).children('span').removeClass('checked');
            $('.js-select-group .check').removeClass('checked');
            $('.check-container .check').removeClass('checked');
            $('.total-price').addClass('c-gray-dark').removeClass('c-red-text');
            $('.js-total-price').css('color','rgb(153, 153, 153)');
            $('.js-go-pay').attr('disabled','disabled');
            $('.js-total-price').html('0');
            $('.js-total-price').html(price);
            $(".cart_total_num").addClass('hide')
            if($('.bottom-cart').hasClass('edit')){
                $('.j-delete-goods').attr('disabled','disabled');
            }
        }else{
            $(dom).children('span').addClass('checked');
            $(dom).children('span').addClass('checked');
            $('.js-select-group .check').addClass('checked');
            $('.check-container .check').addClass('checked');
            $('.price').each(function(){
                if($(this).parents('li').find('.cart_check_span').find('span').hasClass('checked')){
                    num ++;
                    price = price + $(this).children('span').text() * $(this).prev('.num').find('.num-txt').text();
                }
            })
            price = price.toFixed(2)
            $(".cart_total_num").removeClass('hide').children('i').html(num)
            $('.js-total-price').html(price);
            $('.total-price').removeClass('c-gray-dark').addClass('c-red-text');
            $('.js-total-price').css('color','#FF3232');
            $('.js-go-pay').removeAttr('disabled');
            if($('.bottom-cart').hasClass('edit')){
                $('.j-delete-goods').removeAttr('disabled','disabled');
            }
        }
    }
    // 中间选择框点击
    $('.container').on('click','.check-container',function(){
        var flag = false;
        var num = 0;
        if($(this).children('.check').hasClass('checked')){
            $(this).children('.check').removeClass('checked');
            $(".js-select-group span,.select-all span").removeClass('checked');
            $('.js-total-price').html(parseFloat(parseFloat($('.js-total-price').text(),2) - $(this).next().find('.price span').text() * $(this).next().find('.num-txt').text()).toFixed(2));
            $(this).children('.check').removeClass('checked');
            $('.check-container').each(function(){
                if($(this).children('.check').hasClass('checked')){
                    flag = true;
                }
            })
            if(!flag){
                $('.total-price').addClass('c-gray-dark').removeClass('c-red-text');
                $('.js-total-price').css('color','rgb(153, 153, 153)');
                $('.js-go-pay').attr('disabled','disabled');
                if($('.bottom-cart').hasClass('edit')){
                    $('.j-delete-goods').attr('disabled','disabled');
                }
            }
            $('.price').each(function(){
                if($(this).parents('li').find('.cart_check_span').find('span').hasClass('checked')){
                    num ++;
                }
            })
            if(num == 0){
                $(".cart_total_num").addClass('hide')
            }else{
                $(".cart_total_num").removeClass('hide').children('i').html(num)
            }
        }else{
            $('.js-total-price').html(parseFloat(parseFloat($('.js-total-price').text(),2) + $(this).next().find('.price span').text() * $(this).next().find('.num-txt').text(),2).toFixed(2));
            $(this).children('.check').addClass('checked');
            $('.total-price').removeClass('c-gray-dark').addClass('c-red-text');
            $('.js-total-price').css('color','#FF3232');
            $('.js-go-pay').removeAttr('disabled');
            if($('.bottom-cart').hasClass('edit')){
                $('.j-delete-goods').removeAttr('disabled','disabled');
            }
            $('.price').each(function(){
                if($(this).parents('li').find('.cart_check_span').find('span').hasClass('checked')){
                    num ++;
                }
            })
            $(".cart_total_num").removeClass('hide').children('i').html(num)
        }
    })
    // 编辑点击
    $('.j-edit-list').click(function(){
        var btn = ''
        if(trim($(this).text()) == '编辑'){
            $('.js-go-pay').hide();
            $('.j-delete-goods').show();
            $(".cart_total_price").hide()
            $(this).parent().next().find('li').each(function(key,val){
                $(val).find('.cart_num_price').children('.num').hide()
                $(val).find('.delete-btn').css('right','-580px')
                $(val).find('.cart_shop_detail').css('padding-right','1.4rem')
            })
            $('.bottom-cart').addClass('edit');
            $('.check-container').each(function(){
                if($(this).children('.check').hasClass('checked')){
                    $('.j-delete-goods').removeAttr('disabled');
                }
            })
            $(this).text('完成');
        }else{
            $('.js-go-pay').show();
            $('.j-delete-goods').hide();
            $(".cart_total_price").show()
            var price = 0;
            $('.price').each(function(){
                if($(this).parents('li').find('.cart_check_span').find('span').hasClass('checked')){
                    price = price + $(this).children('span').text() * $(this).prev('.num').find('.num-txt').text();
                }
            })
            $('.js-total-price').html(price);
            $(this).parent().next().find('li').each(function(key,val){
                $(val).find('.cart_num_price').children('.num').show()
                $(val).find('.delete-btn').removeAttr('style')
                $(val).find('.cart_shop_detail').removeAttr('style')
            })
            $('.bottom-cart').removeClass('edit');
            $(this).text('编辑');
        }
    })
    // 数量增加点击
    $('.container').on('click','.response-area-plus',function(){
        var that = $(this);
        var quota = $(this).attr('data-quota');
        var num = $(this).siblings('input').val()
        if(quota == 0 ){
            $(this).siblings('input').val(parseInt(num) + 1);
        }else{
            if(parseInt(num) >= parseInt(quota)){
                tool.tip("商品数量不能超过最大购买数量");
                return false
            }else{
                $(this).siblings('input').val(parseInt(num) + 1);
            }
        }
        modifyNum($(this).siblings('input').data('id'),$(this).siblings('input').val(),that);
        if(parseInt($(this).siblings('input').val()) > 1){

        }
    })
    // 数量点击减少
    $('.container').on('click','.response-area-minus',function(){
        var that = $(this);
        var buyMin = $(this).attr('data-buyMin');
        var num = $(this).siblings('input').val()
        if(buyMin == 0 ){
            $(this).siblings('input').val(parseInt(num) - 1);
        }
        else{
            if(parseInt(num) <= parseInt(buyMin)){
                tool.tip("商品数量不能小于最小购买量");
                return false
            }else{
                $(this).siblings('input').val(parseInt(num) - 1);
            }
        }
        if(parseInt($(this).siblings('input').val())<1){
            return;
        }
        modifyNum($(this).siblings('input').data('id'),$(this).siblings('input').val(),that);
    })

    // 点击删除一款商品
    $('.container').on('click','.delete-btn',function(){
        var obj=$(this);
        tool.confirm("你确定要删除此款商品吗？",function(){
            var ids = obj.attr('data-id');
            var delids = new Array();
            delids.push(ids);
            //删除购物车
            delCart('ids[]='+ids,delids);
        })
    })
    // 点击底部删除
    $('.j-delete-goods').click(function(){
        var ids = new Array();
        var count = 0;
        $('.check-container').each(function(){
            if($(this).children('.check').hasClass('checked')){
                ids.push($(this).children('.check').attr('data-id'));
                count++;
            }
        })
        tool.confirm("你确定要删除"+count+"款商品吗？",function(){
            if (ids){
                var request = '';
                for (i=0;i<ids.length;i++){
                    request=request+'&ids[]='+ids[i];
                }
                request = request.substr(1,request.length);
                delCart(request,ids);
                
            }
        })
    })
    // 点击底部结算
    $('.js-go-pay').click(function(){
        var ids = new Array();
        var count = 0;
        $('.check-container').each(function(){
            if($(this).hasClass("hide")){
                return;
            }
            if($(this).children('.check').hasClass('checked')){
                if(!$(this).children('.check').attr('data-id')){
                    // continue;
                }
                ids.push($(this).children('.check').attr('data-id'));
                count++;
            }
        })
        window.location.href='/shop/order/waitPayOrder?cart_id=['+ids.join(",")+']'
    })
    //修改商品
    function modifyNum(id,num,that) {
        $.ajax({
            url:'/shop/cart/edit/'+$('#wid').val(),// 跳转到 action
            data:{
                'id':id,
                'num':num
            },
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    that.parents('.num').children('.num_show').children('.num-txt').html(that.siblings('input').val());
                    var price = 0
                    $('.price').each(function(){
                        if($(this).parents('li').find('.cart_check_span').find('span').hasClass('checked')){
                            price = price + $(this).children('span').text() * $(this).prev('.num').find('.num-txt').text();
                        }
                    })
                    price = price.toFixed(2)
                    $('.js-total-price').html(price);
                    console.log(response)
                }else{
                    tool.tip(response.info);
                }
            },
            error : function() {
                // view("异常！");
                tool.tip("异常！");
            }
        });
    }
    //删除购物车
    function delCart(ids,delids) {
        $.ajax({
            url:'/shop/cart/del/'+$('#wid').val(),// 跳转到 action
            data:ids,
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    for(var i=0;i<delids.length;i++){
                        $("#li_"+delids[i]).remove();
                    }
                    if($(".js-list li").length == 0){
                        $(".empty-list").removeClass("hide");
                        $(".block-cart").addClass("hide");
                        $(".js-bottom-opts").addClass("hide");
                    }
                }else{
                    tool.tip(response.info);
                }
            },
            error : function() {
                // view("异常！");
                tool.tip("异常！");
            }
        });
    }


    $('#sub').on('click',function () {
        $.ajax({
            url:'/shop/cart/del/'+$('#wid').val(),// 跳转到 action
            data:$('#invalid').serialize(),
            type:'post',
            cache:false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            success:function (response) {
                if (response.status == 1){
                    tool.tip(response.info);
                    if(!$('.js-list li').length){
                        $('.empty-list').removeClass('hide');
                    }
                    $('.js-invalid-goods').hide();
                }else{
                    tool.tip(response.info);
                }
            },
            error : function() {
                // view("异常！");
                tool.tip("异常！");
            }
        });
    })

    window.addEventListener("visibilitychange" ,function(){
        sessionStorage.setItem("refer", "true");
    })
    window.addEventListener("popstate", function(e) {  
        sessionStorage.setItem("refer", "true"); 
    },false);  
    // 下拉加载更多
    var page = 2;
    var loading = false;  //状态标记  
    var bl =false;
    window.onscroll = function () {
        if(!bl){
            if (scrollTop() + windowHeight() >= (documentHeight() - 50)) {
                if (loading) return;
                loading = true;
                $.get('/shop/cart/index/' + $('#wid').val(), { page: page }, function (data) {
                    if(data.data.length == 0)
                        bl= true;
                    var html = '';
                    var sub_html = '';
                    if (data.data !== null) {
                        if (data.data.length > 0) {
                            for (var i = 0; i < data.data.length; i++) {
                                if (data.data[i]['flag'] == 1) {
                                    html += '<li class="block-item cart_li block-item-cart" data-placeholder="' + data.data[i]['num'] + '"';
                                    html += 'data-id="' + data.data[i]['id'] + '" id="li_'+data.data[i]['id']+'">';
                                    html += '<div><div class="check-container cart_check_span"><span class="check" data-id="' + data.data[i]['id'] + '"></span></div>';
                                    html += '<div class="name-card cart_shop_name clearfix"> <a href="/shop/product/detail/' + data.data[i]['wid'] +'/'+ data.data[i]['product_id'] + '?_pid_='+ data.data[i]['id'] + '" class="thumb js-goods-link cart_img">';
                                    html += '<img class="js-lazy-'+ page +'" src="' + imgUrl + data.data[i]['img'] + '"></a>';
                                    html += '<div class="detail cart_shop_detail"><a href="/shop/product/detail/' + data.data[i]['wid'] +'/'+ data.data[i]['product_id'] + '?_pid_='+ data.data[i]['id'] + '" class="js-goods-link"><h3 class="title js-ellipsis cart_title_h3">';
                                    html += '<i>' + data.data[i]['title'] + '</i></h3><p class="sku ellipsis cart_sku">'
                                    if(data.data[i].prop1){
                                        html += '<span>'+data.data[i].prop1+':'+data.data[i].prop_value1+'</span>'
                                    }
                                    if(data.data[i].prop2){
                                        html += '<span>'+data.data[i].prop2+':'+data.data[i].prop_value2+'</span>'
                                    }
                                    if(data.data[i].prop3){
                                        html += '<span>'+data.data[i].prop3+':'+data.data[i].prop_value3+'</span>'
                                    }
                                    html += ' </p></a><div class="cart_num_price">'
                                    html += '<div class="num">';
                                    html += '<div class="num_show hide">x<span class="num-txt">' + data.data[i]['num'] + '</span></div>';
                                    html += '<div class="quantity"><button type="button" class="minus"></button>';
                                    html += '<input disabled="" data-id="' + data.data[i]['id'] +  '" type="text" pattern="[0-9]*" class="txt" value="'+data.data[i]['num']+'">';
                                    html += '<button type="button" class="plus"></button>';
                                    html += '<div class="response-area response-area-minus"></div>';
                                    html += '<div class="response-area response-area-plus"></div></div></div>';
                                    html += '<div class="price c-orange">￥';
                                    if (data.data[i]['is_prop'] == 1) {
                                        html += '<span>' + data.data[i]['activity_price'] + '</span>';
                                    } else {
                                        html += '<span>' + data.data[i]['price'] + '</span>';
                                    }
                                    html += '</div></div></div>';
                                    html += '<div class="error-box"></div></div>';
                                    html += '<div class="delete-btn" data-id="' + data.data[i]['id'] + '">';
                                    html += '<span>删除</span></div></div>';
                                    html += '<input type="hidden" name="ids[]" value="' + data.data[i]['id'] + '">'
                                    html += '</li>'
                                } else {
                                    sub_html += '<li class="block-item block-item-cart error"><div><div class="check-container hide">';
                                    sub_html += '<span class="check"></span></div><div class="name-card clearfix"> <a href="' + '#' + '" class="thumb js-goods-link"><img class="js-lazy-'+ page +'" src="' + imgUrl +data.data[i]['img'] + '" />';
                                    sub_html += '</a><div class="detail"><a href="' + '#' + '"><h3 class="title js-ellipsis"><i>' + data.data[i]['title'] + '</i></h3>';
                                    sub_html += '</a><p class="sku ellipsis">x' + data.data[i]['num'] + '</p><div class="num">×';
                                    sub_html += '<span class="num-txt">' + data.data[i]['num'] + '</span></div></div>';
                                    if (data.data[i]['flag'] == -1) {
                                        sub_html += '<div class="error-box">商品已删除</div>';
                                    } else if (data.data[i]['flag'] == 0) {
                                        sub_html += '<div class="error-box">商品已下架</div>';
                                    } else if (data.data[i]['flag'] == 3) {
                                        sub_html += '<div class="error-box">规格发生变化</div>';
                                    } else if (data.data[i]['flag'] == 4) {
                                        sub_html += '<div class="error-box">已售罄</div>';
                                    }
                                    sub_html += '</div><div class="delete-btn"><span>删除</span></div></div>';
                                    sub_html += '<input type="hidden" name="ids[]" value="' + data.data[i]['id'] + '">';
                                    sub_html += '</li>'
                                }

                            }
                            if (sub_html != '') {
                                $('.no_sale_div').css('display', 'block');
                            }
                            $('.on_sale').append(html);
                            $('.no_sale').append(sub_html);
                            //
                            if ($(".j-edit-list").text() == "完成") {
                                $(".js-list").find("li").each(function (key, val) {
                                    $(this).addClass('editing');
                                    $(val).find('input').val($(val).find('.num_show').children('.num-txt').text());
                                    $(val).find('.num_show').addClass('hide');
                                    $(val).find('.quantity').removeClass('hide');;
                                })
                                $('.bottom-cart').addClass('edit');
                                $('.check-container').each(function () {
                                    if ($(this).children('.check').hasClass('checked')) {
                                        $('.j-delete-goods').removeAttr('disabled');
                                    }
                                })
                            }
                        }
                    }
                    page++;
                    loading = false;
                })
            }
        }
        
    }
})
function trim(str,is_global)
{
    var result;
    result = str.replace(/(^\s+)|(\s+$)/g,"");
    return result;
}
//获取页面顶部被卷起来的高度
function scrollTop(){
    return Math.max(
        //chrome
        document.body.scrollTop,
        //firefox/IE
        document.documentElement.scrollTop);
}
//获取页面文档的总高度
function documentHeight(){
    //现代浏览器（IE9+和其他浏览器）和IE8的document.body.scrollHeight和document.documentElement.scrollHeight都可以
    return Math.max(document.body.scrollHeight,document.documentElement.scrollHeight);
}
function windowHeight(){
    return (document.compatMode == "CSS1Compat")?
        document.documentElement.clientHeight:
        document.body.clientHeight;
}
