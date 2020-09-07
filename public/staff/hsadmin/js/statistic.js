/*
 * @Description: 
 * @Author: 魏冬冬（zbf5279@dingtalk.com）
 * @Date: 2019-08-15 15:12:59
 * @LastEditors: 魏冬冬（zbf5279@dingtalk.com）
 * @LastEditTime: 2019-08-21 14:08:09
 */
$(function(){
  $('[data-toggle="tooltip"]').tooltip()
  laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库
  // 开始时间配置
  var start = {
    elem: '#startDate',
    format: 'YYYY-MM-DD',
    min: '2009-06-16', //设定最小日期为当前日期
    max: '2099-06-16', //最大日期
    event: 'focus',
    istime: true,
    istoday: false,
    choose: function(datas){
        end.min = datas; //开始日选好后，重置结束日的最小日期
        end.start = datas //将结束日的初始值设定为开始日
    }
  };
  // 结束时间配置
  var end = {
    elem: '#endDate',
    format: 'YYYY-MM-DD',
    min: '2009-06-16',
    max: '2099-06-16',
    event: 'focus',
    istime: true,
    istoday: false,
    choose: function(datas){
      start.max = datas; //结束日选好后，重置开始日的最大日期
    }
  };
  laydate(start);
  laydate(end);
  // 店铺排行搜索日期初始化开始时间
  var start1 = {
      elem: '#startDate1',
      format: 'YYYY-MM-DD',
      min: '2009-06-16', //设定最小日期为当前日期
      max: '2099-06-16', //最大日期
      event: 'focus',
      istime: true,
      istoday: false,
      choose: function(datas){
          end1.min = datas; //开始日选好后，重置结束日的最小日期
          end1.start = datas //将结束日的初始值设定为开始日
      }
  };
  // 店铺排行搜索日期初始化结束时间
  var end1 = {
    elem: '#endDate1',
    format: 'YYYY-MM-DD',
    min: '2009-06-16',
    max: '2099-06-16',
    event: 'focus',
    istime: true,
    istoday: false,
    choose: function(datas){
        start1.max = datas; //结束日选好后，重置开始日的最大日期
    }
  };
  laydate(start1);
  laydate(end1);
  /**
   * @author: 魏冬冬（zbf5279@dingtalk.com）
   * @description: 日期切换
   * @param {type} 
   * @return: 
   * @Date: 2019-08-21 10:49:43
   */
  $('.search-item-btn').click(function() {
    if ($(this).data('type') == 0) {
      $(this).siblings().removeClass('active');
      $(this).addClass('active');
      var date = $(this).data('days');
      var data = getdate(date);
      $('#startDate').val(data.start_date);
      $('#endDate').val(data.end_date);
    } else {
      $(this).siblings().removeClass('active');
      $(this).addClass('active');
      var date = $(this).data('days');
      var data = getdate(date);
      $('#startDate1').val(data.start_date);
      $('#endDate1').val(data.end_date);
    } 
  })
  /**
   * @author: 魏冬冬（zbf5279@dingtalk.com）
   * @description: 第一个删选
   * @param {type} 
   * @return: 
   * @Date: 2019-08-21 10:49:53
   */
  $('.search-static').click(function() {
    var params = {};
    params.start = $('#startDate').val();
    params.end = $('#endDate').val();
    $.get('/staff/get/active/statistic',params,function(res){
      res = typeof res === 'String' ? JSON.parse(res) : res;
      if (res.status == 1) {
        $('.add-shop-num').text(res.data.newShopCount);
        $('.active-shop-num').text(res.data.activeShopCount);
        $('.add-order-num').text(res.data.orderPayedCount);
        $('.active-user-num').text(res.data.viewTotalCount);
      }
    })
  })
  /**
   * @author: 魏冬冬（zbf5279@dingtalk.com）
   * @description: 获取商家排行数据
   * @param {type} 
   * @return: 
   * @Date: 2019-08-20 16:51:38
   */
  function getShopRank(params) {
    $.get('/staff/get/rank_list/statistic',params,function(res){
      res = typeof res === 'String' ? JSON.parse(res) : res;
      if (res.status != 1) {
        tipshow(res.info,"warn");
        return;
      }
      var html = '';
      if (res.data.data && res.data.data.length) {
        $.each(res.data.data,function(key,val) {
          if (val.rank == 1) {
            html += '<tr><td><span class="table-index first-tab">' + val.rank + '</span></td>';
          } else if (val.rank == 2) {
            html += '<tr><td><span class="table-index second-tab">' + val.rank + '</span></td>';
          } else if (val.rank == 3) {
            html += '<tr><td><span class="table-index third-tab">' + val.rank + '</span></td>';
          } else {
            html += '<tr><td><span class="table-index">' + val.rank + '</span></td>';
          }
          html += '<td><p class="store-title" data-logo="' + val.weixin_logo_url + '" data-income="' + val.income + '" data-nums="' + val.nums + '">' + val.shop_name + '</p></td>';
          html += '<td>' + val.income + '</td>';
          html += '<td>' + val.nums + '</td>';
        })
        if (params.page >= 2) {
          $('.table-scroll tbody').append(html);
        } else {
          $('.table-scroll tbody').html(html);
        }
        if (params.page == 1) {
          $('.store-data .income').text($('.table-scroll tbody tr').eq(0).children().eq(2).text());
          $('.store-data .nums').text($('.table-scroll tbody tr').eq(0).children().eq(3).text());
          $('.store-data .store-data-header span').text($('.table-scroll tbody tr').eq(0).children().eq(1).children('p').text());
          $('.store-data .store-data-header img').attr('src',$('.table-scroll tbody tr').eq(0).children().eq(1).children('p').data('logo'));
        }
      } else if (res.data.data && !res.data.data.length && params.page == 1) {
        $('.store-data .income').text('');
        $('.store-data .nums').text('');
        $('.store-data .store-data-header span').text('');
        $('.store-data .store-data-header img').attr('src','');
        $('.table-scroll tbody').html('');
        $('.table-scroll tbody').append('<tr class="no-data"><td colspan="4">暂无数据!</td></tr>');
      }
      flag = false;
      params.page ++;
    })
  }
  var searchParams = {
    order:'income_desc',
    page: 2,
  }; // 记录搜索条件
  var flag = false; // 记录搜索状态
  /**
   * @author: 魏冬冬（zbf5279@dingtalk.com）
   * @description: 搜索商家排行 
   * @param {type} 
   * @return: 
   * @Date: 2019-08-21 10:50:08
   */
  $('.search-shop').click(function() {
    searchParams.start = $('#startDate1').val();
    searchParams.end = $('#endDate1').val();
    searchParams.keywords = $('#shopName').val();
    searchParams.page = 1;
    $("#scroll")[0].scrollTop = 0;
    getShopRank(searchParams);
  })
  /**
   * @author: 魏冬冬（zbf5279@dingtalk.com）
   * @description: 商家排行排序
   * @param {type} 
   * @return: 
   * @Date: 2019-08-21 10:50:18
   */
  $('.sort-caret').click(function() {
    $('.table thead th').removeClass('ascending');
    $('.table thead th').removeClass('descending');
    $("#scroll")[0].scrollTop = 0;
    if ($(this).hasClass('ascending')) {
      $(this).parents('th').addClass('ascending')
    } else {
      $(this).parents('th').addClass('descending')
    }
    searchParams.order = $(this).data('order');
    searchParams.page = 1;
    getShopRank(searchParams);
  })
  /**
   * @author: 魏冬冬（zbf5279@dingtalk.com）
   * @description: 滚动条滚动到底部
   * @param {type} 
   * @return: 
   * @Date: 2019-08-21 10:50:27
   */
  var nDivHight = $("#scroll").height();
  $("#scroll").scroll(function() {
    nScrollHight = $(this)[0].scrollHeight;
    nScrollTop = $(this)[0].scrollTop;
    var paddingBottom = parseInt($(this).css('padding-bottom')),
        paddingTop = parseInt($(this).css('padding-top') );
    if (nScrollTop + paddingBottom + paddingTop + nDivHight >= nScrollHight) {
      if (flag) {
        return
      }
      flag = true;
      getShopRank(searchParams);
    } 
  });

  /**
   * @author: 魏冬冬（zbf5279@dingtalk.com）
   * @description: 店铺排行店铺标题点击
   * @param {type} 
   * @return: 
   * @Date: 2019-08-21 10:50:39
   */
  $('body').on('click','.store-title',function() {
    $('.store-data .income').text($(this).data('income'));
    $('.store-data .nums').text($(this).data('nums'));
    $('.store-data .store-data-header span').text($(this).text());
    $('.store-data .store-data-header img').attr('src',$(this).data('logo'));
  })
})
/**
 * @author: 魏冬冬（zbf5279@dingtalk.com）
 * @description: 格式化日期
 * @param {Number} day 天数 
 * @return: Obj 包含开始结束时间
 * @Date: 2019-08-21 09:59:16
 */
function getdate(day) {
  var today = new Date();
  var obj = {
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
  yesterday = new Date(today);
  oneweek = new Date(yesterday-oneweek+1);
  obj.end_date = formatDate(yesterday);
  obj.start_date = formatDate(oneweek);
  return obj;
}
/**
 * @author: 魏冬冬（zbf5279@dingtalk.com）
 * @description: 格式化时间格式
 * @param {Date} now 
 * @return: String 格式化的时间
 * @Date: 2019-08-21 10:02:08
 */
function formatDate(now) {
  var year=now.getFullYear();
  var month=now.getMonth()+1;
  var date=now.getDate();
  var hour=now.getHours();
  var minute=now.getMinutes();
  var second=now.getSeconds();
  if (minute == '0') {
    minute = '00';
  }
  if (second =='0') {
    second = '00';
  }
  if (hour =='0') {
    hour = '00';
  }

  // 最近7天和最近30天月份使用两位数格式 Herry 20180622
  if (month < 10) {
    month = '0' + month;
  }

  return year+"-"+month+"-"+date;
}