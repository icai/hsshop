$(function(){
    //表单验证
    var flag = false;//add by 魏冬冬防止表单重复提交2018-7-16
    $("#shopForm").validate({
        ignore: "",
        rules: {
            shop_name: {
                required: true,
                maxlength: 30
            },
            company_name: {
                required: true,
                maxlength: 30
            },
            address: {
                required: true,
                maxlength: 50
            },
            //business_id: "required"
        },
        messages: {
            shop_name: {
                required: "请填写店铺名称",
                maxlength: "最大长度不能大于30"
            },
            company_name: {
                required: "请填写公司名称",
                maxlength: "最大长度不能大于30"
            },
            address: {
                required: "请填写详细地址",
                maxlength: "最大长度不能大于50"
            },
            //business_id: "请选择主分类"
        },
        submitHandler : function(form) {  //验证通过后的执行方法
            $('input[type="submit"]').addClass('disabled');//防止表单多次提交
            //当前的form通过ajax方式提交（用到jQuery.Form文件
            if(flag) return;
            flag = true;
            $.post($('form').attr('action'), $('form').serialize(), function( data ) {
                tipshow(data.info);
                setTimeout(function(){
                    if ( data.status == 1 ) {
                        /* 后台验证通过 */
                        if ( data.url ) {
                            /* 后台返回跳转地址则跳转页面 */
                            window.location.href = data.url;
                        } else {
                            /* 后台没有返回跳转地址 */
                            // to do somethings
                        }
                    } else {
                        $('input[type="submit"]').removeClass('disabled');
                        flag = false;
                        // $('input[type="submit"]').prop('disabled', false);
                        /* 后台验证不通过 */
                        // to do somethings
                    }
                },500);
                
            }, 'json');
            return false;
        }
    });

    /* 选择省 拉取后台城市数据并展示 重置地区数据 */
    $('#province').change(function(){
        var id = $(this).val();
        if ( id ) {
            var city_arr = regions_datas[id];
            var str_city = '<option value="">选择城市</option>';
            for ( var i = 0; i < city_arr.length; i++ ) {
                str_city +='<option value="' + city_arr[i]['id'] + '">' + city_arr[i]['title'] + '</option>';
            }
            $('#city').html(str_city);
        }else{
            $('#city').html('<option value="">选择城市</option>');
        }
        $('#district').html('<option value="">选择地区</option>');
    });
    /* 选择城市 拉取后台地区数据并展示 */
    $('#city').change(function(){
        var id = $(this).val();
        if ( id ) {
            var town_arr = regions_datas[id];
            var str_town = '<option value="">选择地区</option>';
            for (var i = 0; i < town_arr.length; i++) {
                str_town +='<option value="' + town_arr[i]['id'] + '">' + town_arr[i]['title'] + '</option>';
            }
            $('#district').html(str_town);
        } else {
            $('#district').html('<option value="">选择地区</option>');
        }
    });

    // 同意声明
    $('#agreement').click(function(){
        if($(this).is(':checked')){
            $('.submit-btn').removeAttr('disabled');
        }else{
            $('.submit-btn').attr('disabled','disabled');
        }
    })

    /*$(document).on('click','.top_radio li input',function(){
        $('.classfiy_1').html($(this).next().html());
        var id = $(this).parent().parent().data('id');
        $('input[name="business_id"]').val($(this).val());
        $('#classfiy_input').val(id);
        var html = '';
        $.each(business_datas, function(index, val) {
            $.each(val, function(idx, v) {
                if ( v['pid'] == id ) {
                    if ( idx == 0 ) {
                        $('.classfiy_2 .c-gray').html(v['title']);
                        html += '<li><label class="radio"><input type="radio" name="shop_category_2" value="'+ v['id']+'" checked><span>'+ v['title']+'</span></label></li>'
                    } else {
                        html += '<li><label class="radio"><input type="radio" name="shop_category_2" value="'+ v['id']+'"><span>'+ v['title']+'</span></label></li>'
                    }
                }
            });
        });

        if(html != ''){
            $('.choose_classfiy_2').css('display','inline-block');
            $('.choose_classfiy_2 .widget-selectbox-content ul').html(html);
        }else{
            $('.choose_classfiy_2').css('display','none');
        }
    })
    $(document).on('click','.sub_radio li input',function(){
        $('.classfiy_2').html($(this).next().html());
        $('input[name="business_id"]').val($(this).val());
    })*/

})