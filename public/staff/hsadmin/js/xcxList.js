$(function () {
  //	设置日期
  var start = {
    elem: '#start_time',
    format: 'YYYY-MM-DD hh:mm:ss',
    min: laydate.now(), //设定最小日期为当前日期
    max: '2099-06-16 23:59:59', //最大日期
    istime: true,
    istoday: false,
    choose: function (datas) {
      end.min = datas; //开始日选好后，重置结束日的最小日期
      end.start = datas //将结束日的初始值设定为开始日
      $('.start_time').text(datas);
    }
  };
  var end = {
    elem: '#end_time',
    format: 'YYYY-MM-DD hh:mm:ss',
    min: laydate.now(),
    max: '2099-06-16 23:59:59',
    istime: true,
    istoday: false,
    choose: function (datas) {
      start.max = datas; //结束日选好后，重置开始日的最大日期
      $('.end_time').html(datas);;
    }
  };
	/**
	 * 获取当前行的id和title
	 * @param {any} thisDom 
	 * @returns 返回当前行的wid
	 */
  function getWid(thisDom) {
    return {
      wid: thisDom.parents(".tr-id").find(".xcx-id").data("wid"),
      title: thisDom.parents(".tr-id").find(".title_merchant").html()
    }
  }
	/**
	 * 封装下拉选择小程序页面路径的插入函数
	 * @param {any} thisDom 当前Dom的this 相当于$(this)
	 * @param {any} targetDom 目标DOM
	 */
  function getXcxRes(thisDom, targetDom) {
    targetDom.empty()
    const this_verResult = thisDom.data("ver")
    if (this_verResult.page_list) {
      const xcx_page = JSON.parse(this_verResult.page_list)
      let options = ''
      for (let i = 0; i < xcx_page.length; i++) {
        options = `<option>${xcx_page[i]}</option>`
        targetDom.append(options);
      }
    }
  }
  laydate(start);
  laydate(end);

  layer.config({
    extend: 'extend/layer.ext.js'
  });
  /**
  * @author: 尚松博（ingkongshi@dingtalk.com）
  * @description: 添加插件
  * @param {type} 
  * @return: 
  * @Date: 2019-10-10 16:03:12
  */
  $(".addPlugin").click(function() {
    var res = $(this).data("ver");
    $.ajax({
      type: "POST",
      url: "/staff/xcx/pluginApply",
      data: {
        id: res.id
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      async: true,
      success: function (res) {
        if (res.status == 1) {
          tipshow(res.info, "info")
        } else {
          tipshow(res.info, "warn")
        }
      },
      error: function () {
        tipshow("数据访问错误", "warn")
      }
    });
  })
  /**
  * @author: 尚松博（ingkongshi@dingtalk.com）
  * @description: 插件列表
  * @param {type} 
  * @return: 
  * @Date: 2019-10-10 16:03:12
  */
  $(".pluginList").click(function() {
    //弹出一个页面层
    layer.open({
      type: 1,
      area: ['1200px', '600px'],
      title: '插件列表',
      shadeClose: true, //点击遮罩关闭
      content: $('.pluginList_model').html(),
    }); 
    $(".pluginListTable").show()
    var res = $(this).data("ver");
    $.ajax({
      type: "GET",
      url: "/staff/xcx/pluginList",
      data: {
        id: res.id
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      async: true,
      success: function (res) {
        if (res.status == 1) {
          var tableData = ''
        for (var i = 0; i < res.data.length; i++) {
          tableData += "<tr>";
          tableData += "<td><img class='pluginImg' src=" + res.data[i].headimgurl + "/></td>"
          tableData += "<td>" + res.data[i].appid + "</td>"
          tableData += "<td>" + ( res.data[i].status == 1 ? '申请中' : res.data[i].status == 2 ? '申请通过' : res.data[i].status == 3 ? '被拒绝' : '申请超时') + "</td>"
          tableData += "<td>" + res.data[i].nickname + "</td>"
          tableData += "<td>" + (res.data[i].version ? res.data[i].version : '-') + "</td>"
          tableData += "<td><a href='javascript:void(0);' class='updatePluginBtn' data-appid=" + res.data[i].appid + ">更新插件</a></td>"
          tableData += "</tr>";
        }
        $(".pluginTableData").html(tableData)
        $(".pluginTableData").find(".updatePluginBtn").click(updatePluginFn)
        } else {
          $(".pluginTableData").html("<tr><td colspan='6'>暂无数据</td></tr>")
            tipshow(res.info, "warn")
        }
      },
      error: function () {
        tipshow("数据访问错误", "warn")
      }
    });
    // 更新插件
    function updatePluginFn () {
     var currentAppid = $(this).data("appid")
     layer.prompt({
       title: '请输入更新插件的版本号：'
     }, function(value, index, elem){
       if (value) {
         $.ajax({
           type: "POST",
           url: "/staff/xcx/pluginUpdate",
           data: {
             id: res.id,
             plugin_appid: currentAppid,
             user_version: value
           },
           headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },
           async: true,
           success: function (res) {
             if (res.status == 1) {
               tipshow(res.info, "info")
             } else {
               tipshow(res.info, "warn")
             }
           },
           error: function () {
             tipshow("数据访问错误", "warn")
           }
         });
       } else {
         tipshow("请输入更新插件的版本号", "warn")
       }
       layer.close(index);
     });
   }
  })
  /**
  * @author: 戴江淮（npr5778@dingtalk.com）
  * @description: 查看每月提审额度限制&加急审核机制
  * @param {type} 
  * @return: 
  * @Date: 2019-10-10 16:03:12
  */
  $(".look_btn").click(function () {
    layer.open({
      type: 1,
      area: ['600px', 'auto'],
      title: "每月提审额度限制&加急审核机制",
      shadeClose: true, //点击遮罩关闭
      content: $('.audit_code_model').html(),
    })
    $.ajax({
      url: "/staff/xcx/queryQuota",
      type: "GET",
      async: true,
      success: function (res) {
        hstool.closeLoad();
        if (res.status == 1) {
          $(".rest").val(res.data.rest)
          $(".limit").val(res.data.limit)
          $(".speedup_rest").val(res.data.speedup_rest)
          $(".speedup_limit").val(res.data.speedup_limit)
          // tipshow(res.info, "info");
          // window.location.reload();
        } else {
          tipshow(res.info, "warn")
        }
      },
      error: function () {
        hstool.closeLoad();
        tipshow("数据访问错误", "warn")
      }
    })
  })
  /**
  * @author: 戴江淮（npr5778@dingtalk.com）
  * @description: 加急审核申请
  * @param {type} 
  * @return: 
  * @Date: 2019-10-10 19:00:45
  */
  $(".urgent-Audit").click(function () {
    hstool.load();
    var id = $(this).data('xcxid');
    $.ajax({
      type: "POST",
      url: "/staff/xcx/speedUpAudit",
      data: {
        id: id
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      async: true,
      success: function (res) {
        hstool.closeLoad();
        if (res.status == 1) {
          tipshow("加急成功，请耐心等待审核结果", "info")
        } else {
          tipshow(res.info, "warn")
        }
      },
      error: function () {
        hstool.closeLoad();
        tipshow("数据访问错误", "warn")
      }
    });
  })
  /**
  * @author: 
  * @description: 选择上传代码的方式
  * @param {type} 
  * @return: 
  * @update: 尚松博（ingkongshi@dingtalk.com）2020-03-06 15:23:55 增加参数
  * @Date: 2019-10-10 19:00:45
  */
  $('.upload_code').click(function () {
    var xcxid = $(this).parents(".tr-id").find(".xcx-id-box").val();
    var title = $(this).parents(".tr-id").find(".title_merchant").text();
    var res = $(this).data("ver");
    $(".upload_title").text(title)
    if (res.online_live_status) {
      commitXcxCode(xcxid, 1)
    } else {
      //弹出一个页面层
      layer.confirm('请选择送审方式：', {
        btn: ['直播送审','普通送审'], //按钮
        closeBtn: 0,
        shadeClose: true
      }, function(){
        commitXcxCode(xcxid, 1)
      }, function(){
        commitXcxCode(xcxid, 0)
      });
    }
  })
  /**
  * @author: 
  * @description: 上传代码
  * @param {numbe} id 小程序id 
  * @param {number} type 上传类型 
  * @return: 
  * @update: 尚松博（ingkongshi@dingtalk.com）2020-03-06 15:23:55 增加参数
  * @Date: 2019-10-10 19:00:45
  */
  function commitXcxCode(id, type) {
    layer.open({
      type: 1,
      area: ['600px', 'auto'],
      shadeClose: true, //点击遮罩关闭
      content: type == 1 ? $('.upload_code_model_live').html() : $('.upload_code_model').html(),
      btn: ['确认', '取消'],
      yes: function (idnex, layero) {
        hstool.load();
        var data = {
          user_version: $(layero).find(".version").val(),
          user_desc: $(layero).find(".baseinfo").val(),
          template_id: $(layero).find(".template_id").val(),
          xcxid: id,
          live_status: type
        };
        $.ajax({
          url: "/staff/xcx/commit",
          type: "POST",
          data: data,
          dataType: 'json',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (res) {
            hstool.closeLoad();
            if (res.status == 1) {
              tipshow(res.info, "info");
              //何书哲 2018年10月12日 为方便客服操作，去掉操作后自动刷新功能
              // window.location.reload();
            } else {
              tipshow(res.info, "warn")
            }
          },
          error: function (res) {
            alert("数据访问错误");
          }
        });
        layer.closeAll();
      }
    });
  }
  // 设置域名
  $('body').on('click', '.setting_host', function () {
    var id = $(this).data('xcxid')
    //弹出一个页面层
    layer.open({
      type: 1,
      title: '请输入域名',
      shadeClose: true, //点击遮罩关闭
      content: $('.set_code_model').html(),
      btn: ['确认', '取消'],
      success: function (index, layero) {
      },
      yes: function (idnex, layero) {
        hstool.load();
        var data = {
          id: id,
          action: "set",
          domain: $(layero).find(".set_zhost").val()
        }
        $.ajax({
          url: "/staff/xcx/modifyDomain",
          type: "POST",
          data: data,
          dataType: 'json',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (res) {
            hstool.closeLoad();
            if (res.status == 1) {
              tipshow(res.info, "info");
              window.location.reload();
            } else {
              tipshow(res.info, "warn")
            }
          },
          error: function (res) {
            hstool.closeLoad();
            alert("数据访问错误");
          }
        }),
          layer.closeAll();
      }
    });
  })
  // 设置业务域名
  $('body').on('click', '.js_setting-webview-host', function () {
    var xcxid = $(this).parents(".tr-id").find(".xcx-id-box").val();
    //弹出一个页面层
    layer.open({
      type: 1,
      title: '请输入业务域名',
      shadeClose: true, //点击遮罩关闭
      content: $('.set_webview_model').html(),
      btn: ['确认', '取消'],
      success: function (index, layero) {
      },
      yes: function (idnex, layero) {
        hstool.load();
        var data = {
          xcxid: xcxid,
          action: "set",
          domain: $(layero).find(".set_webview_zhost").val()
        }
        $.ajax({
          url: "/staff/xcx/setWebviewDomain",
          type: "POST",
          data: data,
          dataType: 'json',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (res) {
            hstool.closeLoad();
            if (res.errCode == 0) {
              tipshow("设置业务域名成功！", "info");
              window.location.reload();
            } else {
              tipshow(res.errMsg, "warn");
            }
          },
          error: function (res) {
            hstool.closeLoad();
            alert("数据访问错误");
          }
        }),
          layer.closeAll();
      }
    });
  })
  // 提交审核
  $('.submit_code').click(function () {
    $(".submit_select").html("");
    var res = $(this).data("ver");
    var xcxid = $(this).parents(".tr-id").find(".xcx-id-box").val();
    var title = $(this).parents(".tr-id").find(".title_merchant").text();
    var sub_title = '<div class="sub_code_model"><div class="sub_info">标题：</div><div><input class="sub_title" type="text" name="title" value="' + title + '"></div></div>'
    var sub_tag = '';
    $(".title_up").text(title);
    $(".sub_title").val(title);
    var tagTitle = '';
    if (res.category_list) {
      var category_list = JSON.parse(res.category_list);
      //add by jonzhang
      if (category_list.length != 0) {
        tagTitle = category_list[0].first_class;
      }
      sub_tag = '<div class="sub_code_model"><div class="sub_info">标签：</div><div><input type="text" name="tag" class="sub_selfir" value="' + tagTitle + '"></div></div>';
      for (var i = 0; i < category_list.length; i++) {
        var option = '<option value=' + category_list[i].first_id + ',' + category_list[i].second_id + ',' + category_list[i].third_id + '>' + category_list[i].first_class + "," + category_list[i].second_class + "," + category_list[i].third_class; +'</option>';
        if (option.indexOf("undefined") > 0) {
          option = option.replace("undefined", "");
        }
        re = new RegExp("undefined", "g"); //定义正则表达式
        option = option.replace(re, "");
        $(".submit_select").append(option);
      }
    }
    if (res.page_list) {
      var page_list = JSON.parse(res.page_list);
      for (var i = 0; i < page_list.length; i++) {
        var option = '<option>' + page_list[i] + '</option>';
        if (page_list.indexOf("undefined") > 0) {
          page_list = page_list.replace("undefined", "");
        }
        $(".service_select").append(option);
      }
    }
    //弹出一个页面层
    layer.open({
      type: 1,
      area: ['600px', 'auto'],
      title: '提交审核',
      shadeClose: true, //点击遮罩关闭
      content: $('.submit_code_model').append(sub_tag + sub_title).html(),
      btn: ['确认', '取消'],
      success: function (index, layero) {
      },
      yes: function (idnex, layero) {
        hstool.load();
        var category = $(layero).find(".submit_select option:selected").text().split(',');
        var category_id = $(layero).find(".submit_select option:selected").val().split(',');
        var address = $(layero).find(".service_select option:selected").text();
        var item_list = [{
          first_class: category[0],
          second_class: category[1],
          third_class: category[2],
          first_id: category_id[0],
          second_id: category_id[1],
          third_id: category_id[2],
          address: address,
          tag: $(layero).find("input[name=tag]").val(),
          title: $(layero).find("input[name=title]").val(),
        }];
        var data = {
          item_list: item_list,
          xcxid: xcxid
        };
        $.ajax({
          type: "POST",
          url: "/staff/xcx/submitAudit",
          data: data,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          async: true,
          success: function (res) {
            hstool.closeLoad();
            if (res.status == 1) {
              tipshow(res.info, "info");
              //何书哲 2018年10月12日 为方便客服操作，去掉操作后自动刷新功能
              // window.location.reload();
            } else {
              tipshow(res.info, "warn");
            }

          },
          error: function () {
            alert("数据访问错误")
          }
        });
        layer.closeAll();
      },
      end: function () {
        $('.sub_code_model').remove()
      }
    });
  })
  // 绑定体验
  $('.bind_experiencer').click(function () {
    var id = $(this).data('xcxid');
    layer.prompt({ title: '请填写体验中微信号', formType: 3 }, function (pass, index) {
      content:
      hstool.load();
      var data = {
        wechatid: $(".layui-layer-input").val(),
        id: id
      }
      $.ajax({
        url: "/staff/xcx/bindTester",
        type: "POST",
        data: data,
        dataType: 'json',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (res) {
          hstool.closeLoad();
          if (res.status == 1) {
            tipshow(res.info, "info")
          } else {
            tipshow(res.info, "warn")
          }
        },
        error: function (res) {
          alert("数据访问错误");
        }
      }),
        layer.close(index);
    });
  })
  //解绑体验者
  $('.cancel_experiencer').click(function () {
    var xcxid = $(this).parents(".tr-id").find(".xcx-id-box").val();
    layer.prompt({ title: '请填写体验中微信号', formType: 3 }, function (pass, index) {
      content:
      hstool.load();
      var data = {
        wechatid: $(".layui-layer-input").val(),
        xcxid: xcxid
      }
      $.ajax({
        url: "/staff/xcx/unbindTester",
        type: "POST",
        data: data,
        dataType: 'json',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (res) {
          hstool.closeLoad();
          if (res.status == 1) {
            tipshow("解绑成功", "info")
          } else {
            tipshow(res.info, "warn")
          }
        },
        error: function (res) {
          alert("数据访问错误");
        }
      }),
        layer.close(index);
    });
  })
  //查看模板消息
  $('.see_news_modei').click(function () {
    $('.see_code_model').html("");//清空模板
    var id = $(this).data('xcxid');
    $.ajax({
      type: "POST",
      url: "/staff/xcx/getTemplates",
      data: {
        id: id
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      async: true,
      success: function (res) {
        $('.see_code_model').html("");//清空模板
        if (res.status == 1) {
          for (var i = 0; i < 3; i++) {
            var html = '<div class="see_model"><h3 class="code_title" style="margin-bottom: 10px; text-align: center; color: red;">' + res.data[i].title + '</h3>';
            html += '<div><div class="info">模板id： </div><div><p class="template_id">' + res.data[i].template_id + '</p></div></div>';
            html += '<div><div class="info">通知对象：</div><div><p>买家</p></div></div>';
            html += '<div><div class="info">模板内容：</div><div><p class="code_content">' + res.data[i].content + '</p></div></div>'
            html += '<div><div class="info">模板示例：</div><div><p class="code_example">' + res.data[i].example + '</p></div></div></div>';
            $('.see_code_model').append(html);
          }
          //弹出一个页面层
          layer.open({
            type: 1,
            area: ['800px', '90%'],
            title: '消息模板',
            shadeClose: true, //点击遮罩关闭
            content: $('.see_code_model').html()
          });
        } else {
          tipshow(res.info, 'warn')
        }
      },
      error: function () {
        alert("数据访问错误")
      }
    });
  })
  //查看详情
  $('.see_detail').click(function () {
    //弹出一个页面层
    layer.open({
      type: 1,
      area: ['1200px', '700px'],
      title: '查看备注',
      shadeClose: true, //点击遮罩关闭
      content: $('.detail_model').html(),
    });
    $(".table-detail").show()
    $(".remark-table").hide()
    $(".detail_tab").addClass("active_tab")
    $(".remark_tab").removeClass("active_tab")
  })
  //获取类目
  $(".get_category").click(function () {
    hstool.load();
    var id = $(this).data('xcxid');
    $.ajax({
      type: "GET",
      url: "/staff/xcx/getCategory",
      data: {
        id: id
      },
      async: true,
      success: function (res) {
        hstool.closeLoad();
        if (res.status == 1) {
          tipshow(res.info, "info");
          //何书哲 2018年10月12日 为方便客服操作，去掉操作后自动刷新功能
          // window.location.reload();
        } else {
          tipshow(res.info, "warn")
        }
      },
      error: function () {
        hstool.closeLoad();
        alert("数据访问错误")
      }
    });
  })
  //获取页面
  $(".get_page").click(function () {
    hstool.load();
    var xcxid = $(this).parents(".tr-id").find(".xcx-id-box").val();
    $.ajax({
      type: "GET",
      url: "/staff/xcx/getPage",
      data: {
        xcxid: xcxid
      },
      async: true,
      success: function (res) {
        hstool.closeLoad();
        if (res.status == 1) {
          tipshow(res.info, "info");
          //何书哲 2018年10月12日 为方便客服操作，去掉操作后自动刷新功能
          // window.location.reload();
        } else {
          tipshow(res.info, "warn")
        }
      },
      error: function () {
        hstool.closeLoad();
        alert("数据访问错误")
      }
    });
  })
  //提交发布
  $(".code_release").click(function () {
    hstool.load();
    var xcxid = $(this).parents(".tr-id").find(".xcx-id-box").val();
    $.ajax({
      type: "POST",
      url: "/staff/xcx/release",
      data: {
        xcxid: xcxid
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      async: true,
      success: function (res) {
        hstool.closeLoad();
        if (res.status == 1) {
          tipshow(res.info, "info");
          //何书哲 2018年10月12日 为方便客服操作，去掉操作后自动刷新功能
          // window.location.reload();
        } else {
          tipshow(res.info, "warn");
        }
      },
      error: function () {
        hstool.closeLoad();
        alert("数据访问错误")
      }
    });
  })
  //取消审核
  $(".cancel_submit").click(function () {
    hstool.load();
    var id = $(this).data('xcxid');
    $.ajax({
      type: "POST",
      url: "/staff/xcx/cancelAudit",
      data: {
        id: id
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      async: true,
      success: function (res) {
        hstool.closeLoad();
        if (res.errCode == 0) {
          tipshow("取消审核成功", "info")
        } else {
          tipshow(res.errMsg, "warn")
        }
      },
      error: function () {
        hstool.closeLoad();
        alert("数据访问错误")
      }
    });
  })
  //朕要体验
  $(".experience_code").click(function () {
    var wid = $(this).parents(".tr-id").find(".xcx-id").data("wid");
    $.ajax({
      type: "GET",
      url: "/staff/xcx/getQrCode",
      data: {
        wid: wid
      },
      async: false,
      success: function (res) {
        if (res.status == 1) {
          $("#experience_code").attr("href", res)
        } else {
          tipshow(res.info, 'warn')
        }
      },
      error: function () {
        alert("数据访问错误")
      }
    });
  })
  //设置消息模板
  $(".set_modei").click(function () {
    hstool.load();
    var xcxid = $(this).parents(".tr-id").find(".xcx-id-box").val();

    hstool.load();
    $.ajax({
      type: "POST",
      url: "/staff/xcx/addTemplates",
      data: {
        xcxid: xcxid
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      async: true,
      success: function (res) {
        hstool.closeLoad();
        if (res.status == 1) {
          tipshow(res.info, 'info')
        } else {
          tipshow(res.info, 'warn')
        }
      },
      error: function () {
        hstool.closeLoad();
        alert("数据访问错误")
      }
    });
  })
  // 获取二维码
  $(".get_qrcode").click(function () {
    // 按照封装方法获取wid和title
    const xcxid = $(this).parents(".tr-id").find(".xcx-id-box").val();
    const this_title = getWid($(this)).title;
    $(".qr_code_title").text(this_title)
    // 获取页面路径的options等参数
    const this_verResult = $(this).data("ver")
    getXcxRes($(this), $(".qr_code_path"))
    $("#img_qrcode").attr("src", '');
    $(".qr_code_model").show()
    $(".qr_code_img").hide()
    // 弹出二维码弹窗
    layer.open({
      type: 1,
      area: ['500px', '320px'],
      title: '获取二维码',
      shadeClose: true, // 点击遮罩层关闭弹窗
      btn: ['朕已确认', '朕再想想'],
      content: $(".get_qrcode_model").html(),
      yes: (index, layero) => {
        hstool.load();
        const data = {
          xcxid: xcxid,
          wid: getWid($(this)).wid,
          width: $(layero).find(".qr_code_width").val(),
          path: $(layero).find(".qr_code_path option:selected").val(),
          query: $(layero).find(".qr_code_params").val()
        };
        $.ajax({
          url: "/staff/xcx/qrcode",
          type: "GET",
          data: data,
          dataType: 'json',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (result) {
            if (result.errCode === 0) {
              $(".xcx-xcximg").attr("src", `data:image/png;base64,${result.data}`);
              if ($("#img_qrcode").attr("src")) {
                $(".qr_code_model").hide()
                $(".qr_code_img").show()
                tipshow("领取二维码成功", "info")
              }
            } else {
              tipshow(result.errMsg, "error")
            }
            hstool.closeLoad()
          },
          error: (e) => {
            hstool.closeLoad()
            tipshow(e, "error")
          }
        })
      },
      cancel: (index, layero) => {
        setTimeout(() => {
          $(".qr_code_model").show()
          $(".qr_code_img").hide()
        }, 200)
      },
    })
  })
  // 获取菊花码
  $(".get_flower_code").click(function () {
    //const wid = getWid($(this)).wid;
    const xcxid = $(this).parents(".tr-id").find(".xcx-id-box").val();
    const this_title = getWid($(this)).title;
    $(".qr_flowercode_title").text(this_title)
    getXcxRes($(this), $(".qr_flowercode_path"))
    $(".qr_flower_code_model").show()
    $(".flower_img").hide()
    $("#img_xcxm").attr("src", '');
    // 弹出菊花码弹窗
    layer.open({
      type: 1,
      area: ['500px', '320px'],
      title: '获取菊花码',
      shadeClose: true, // 点击遮罩层关闭弹窗
      btn: ['朕已确认', '朕再想想'],
      content: $(".get_flower_model").html(),
      yes: (index, layero) => {
        hstool.load();
        const data = {
          xcxid: xcxid,
          width: $(layero).find(".qr_flowercode_width").val(),
          page: $(layero).find(".qr_flowercode_path option:selected").val(),
        };
        $.ajax({
          url: "/staff/xcx/code",
          type: "GET",
          data: data,
          async: true,
          success: (result) => {
            if (result.errCode === 0) {
              $(".xcx-flower-img").attr("src", 'data:image/png;base64,' + result.data);
              if ($('#img_xcxm').attr('src')) {
                $(".qr_flower_code_model").hide()
                $(".flower_img").show()
                tipshow("领取菊花码成功", "info")
              }
            } else {
              tipshow(result.errMsg, "error")
            }
            hstool.closeLoad()
          },
          error: (e) => {
            hstool.closeLoad()
            tipshow(e, "error")
          }
        })
      },
      cancel: (index, layero) => {
        setTimeout(() => {
          $(".qr_flower_code_model").show()
          $(".flower_img").hide()
        }, 200)
      },
    })
  })
  // 添加备注
  $(".add_remark_btn").click(function () {
    const xcxid = $(this).parents(".tr-id").find(".xcx-id-box").val();
    const this_wid = getWid($(this)).wid;
    const this_title = getWid($(this)).title;
    $(".remark_add_title").text(this_title)
    layer.open({
      type: 1,
      area: ['500px', 'auto'],
      title: '添加备注',
      shadeClose: true,
      btn: ['朕已确认', '朕再想想'],
      content: $(".add_remark_model").html(),
      yes: (index, layero) => {
        hstool.load();
        if ($(layero).find(".remark_add_cont").val()) {
          // 参数需要配置
          const data = {
            xcxid: xcxid,
            appId: $(this).parents(".tr-id").find(".xcx_app_id").text(),
            wid: this_wid,
            appName: this_title,
            content: $(layero).find(".remark_add_cont").val(),
          }
          $.ajax({
            url: "/staff/xcx/log/add",
            type: "GET",
            data: data,
            async: true,
            success: (result) => {
              hstool.closeLoad()
              if (result.errCode === 0) {
                tipshow("添加备注成功", "info")
              } else {
                tipshow(result.errMsg, "warn")
              }
            },
          })
          layer.closeAll();
        } else {
          hstool.closeLoad()
          tipshow("请输入备注信息", "warn")
        }
      }
    })
  })
  // 下架小程序
  $(".off_the_shelf").click(function () {
    $.ajax({
      type: "GET",
      url: "/staff/xcx/pulloff",
      data: {
        xcxid: $(this).parents(".tr-id").find(".xcx-id-box").val(),
      },
      async: true,
      success: (result) => {
        if (result.errCode === 0) {
          tipshow("小程序下架成功", "info")
        } else {
          tipshow(result.errMsg, "error")
        }
      },
      error: (e) => {
        tipshow(e.errCode, "error")
      },
    })
  })
  // 作废小程序
  $(".to_void_btn").click(function () {
    $.ajax({
      type: "GET",
      url: "/staff/xcx/cancel",
      data: {
        xcxid: $(this).parents(".tr-id").find(".xcx-id-box").val(),
      },
      async: true,
      success: (result) => {
        if (result.errCode === 0) {
          tipshow("小程序已被作废", "info")
        } else {
          tipshow(result.errMsg, "error")
        }
      },
      error: (e) => {
        tipshow(e.errCode, "error")
      },
    })
  })

  //add by zhangguojun 已经发布的小程序，进行版本回退
  $("#revert_Release").click(function () {
    $.ajax({
      type: "GET",
      url: "/staff/xcx/revertCodeRelease",
      data: {
        id: $(this).parents(".tr-id").find(".xcx-id-box").val()
      },
      async: true,
      success: (result) => {
        if (result.errCode === 0) {
          tipshow("小程序回退成功", "info")
        } else {
          tipshow(result.errMsg, "error")
        }
      },
      error: (e) => {
        tipshow(e.errCode, "error")
      },
    })
  })

  // 在弹窗里面查看备注 查看详情的时候
  $("body").on('click', '.remark_tab', function () {
    $(".table-detail").hide()
    $(".remark-table").show()
    $(".remark_tab").addClass("active_tab")
    $(".detail_tab").removeClass("active_tab")
  })
  // 在弹窗里面查看备注的时候
  $("body").on('click', '.detail_tab', function () {
    $(".table-detail").show()
    $(".remark-table").hide()
    $(".detail_tab").addClass("active_tab")
    $(".remark_tab").removeClass("active_tab")
  })
  //一键获取域名
  $(".get_host_btn").click(function () {
    hstool.load();
    $.ajax({
      type: "GET",
      url: "/staff/xcx/getAllDomains",
      data: $(".search_form").serialize(),
      async: true,
      success: function (res) {
        hstool.closeLoad();
        if (res.status == 1) {
          $('.host_con').text(res.data.data)
          layer.open({
            type: 1,
            area: ['400px', '300px'],
            title: '小程序域名合集',
            shadeClose: true, //点击遮罩关闭
            content: $('.get_host').html()
          });
        }
      },
      error: function () {
        hstool.closeLoad();
        alert("数据访问错误")
      }
    });
  })


  var globalPage = {
    total: 0,
    pageSize: 0,
  }
	/**
	 * 显示备注的请求
	 * @param {*} thisDom 当前DOM
	 * @param {*} currentPage 当前页
	 */
  function seeRemark(thisDom, currentPage) {
    $.ajax({
      url: "/staff/xcx/log/showAll",
      type: "GET",
      data: {
        appId: thisDom.parents(".tr-id").find(".xcx_app_id").text(),
        wid: thisDom.parents(".tr-id").find(".xcx-id").data("wid"),
        page: currentPage,
      },
      async: false,
      success: (result) => {
        $(".remark_tbody").empty()
        if (result.errCode === 0) {
          result.data.forEach((e) => {
            let html = `<tr><td>${e.app_name}</td>`;
            html += `<td>${e.content}</td>`
            html += `<td>${e.operator}</td>`
            html += `<td>${e.create_time}</td></tr>`
            $(".remark_tbody").append(html)
          })
          globalPage.total = result.total
          globalPage.pageSize = result.pageSize
        }
      },
      error: (error) => {
        tipshow(error, "error")
      }
    })
  }
	/*
	* 小程序自动更新
	*/
  // 全选按钮的事件
  var aIds = [];
  $("#allCheck").click(function () {
    if ($(this).prop("checked")) {
      $(".xcx-id-box").prop("checked", true)
    } else {
      $(".xcx-id-box").prop("checked", false)
      aIds = [];
    }
  })

  // 单个按钮点击事件
  $('.switch_check').click(function () {
    var ids = $(this).parents('.switch_items').attr('data-id');
    if ($(this).prop('checked')) {
      $(this).attr({ 'value': 1, 'checked': true });
      ids = `[${ids}]`
      autoUpdate(ids, 1)
    } else {
      $(this).attr({ 'value': 0, 'checked': false });
      ids = `[${ids}]`
      autoUpdate(ids, 0)
    }
  })

  //开启/关闭自动发布
  $('.btnOpen').click(function () {
    var ids = [];
    $("#allCheck").attr({ 'checked': false })
    $(".xcx-id-box").each(function () {
      if ($(this).prop("checked")) {
        $(this).parents('tr').find('.switch_check').attr({ 'value': 1, 'checked': true })
        ids.push($(this).val());
        $(this).prop("checked", false)
      }
    })
    if (ids.length >= 1) {
      autoUpdate(JSON.stringify(ids), 1)
    }

  })
  $('.btnClose').click(function () {
    var ids = [];
    $("#allCheck").attr({ 'checked': false })
    $(".xcx-id-box").each(function () {
      if ($(this).prop("checked")) {
        $(this).parents('tr').find('.switch_check').attr({ 'value': 0, 'checked': false })
        ids.push($(this).val());
        $(this).prop("checked", false)
      }
    })
    if (ids.length >= 1) {
      autoUpdate(JSON.stringify(ids), 0)
    }
  })


  //自动发布的数据传输
  function autoUpdate(ids, isAuto) {
    $.ajax({
      url: "/staff/xcx/updateDataForBatch",
      type: "GET",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      async: true,
      data: {
        ids: ids,
        isAuto: isAuto
      },
      success: function (res) {
        location.reload()
      },
      error: function (err) {
        tipshow(error, "error")
      }
    })
  }

  //按钮-是否付费
  $(document).on('click', '#isFee,#isFree,#isGive', function () {
    var judgeFree = [];
    var changeLogo = [];
    $(".xcx-id-box").each(function () {
      if ($(this).prop("checked")) {
        judgeFree.push($(this).parents('tr').find('th').attr('data-wid'))
      }
    })
    console.log(judgeFree);
    if (judgeFree.length == 0) {
      return false
    }
    var free_ids = JSON.stringify(judgeFree);
    if ($(this).attr('id') == 'isFee') {
      transFreeAjax(free_ids, 1);
    }
    else if ($(this).attr('id') == 'isFree') {
      transFreeAjax(free_ids, 0);
    }
    else if ($(this).attr('id') == 'isGive') {
      transFreeAjax(free_ids, 2);
    }
    judgeFree = [];
    changeLogo = [];
    return;
  })


  // 付费免费
  function transFreeAjax(ids, isFee) {
    if (ids != '[]' && ids != '') {
      $.ajax({
        url: '/staff/store/updateForFee',
        data: { ids, isFee },
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        dataType: 'json',
        success: function (res) {
          location.reload()
        }
      })
    }
  }

  //查看详情数据
  var this_id = ""; //当前ID 
  var wid = $(this).parents(".tr-id").find(".xcx-id").data("wid");
  $(".see_detail").click(function () {
    // 备注详情tab请求函数 和分页请求的封装 因为回调函数里面不能用递归
    seeRemark($(this), 1)
    const that = $(this)
    $(".page").extendPagination({
      totalCount: globalPage.total,
      showCount: 10,
      limit: globalPage.pageSize,
      callback: function (page) {
        seeRemark(that, page)
      }
    })

    this_id = $(this).data("id"); //当前ID
    wid = $(this).parents(".tr-id").find(".xcx-id").data("wid");

    //查看二维码
    var _url = '/staff/xcx/getQrCode?wid=' + wid;
    $('.see_clic').attr('href', _url)

    var res = $(this).data("ver");
    console.log($(this))
    $(".merchant_name").text(res.merchant_name);   //名称
    $(".request_domain").text(res.request_domain);   //域名名称
    if (res.request_domain) {							//绑定状态							
      $(".res_bind").text("已绑定")
    }
    $(".authorizer_access_token").text(res.authorizer_access_token);   //令牌
    $(".authorizer_expire_time").text(res.authorizer_expire_time);   //令牌过期时间
    $(".authorizer_refresh_token").text(res.authorizer_refresh_token);   //刷新
    $(".res_app_id").text(res.user_name);   //小程序ID
    $(".res_title").text(res.title);   //授权公众号
    $(".func_info_name").text(res.func_info_name);   //授权功能
    if (res.verify_type == -1) {					   //授权方认证				
      $(".verify_type").text("未认证");
    } else if (res.verify_type == 0) {
      $(".verify_type").text("微信认证");
    }
    $(".signature").text(res.signature);   //账号介绍
    $(".request_domain").text(res.request_domain);         //request合法域名
    $(".ws_request_domain").text(res.ws_request_domain);   //socket合法域名
    $(".upload_domain").text(res.upload_domain);           //uploadFile合法域名
    $(".download_domain").text(res.download_domain);       //downloadFile合法域名
    $(".td_reason").text(res.reason);       //原因
    var content = "";
    if (res.category_list) {                                  //服务类目
      var category_list = JSON.parse(res.category_list);
      for (var i = 0; i < category_list.length; i++) {
        content += category_list[i].first_class + "," + category_list[i].second_class + "," + category_list[i].third_class;
        $(".category_list").text(content);   //服务类目
      }
      if (content.indexOf("undefined") > 0) {
        content = content.replace("undefined", "");
      }
      re = new RegExp("undefined", "g"); //定义正则表达式
      content = content.replace(re, "");
      $(".category_list").text(content);
    }
    var numindex = "";
    if (res.page_list) {
      var page_list = JSON.parse(res.page_list);
      for (var i = 0; i < page_list.length; i++) {
        numindex += page_list[i] + "，";
        $(".page_list").text(numindex);   //小程序页面
      }
    }

    $(".principal_name").text(res.principal_name);   //主体名称
    if (res.business_info) {
      var business_info = JSON.parse(res.business_info);
      if (business_info.open_store == 1) {   		 //微信门店功能	
        $(".open_store").text("已开通")
      }
      if (business_info.open_shake == 1) {           //微信扫商品功能
        $(".open_scan").text("已开通")
      }
      if (business_info.open_scan == 1) {           //微信支付功能
        $(".open_pay").text("已开通")
      }
      if (business_info.open_pay == 1) {           //微信卡券功能
        $(".open_card").text("已开通")
      }
      if (business_info.open_card == 1) {           //微信摇一摇功能
        $(".open_shake").text("已开通")
      }
    }
  });
  //点击查看二维码
  $("body").on('click', '.see_clic', function () {
    $.ajax({
      type: "GET",
      url: "/staff/xcx/getQrCode",
      data: {
        wid: wid
      },
      async: false,
      success: function (res) {
        if (res.status == 1) {
          $("#experience_code").attr("href", res)
        } else {
          tipshow(res.info, 'warn')
        }
      },
      error: function () {
        alert("数据访问错误")
      }
    });
  })
})

/*
loading加载状态
*/
var hstool = (function () {
  var hstool = {};
  hstool.config = {//默认配置
    type: 0, //类型 0.msg 1.tips提示框 2.选择商品
    title: '信息', //标题
    opacity: 0.7, //遮罩层透明度
    message: "",
    zIndex: 19891014,
    time: 0, //0表示不自动关闭
    content: "",
    isMask: false, //是否添加点击遮罩层事件 
    done: null, //完成操作的回调函数 
    host: "",//域名
    area: [],//区域 参数 width,height
    skin: "default" //皮肤设定 后期扩展使用
  }
  /*
  * 初始化参数
  */
  hstool.init = function (config) {
    for (var key in config) {
      this.config[key] = config[key];
    }
  }
	/*
    * loading 加载层
    * 
    */
  hstool.load = function (config) {
    config = config || {};
    var that = this;
    that.init(config);
    $("body").append('<div class="all_load"><div class="hstool-dialog-loading"></div></div>');
    $(".all_load").css({ "z-index": that.config.zIndex + 2 });
    if (that.config.time > 0) {
      setTimeout(function () {
        that.closeLoad();
      }, that.config.time);
    }
  }
  /*
  * 关闭加载层 
  */
  hstool.closeLoad = function () {
    $(".all_load").remove();
  }
  return hstool;
})();