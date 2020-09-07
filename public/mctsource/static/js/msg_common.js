// "use strict";
/**
 * 消息模块新增代码
 * @author  txw
 * @date  2017/9/20
 */
$(function () {
    //点击通知按钮
    $(".btn-msg-tx").click(function () {
        $(".noticePanel").removeClass('hide');
    });
    //关闭消息按钮
    $(".noticePanel .noticePanel__title .icon--close").click(function () {
        $(".noticePanel").addClass('hide');
    });
    //点击全部
    $('.mark_read').click(function () {
        var that = this;
        if (!$(that).hasClass('zx-disabled')) {
            $.ajax({
                url: '/merchants/notification/readAllNotification',
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (res.status == 1) {
                        $('.btn-msg-tx').removeClass('active');
                        $('.unread-badge').text('0');
                        var _html = '<div class="noticeItem xiaoxicenter"><img class="xiaoxiimg" src="' + SOURCE_URL + 'mctsource/images/duqu.png"/>'
                        _html += '<div class="colc9 marbom10">暂时没有新通知哦~</div><a class="seehistory" href="/merchants/notification/notificationListView">查看历史消息</a></div>';
                        $(".noticeList").html(_html);
                        tipshow(res.info);
                        $(that).addClass("colc9").css('color', '#C9C9C9');
                        $('.span-msg-tip').html('');
                    } else {
                        tipshow(res.info, 'warm')
                    }
                },
                error: function () {
                    alert('数据访问错误')
                }
            })
        }

    });
    //点击消息设置该消息已读
    $("body").on("click", ".noticeItem__content .msg-detail", function (e) {
        e.stopPropagation();
        var href = $(this).attr("href");
        var id = $(this).attr("data-id");
        $.ajax({
            url: "/merchants/notification/notificationDetail",
            type: "get",
            data: {
                notification_id: id
            },
            dataType: "json",
            success: function (res) {
                if (res.status == 1) {
                    location.href = href;
                }
            }
        });
    });
    if (typeof MSG_HOST != "undefined") {
        msgTool.getMsgCount();
        msgTool.getMsgInfo(MSG_URL);
        msgTool.init({
            msgHost: MSG_HOST,
            wid: MSG_WID,
            port: MSG_PORT
        });
        msgTool.socketFn();
    }
})

/**
 * 公用文件避免耦合
 */
var msgTool = {
    config: {
        msgHost: null, //网站域名
        wid: null, //用户id
        port: null, //websokect 端口号 
    },
    /**
     * 初始化对象属性 
     */
    init: function (config) {
        for (var key in config) {
            this.config[key] = config[key];
        }
    },
    /**
     * webSocket 对象
     */
    socketFn: function () {
        if ("WebSocket" in window) {
            var that = this;
            var url = this.getWebSocketUrl();
            var msg_socket = new WebSocket(url);
            // 监听消息
            msg_socket.onmessage = function (event) {
                that.getMsgCount();
                that.getMsgInfo();
                // console.log(JSON.parse(event.data) ); 
            };
            // 监听Socket的关闭
            msg_socket.onclose = function (event) {
                that.socketFn();
                console.log('Client notified msg_socket has closed', event);
            };
            // 打开Socket 
            msg_socket.onopen = function (event) {
                //msg_socket.send('发送消息');  
            };

            msg_socket.onerror = function (event) {
                console.log("error", event);
            };
        } else {
            console.log("您的浏览器不支持 WebSocket!");
        }
    },
    /**
     * 获取并渲染消息数据  
     */
    getMsgInfo: function (msg_url) {
        //后台点击通知中心->更多,路由会出现undefined,不存在则默认赋值 hsz 2018/6/26
        msg_url = msg_url || '/merchants/notification/notificationListView';
        $.ajax({
            url: "/merchants/notification/getRightNavNotificationList",
            type: "get",
            data: {},
            dataType: "json",
            success: function (res) {
                if (res.status == 1) {
                    var list = res.data.notificationList;
                    var _html = "";
                    var allMsgRead = false; //全部消息已读 状态 true 可点 false 不可点(默认)
                    for (var i = 0; i < list.length; i++) {
                        _html += '<div class="noticeItem"><h4 class="noticeItem__title">' + list[i].title;
                        if (list[i].notificationList.count > 0) {
                            _html += '<span class="noticeItem__badge">' + list[i].notificationList.count + '</span>';
                        }
                        _html += '<a target="_blank" rel="noopener noreferrer" href="' + msg_url + '" class="pull-right noticeItem__more">更多</a></h4>';
                        var arrList = list[i].notificationList.data;
                        if (arrList.length > 0) {
                            _html += '<div class="noticeItem__container"><ul>';
                            for (var j = 0; j < arrList.length; j++) {
                                _html += '<li class="noticeItem__content"><a class="msg-detail" data-id="' + arrList[j].id + '" href="' + arrList[j].redirect_url + '">' + arrList[j].notification_content + '</a></li>';
                            }
                            _html += '</ul></div>';
                            allMsgRead = true;
                        } else {
                            _html += '<div class="noticeItem__content--empty">暂时没有新通知哦~</div>';
                            // $('.mark_read').addClass('colc9').css('color','#C9C9C9')

                            $('.mark_read').addClass('colc9').css('color', '#C9C9C9')
                            //	        				$('.span-msg-tip').html("");
                            //	        				var _html = '<div class="noticeItem xiaoxicenter"><img class="xiaoxiimg" src="'+SOURCE_URL+'mctsource/images/duqu.png"/>'
                            //			        		_html +='<div class="colc9 marbom10">暂时没有新通知哦~</div><a class="seehistory" href="/merchants/notification/notificationListView">查看历史消息</a></div>';
                            //			        		$(".noticeList").html(_html);
                            //                             allMsgRead = false;

                            //                          allMsgRead = false;
                        }
                        _html += '</div>';
                    }
                    $(".noticeList").html(_html);
                    if (!allMsgRead)
                        $(".mark_read").addClass("zx-disabled");
                    else
                        $(".mark_read").removeClass("zx-disabled");
                }
            }
        });
    },
    /**
     * 获取消息数  
     */
    getMsgCount: function () {
        $.ajax({
            url: "/merchants/notification/notificationCount",
            type: "get",
            data: {
                is_read: 0
            },
            dataType: "json",
            success: function (res) {
                if (res.status == 1) {
                    var count = res.data.notificationCount;
                    if (count > 0) {
                        $('.span-msg-tip').html("（" + count + "未读）");
                        var img = "<img src='" + SOURCE_URL + 'mctsource/images/tongzhi_active.png' + "'/>";
                        var span = '<span class="unread-badge">' + count + '</span>';
                        $(".btn-msg-tx").addClass('active').html(img + "通知" + span);
                    } else {
                        $('.span-msg-tip').html("");
                        $(".btn-msg-tx").removeClass('active');
                        $(".btn-msg-tx").find("span").remove();
                        $(".btn-msg-tx").append("<img src='" + SOURCE_URL + 'mctsource/images/tongzhi.png' + "'/><span>通知</span>");
                    }
                }
            }
        });
    },
    /**
     * 获取websocket 的地址
     * @return webSocket 请求地址
     */
    getWebSocketUrl: function () {
        //var socketUrl = this.config.msgHost.split('//')[1].split('/')[0] + ":" + this.config.port; 
        var socketUrl = this.config.msgHost.split('//')[1].split('/')[0];
        return 'wss:' + socketUrl + '/websocket/subscribe/message?user=' + this.config.wid;
    }
}