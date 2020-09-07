$(function(){
  laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库
  var start = {
      elem: '#startDate',
      format: 'YYYY-MM-DD hh:mm:ss',
      min: '2009-06-16 23:59:59', //设定最小日期为当前日期
      max: '2099-06-16 23:59:59', //最大日期
      event: 'focus',
      istime: true,
      istoday: false,
      choose: function(datas){
         end.min = datas; //开始日选好后，重置结束日的最小日期
         end.start = datas //将结束日的初始值设定为开始日
      }
    };
  var end = {
      elem: '#endDate',
      format: 'YYYY-MM-DD hh:mm:ss',
      min: '2009-06-16 23:59:59',
      max: '2099-06-16 23:59:59',
      event: 'focus',
      istime: true,
      istoday: false,
      choose: function(datas){
        start.max = datas; //结束日选好后，重置开始日的最大日期
      }
    };
    laydate(start);
    laydate(end);
    $('.date-quick-pick').click(function(){
        var date = $(this).data('days');
        var data = getdate(date);
        $('#startDate').val(data.start_date);
        $('#endDate').val(data.end_date);
    })
    $('.js-export').click(function(){
        if($('#startDate').val()==''){
            tipshow();
            $('#startDate').focus();
            return;
        }
        if($('#endDate').val()==''){
            tipshow();
            $('#endDate').focus();
            return;
        }
        $('.modal').show();
        $('.modal-backdrop').show();
        center($('.modal-dialog'));
    })
    $('.close').click(function(){
        $('.modal').hide();
        $('.modal-backdrop').hide();
    })
    var index;//记录点击位置
    // 备注点击
    $(document).on('click','.info',function(){
        index = $(this).data('index');
        $('.js-remark').val('')
        $('.js-remark').val($($(this).parents('table').children('tbody').get(index)).children('.remark-row').children('td').text());
        // showModel($('#baseModal'),$('#base-modal-dialog'),1000)
        centerModel($('#base-modal-dialog'));
        $('#baseModal').show();
        $('.modal-backdrop').show();
    })
    // 备注提交
    $('.submit_info').click(function(){
        var beiinfo = $('.js-remark').val();
        if(beiinfo != ''){
          
          $($('.remark-row').get(index)).children('td').html(beiinfo);
          $($('.remark-row').get(index)).show();
          $('.js-remark').val('')
         
        }else{
          $($('.remark-row').get(index)).hide();
        }
        hideModel($('#baseModal'));
    })
    //加星
    $('.star').raty({ 
      click: function(score, evt) {
        // alert('ID: ' + $(this).data('id') + "\nscore: " + score + "\nevent: " + evt);
        $(this).parent().hide();
        $(this).parent().prev().show();
        $(this).parent().prev().children('.add_pss').hide();
        $(this).parent().prev().children('.star_score').children('.score').html(score);
        $(this).parent().prev().children('.star_score').show();
      }
    });
    //移动到加星上显示星星
    $(document).on('mouseenter','.add_pss',function(){
        $(this).parent().hide();
        $(this).parent().next().show();
    })
    //鼠标移出星星标签
    $(document).on('mouseleave','.star_container',function(){
        $(this).hide();
        $(this).prev().show();
    })

    $(document).on('mouseenter','.star_score',function(){
        $(this).parent().hide();
        $(this).parent().next().show();
    })
    //删除评分
    $(document).on('click','.delete_star',function(){
        $(this).parent().hide();
        $(this).next().raty({ 
        click: function(score, evt) {
            // alert('ID: ' + $(this).data('id') + "\nscore: " + score + "\nevent: " + evt);
            $(this).parent().hide();
            $(this).parent().prev().show();
            $(this).parent().prev().children('.add_pss').hide();
            $(this).parent().prev().children('.star_score').children('.score').html(score);
            $(this).parent().prev().children('.star_score').show();
          },
          score:0
        });
        $(this).parent().prev().children('.star_score').hide();
        $(this).parent().prev().children('.add_pss').show();
    })
});
function center(obj){
    var window_height = $(document).height();
    var height = obj.height();
    obj.css('margin-top',window_height/2-height/2);
}
function getdate(day){
    var today = new Date();
    var obj={
            end_date:'',
            start_date:'',
        };
    today.setHours(0);
    today.setMinutes(0);
    today.setSeconds(0);
    today.setMilliseconds(0);
    today = Date.parse(today);
    // 昨天
    var oneweek = 1000*60*60*24*day;
    yesterday = new Date(today-1);
    oneweek = new Date(yesterday-oneweek+1);
    obj.end_date = formatDate(yesterday);
    obj.start_date = formatDate(oneweek);
    return obj;
}
function tipshow(){
    $('.tip').html('请先选择日期范围');
    setTimeout(function(){
         $('.tip').hide(1000);
    },2000)
}
function formatDate(now) { 
    var year=now.getFullYear(); 
    var month=now.getMonth()+1; 
    var date=now.getDate(); 
    var hour=now.getHours(); 
    var minute=now.getMinutes(); 
    var second=now.getSeconds(); 
    if(minute == '0'){
        minute = '00';
    }
    if(second =='0'){
        second = '00';
    }
    if(hour =='0'){
        hour = '00';
    }
    return year+"-"+month+"-"+date+" "+hour+":"+minute+":"+second;      
}