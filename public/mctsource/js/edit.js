// add by 黄新琴 2018/8/20
// 满减名称字数限制
$('.J_title').blur(function(){
    if($(this).val().length>15){
        tipshow('满减名称请控制在15个字以内！','warn');
    }
})
// 时间选择
$('input[name="time"]').change(function(){
    if ($(this).data('type')==2){
        $('.J_end-time').hide();
    } else {
        $('.J_end-time').show();
    }
});
var start = {
    elem: '#start_time',
    format: 'YYYY-MM-DD hh:mm:ss',
    min: laydate.now(), //设定最小日期为当前日期
    max: '2099-06-16 23:59:59', //最大日期
    istime: true,
    istoday: false,
    choose: function(datas) {
        // console.log(datas);
        $('#start_time').val(datas);
        $('#start_time').focus();
        $('#start_time').blur();
        // $('.edit_form').data("bootstrapValidator").validate('start_at');
        end.min = datas; //开始日选好后，重置结束日的最小日期
        end.start = datas //将结束日的初始值设定为开始日
    }
};
var t_min_time = $("#start_time").val() || laydate.now();
var end = {
    elem: '#end_time',
    format: 'YYYY-MM-DD hh:mm:ss',
    min: t_min_time,
    max: '2099-06-16 23:59:59',
    istime: true,
    istoday: false,
    choose: function(datas) {
        // console.log($('#endTime').val())
        $('#end_time').val(datas);
        $('#end_time').focus();
        $('#end_time').blur();
        // $('.edit_form').data("bootstrapValidator").validateField('end_at');
        start.max = datas; //结束日选好后，重置开始日的最大日期
    }
};
laydate(start);
laydate(end);
$('.suggest').hover(function(){
    $('.suggest-desc').toggle();
})
// 满减类型
$('input[name="discount-type"]').change(function(){
    if ($(this).data('type')==1){
        $('.J_wraper-1').show();
        $('.J_wraper-2').hide();
    } else if ($(this).data('type')==2){
        $('.J_wraper-2').show();
        $('.J_wraper-1').hide();
    }
});
// 新增满减利益点
var moneyNum = 3,amountNum = 3;
$('.J_profit-add').click(function(){
    var type = $('input[name="discount-type"]:checked').data('type'),html='';
    if (type == 1){
        moneyNum += 1;
        html = '<div class="profit-item"><div class="item-box"><span class="J_profit-index">';
        html += moneyNum;
        html += '</span>. 满<input type="number" class="profit-input J_profit_money">元</div>&nbsp;<div class="item-box">减<input type="number" class="profit-input J_desc_money">元</div><span class="item-del">删除</span></div>';
        $('.J_wraper-1').append(html);
    } else if (type == 2){
        amountNum += 1;
        html = '<div class="profit-item"><div class="item-box"><span class="J_profit-index">';
        html += amountNum;
        html += '</span>. 满<input type="number" class="profit-input J_profit_amount">件</div>&nbsp;<div class="item-box">减<input type="number" class="profit-input J_desc_amount">元</div><span class="item-del">删除</span></div>';
        $('.J_wraper-2').append(html);
    }
    
});
// 删除满减利益点
$('.profit-wraper').on('click', '.item-del', function(){
    $(this).parent().remove();
    var type = $('input[name="discount-type"]:checked').data('type');
    if (type == 1) {
        moneyNum -=1;
        $('.J_wraper-1 .profit-item').each(function(i){
            $(this).children().children('.J_profit-index').html(i+1);
        });
    }else if(type == 2){
        amountNum -= 1;
        $('.J_wraper-2 .profit-item').each(function(i){
            $(this).children().children('.J_profit-index').html(i+1);
        });
    }
})
// 满减商品类型
$('input[name="product-type"]').change(function(){
    if ($(this).data('type')==1){
        $('.product-wraper').hide();
    } else if ($(this).data('type')==2){
        $('.product-wraper').show();
    }
});

// 编辑模式渲染数据
if (tempData.id){
    $('.J_title').val(tempData.title);
    $('#start_time').val(tempData.start_time);
    if (tempData.end_time){
        $('#end_time').val(tempData.end_time);
    } else {
        $('.J_end-time').hide();
        $('#time-all').attr('checked',true);
    }
    var content = JSON.parse(tempData.content);
    if (tempData.type == 1) {
        moneyNum = content.length;
        var html = '',item;
        for (var i=0;i<content.length;i++){
            item = content[i];
            html += '<div class="profit-item"><div class="item-box"><span class="J_profit-index">';
            html += i + 1;
            html += '</span>. 满<input type="number" class="profit-input J_profit_money" value="' + item.condition + '">元</div>&nbsp;<div class="item-box">';
            html += '减<input type="number" class="profit-input J_desc_money" value="' + item.discount + '">元</div><span class="item-del">删除</span></div>';
        }
        $('.J_wraper-1').html(html);
    } else if (tempData.type == 2){
        amountNum =content.length;
        $('#amount-type').attr('checked',true);
        $('.J_wraper-2').show();
        $('.J_wraper-1').hide();
        var html = '',item;
        for (var i=0;i<content.length;i++){
            item = content[i];
            html += '<div class="profit-item"><div class="item-box"><span class="J_profit-index">';
            html += i + 1;
            html += '</span>. 满<input type="number" class="profit-input J_profit_amount" value="' + item.condition + '">件</div>&nbsp;<div class="item-box">';
            html += '减<input type="number" class="profit-input J_desc_amount" value="' + item.discount + '">元</div><span class="item-del">删除</span></div>';
        }
        $('.J_wraper-2').html(html);
    }
    if (tempData.use_type == 2){
        $('#design-type').attr('checked',true);
        $('.product-wraper').show();
    }
    
}

