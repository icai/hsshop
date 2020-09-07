define("js/index", [],
function(e) {
    var t, i, a, n, o, s, r, l, c;
    i = e("wyaoqing.create/client.v3/subject/func"),
    l = e("wyaoqing.create/client.v3/subject/render"),
    n = e("wyaoqing.create/client.v3/subject/page"),
    c = e("wyaoqing.create/client.v3/plugins/report").init(),
    a = e("wyaoqing.create/client.v3/plugins/orientation"),
    t = global.service + "/show/preview/pageInfo?campaign_id=" + campaign_id,
    s = [],
    r = [],
    global.my_render = null,
    global.my_func = null,
    global.my_page = null,
    o = scalePage(),
    e = {
            "code": 1,
            "message": "操作成功",
            "data": {
                "1": {
                    "attr": {
                        "bg": "266710_o_1bl0mnv1k1hgv1sn1nsshi71v9tr"
                    },
                    "pageEffect": [],
                    "elements": [
                        {
                            "content": "<p style=\"text-align: center;\"><font size=\"5\" color=\"#ffffff\"><span style=\"line-height: 30px;\">宁海站</span></font></p>",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "23px",
                                "top": "345px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "280px",
                                "zIndex": "1",
                                "borderRadius": "0px",
                                "borderColor": "#000000",
                                "padding": "3px",
                                "height": "5px"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "text",
                                "widget_id": "9668234",
                                "id": "419297_1495619_9668234"
                            },
                            "type": "0",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "zoom",
                                "name": "zoom-in",
                                "duration": "1s",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        },
                        {
                            "content": "{\"css\":{\"width\":\"335px\",\"height\":\"485.08px\",\"overflow\":\"hidden\",\"borderRadius\":\"0px\",\"marginLeft\":\"0px\",\"display\":\"block\",\"marginTop\":\"0px\",\"top\":\"-0.01371173469385667px\"},\"attr\":{\"image_width\":\"750\",\"image_height\":\"1086\"},\"src\":\"266710_o_1bl0m168i4hf1o691lub1fruffg10\"}",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "0px",
                                "top": "0px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "335px",
                                "zIndex": "0",
                                "height": "485.08px",
                                "borderRadius": "0px",
                                "borderColor": "#000000"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px",
                                "overflow": "hidden"
                            },
                            "attr": {
                                "value": "",
                                "type": "image",
                                "widget_id": "9673357",
                                "id": "419297_1495619_9673357"
                            },
                            "type": "1",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "fade",
                                "name": "none",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        }
                    ]
                },
                "2": {
                    "attr": {
                        "bg": "266710_o_1bl0m0gg61om8rqkkn91epd1kh5r"
                    },
                    "pageEffect": [],
                    "elements": [
                        {
                            "content": "{\"css\":{\"width\":\"335px\",\"height\":\"485.40816326530614px\",\"overflow\":\"hidden\",\"borderRadius\":\"0px\",\"marginLeft\":\"0px\",\"display\":\"block\",\"marginTop\":\"0px\"},\"attr\":{\"image_width\":\"1105\",\"image_height\":\"1600\"},\"src\":\"266710_o_1bl0mb6lnvj01vp9gmg1msi17ca1a\"}",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "0px",
                                "top": "0px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "335px",
                                "zIndex": "1",
                                "height": "485.40816326530614px",
                                "borderRadius": "0px",
                                "borderColor": "#000000"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px",
                                "overflow": "hidden"
                            },
                            "attr": {
                                "value": "",
                                "type": "image",
                                "widget_id": "9673416",
                                "id": "419297_1496410_9673416"
                            },
                            "type": "1",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "fade",
                                "name": "none",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        }
                    ]
                },
                "3": {
                    "attr": {
                        "bg": "266710_o_1bl0m0gg61om8rqkkn91epd1kh5r"
                    },
                    "pageEffect": [],
                    "elements": [
                        {
                            "content": "{\"css\":{\"width\":\"335px\",\"height\":\"485.08px\",\"overflow\":\"hidden\",\"borderRadius\":\"0px\",\"marginLeft\":\"0px\",\"display\":\"block\",\"marginTop\":\"0px\"},\"attr\":{\"image_width\":\"750\",\"image_height\":\"1086\"},\"src\":\"266710_o_1bl0m9mulhng7vo180jib21hkk15\"}",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "0px",
                                "top": "0px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "335px",
                                "zIndex": "1",
                                "height": "485.08px",
                                "borderRadius": "0px",
                                "borderColor": "#000000"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px",
                                "overflow": "hidden"
                            },
                            "attr": {
                                "value": "",
                                "type": "image",
                                "widget_id": "9673364",
                                "id": "419297_1495620_9673364"
                            },
                            "type": "1",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "fade",
                                "name": "none",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        }
                    ]
                },
                "4": {
                    "attr": {
                        "bg": "266710_o_1bl0m0gg61om8rqkkn91epd1kh5r"
                    },
                    "pageEffect": [],
                    "elements": [
                        {
                            "content": "<p style=\"text-align: center;\"><font color=\"#ffffff\" size=\"3\">|会议聚焦|</font></p>",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "0px",
                                "top": "2px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "320px",
                                "zIndex": "1",
                                "borderRadius": "0px",
                                "height": "40px",
                                "borderColor": "#000000",
                                "padding": "0px"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "text",
                                "widget_id": "9668237",
                                "id": "419297_1495621_9668237"
                            },
                            "type": "0",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "fade",
                                "name": "none",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        },
                        {
                            "content": "<p><font color=\"#ffffff\" size=\"3\"><span style=\"line-height: 22.5px;\">1.截止目前为止，微信注册用户超过10亿，日活跃用户超过了8亿，微信已经成为互联网的超级入口。</span></font></p>",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "17px",
                                "top": "40px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "290px",
                                "zIndex": "2",
                                "borderRadius": "0px",
                                "height": "40px",
                                "borderColor": "#000000",
                                "padding": "0px"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "text",
                                "widget_id": "9668238",
                                "id": "419297_1495621_9668238"
                            },
                            "type": "0",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "bounce",
                                "name": "bounce-in-right-big",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        },
                        {
                            "content": "<p><font color=\"#ffffff\" size=\"3\"><span style=\"line-height: 22.5px;\">2.手机用户每天使用微信的习惯占用手机上网时间的70%，所以用户在哪，用户的习惯在哪，企业的生意机会就会在哪！2.手机用户每天使用微信的习惯占用手机上网时间的70%，所以用户在哪，用户的习惯在哪，企业的生意机会就会在哪！</span></font></p>",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "15px",
                                "top": "125px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "290px",
                                "zIndex": "3",
                                "borderRadius": "0px",
                                "height": "40px",
                                "borderColor": "#000000",
                                "padding": "0px"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "text",
                                "widget_id": "9668239",
                                "id": "419297_1495621_9668239"
                            },
                            "type": "0",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "fade",
                                "name": "fade-in-left-big",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        },
                        {
                            "content": "<p><font color=\"#ffffff\" size=\"3\"><span style=\"line-height: 22.5px;\">3.线上线下将成为中小企业成长，突破瓶颈的创新模式 </span></font></p>",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "15px",
                                "top": "272px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "278px",
                                "zIndex": "4",
                                "borderRadius": "0px",
                                "height": "40px",
                                "borderColor": "#000000",
                                "padding": "0px"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "text",
                                "widget_id": "9668240",
                                "id": "419297_1495621_9668240"
                            },
                            "type": "0",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "fade",
                                "name": "fade-in-right-big",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        },
                        {
                            "content": "<p><font color=\"#ffffff\" size=\"3\"><span style=\"line-height: 22.5px;\">4.在当今粉丝经济，互享经济新时代下，所有的传统企业都将是一家移动互联网公司。</span></font></p>",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "12px",
                                "top": "327px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "292px",
                                "zIndex": "5",
                                "borderRadius": "0px",
                                "height": "55px",
                                "borderColor": "#000000",
                                "padding": "0px"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "text",
                                "widget_id": "9668241",
                                "id": "419297_1495621_9668241"
                            },
                            "type": "0",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "fade",
                                "name": "fade-in-left-big",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        },
                        {
                            "content": "<p><font color=\"#ffffff\" size=\"3\"><span style=\"line-height: 22.5px;\">5.对于企业和个人，我们将如何把握新一轮微信电商红利。</span></font></p>",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "12px",
                                "top": "404px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "292px",
                                "zIndex": "6",
                                "borderRadius": "0px",
                                "height": "46px",
                                "borderColor": "#000000",
                                "padding": "0px"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "text",
                                "widget_id": "9668242",
                                "id": "419297_1495621_9668242"
                            },
                            "type": "0",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "fade",
                                "name": "fade-in-right-big",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        }
                    ]
                },
                "5": {
                    "attr": {
                        "bg": "266710_o_1bl0m0gg61om8rqkkn91epd1kh5r"
                    },
                    "pageEffect": [],
                    "elements": [
                        {
                            "content": "<p style=\"text-align: center;\"><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\">|小程序入口|</font></span></p><p><br></p>",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "0px",
                                "top": "5px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "320px",
                                "zIndex": "1",
                                "borderRadius": "0px",
                                "height": "40px",
                                "borderColor": "#000000",
                                "padding": "0px"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "text",
                                "widget_id": "9668248",
                                "id": "419297_1495623_9668248"
                            },
                            "type": "0",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "fade",
                                "name": "none",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        },
                        {
                            "content": "{\"css\":{\"width\":\"320px\",\"height\":\"213.49999999999997px\",\"overflow\":\"hidden\",\"borderRadius\":\"0px\",\"marginLeft\":\"0px\",\"display\":\"block\",\"marginTop\":\"0px\",\"left\":\"0px\"},\"attr\":{\"image_width\":\"640\",\"image_height\":\"427\"},\"src\":\"266710_o_1bk3rmrn2k91jkj2rs1r751cfrr\"}",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "0px",
                                "top": "30px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "320px",
                                "zIndex": "2",
                                "height": "213.49999999999997px",
                                "borderRadius": "0px",
                                "borderColor": "#000000"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px",
                                "overflow": "hidden"
                            },
                            "attr": {
                                "value": "",
                                "type": "image",
                                "widget_id": "9668249",
                                "id": "419297_1495623_9668249"
                            },
                            "type": "1",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "bounce",
                                "name": "bounce-in-down-big",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        },
                        {
                            "content": "{\"css\":{\"width\":\"320px\",\"height\":\"273.5407407407408px\",\"overflow\":\"hidden\",\"borderRadius\":\"0px\",\"marginLeft\":\"0px\",\"display\":\"block\",\"marginTop\":\"0px\",\"top\":\"211.703125px\",\"left\":\"0px\"},\"attr\":{\"image_width\":\"675\",\"image_height\":\"577\"},\"src\":\"266710_o_1bk3sfooh174sqi91j3j1bokca510\"}",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "0px",
                                "top": "211px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "320px",
                                "zIndex": "3",
                                "height": "273.5407407407408px",
                                "borderRadius": "0px",
                                "borderColor": "#000000"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px",
                                "overflow": "hidden"
                            },
                            "attr": {
                                "value": "",
                                "type": "image",
                                "widget_id": "9668250",
                                "id": "419297_1495623_9668250"
                            },
                            "type": "1",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "bounce",
                                "name": "bounce-in-up-big",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        }
                    ]
                },
                "6": {
                    "attr": {
                        "bg": "266710_o_1bl0m0gg61om8rqkkn91epd1kh5r"
                    },
                    "pageEffect": [],
                    "elements": [
                        {
                            "content": "<p style=\"text-align: center;\"><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\">|会议内容|</font></span></p><p><br></p>",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "0px",
                                "top": "5px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "320px",
                                "zIndex": "1",
                                "borderRadius": "0px",
                                "height": "40px",
                                "borderColor": "#000000",
                                "padding": "0px"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "text",
                                "id": "419297_1495624_9668251",
                                "widget_id": "9668251"
                            },
                            "type": "0",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "fade",
                                "name": "none",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        },
                        {
                            "content": "<p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\">1.微信小程序到底是什么？\r</font></span></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\"><br></font></span></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\"><br></font></span></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\">2.微信小程序适合什么产品？\r</font></span></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\"><br></font></span></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\"><br></font></span></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\">3</font></span><span style=\"font-size: medium; color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\">.小程序带来的创业投资机会是什么？ </span></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\"><br></font></span></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\"><br></font></span></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\">4.小程序会是商业服务移动化最后一站？\r</font></span></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\"><br></font></span></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\"><br></font></span></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\">5.小程序可以替代APP吗？\r</font></span></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\"><br></font></span></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\"><br></font></span></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\">6.企业如何完美对接小程序参与千亿市场？  </font></span></p><p><br></p>",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "26px",
                                "top": "81px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "294px",
                                "zIndex": "2",
                                "borderRadius": "0px",
                                "height": "40px",
                                "borderColor": "#000000",
                                "padding": "0px"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "text",
                                "id": "419297_1495624_9668252",
                                "widget_id": "9668252"
                            },
                            "type": "0",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "fade",
                                "name": "fade-in-left",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        }
                    ]
                },
                "7": {
                    "attr": {
                        "bg": "266710_o_1bl0m0gg61om8rqkkn91epd1kh5r"
                    },
                    "pageEffect": [],
                    "elements": [
                        {
                            "content": "<p style=\"text-align: center;\"><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\">|注意事项|</font></span></p><p><br></p>",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "0px",
                                "top": "7px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "320px",
                                "zIndex": "1",
                                "borderRadius": "0px",
                                "height": "40px",
                                "borderColor": "#000000",
                                "padding": "0px"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "text",
                                "id": "419297_1495625_9668253",
                                "widget_id": "9668253"
                            },
                            "type": "0",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "fade",
                                "name": "none",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        },
                        {
                            "content": "<p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\">1.每家企业仅限1个名额（企业法人或创始人或股东将营业执照拍照保存至手机。；另报名之后请联系相关负责人员对接事宜） \r</font></span></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\"><br></font></span></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\"><br></font></span></p><p><br></p><p><br></p>",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "31px",
                                "top": "79px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "268px",
                                "zIndex": "2",
                                "borderRadius": "0px",
                                "height": "40px",
                                "borderColor": "#000000",
                                "padding": "0px"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "text",
                                "id": "419297_1495625_9668254",
                                "widget_id": "9668254"
                            },
                            "type": "0",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "other",
                                "name": "shake-y",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        },
                        {
                            "content": "<p><br></p><p><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\">2.候选企业接收到通知文函请填写完整信息提交。并于当天下午17点前提交回执，以便安排席位。\r</font></span></p><p><br></p>",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "30px",
                                "top": "191px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "256px",
                                "zIndex": "3",
                                "borderRadius": "0px",
                                "height": "40px",
                                "borderColor": "#000000",
                                "padding": "0px"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "text",
                                "id": "419297_1495625_9668255",
                                "widget_id": "9668255"
                            },
                            "type": "0",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "other",
                                "name": "shake-y",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        },
                        {
                            "content": "<p><span style=\"line-height: 20px; color: rgb(255, 255, 255); font-size: medium; background-color: initial;\"></span><br></p><p><font color=\"#ffffff\" size=\"3\"><span style=\"line-height: 20px;\">3签到前请出示：名片、签到编号（会议前一天由会务提供）即可</span></font></p>",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "28px",
                                "top": "314px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "255px",
                                "zIndex": "4",
                                "borderRadius": "0px",
                                "height": "53px",
                                "borderColor": "#000000",
                                "padding": "0px"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "text",
                                "id": "419297_1495625_9668256",
                                "widget_id": "9668256"
                            },
                            "type": "0",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "other",
                                "name": "shake-y",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        }
                    ]
                },
                "8": {
                    "attr": {
                        "bg": "266710_o_1bl0m0gg61om8rqkkn91epd1kh5r"
                    },
                    "pageEffect": [],
                    "elements": [
                        {
                            "content": "<p style=\"text-align: center;\"><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"3\">|会议地址|</font></span></p><p><br></p>",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "0px",
                                "top": "1px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "320px",
                                "zIndex": "1",
                                "borderRadius": "0px",
                                "height": "40px",
                                "borderColor": "#000000",
                                "padding": "0px"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "text",
                                "widget_id": "9668257",
                                "id": "419297_1495626_9668257"
                            },
                            "type": "0",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "fade",
                                "name": "none",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        },
                        {
                            "content": "<img src=\"http://img.wyaoqing.com/1_o_19j3bjchmneni9366r191obakm\" style=\"width:100%;height:100%\">",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "26px",
                                "top": "122px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "268px",
                                "zIndex": "2",
                                "height": "183px",
                                "borderRadius": "0px",
                                "borderColor": "#000000"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px",
                                "overflow": "hidden",
                                "height": "100%"
                            },
                            "attr": {
                                "value": "",
                                "type": "map",
                                "widget_id": "9668258",
                                "id": "419297_1495626_9668258"
                            },
                            "type": "7",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "bounce",
                                "name": "none",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        },
                        {
                            "content": "<p><font color=\"#ffffff\" size=\"3\"><span style=\"line-height: 20px;\">会议时间：2017年8月20日\n</span></font></p><p><font color=\"#ffffff\" size=\"3\"><span style=\"line-height: 20px;\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;（周日）下午13:00签到 \n</span></font></p><p><font color=\"#ffffff\" size=\"3\"><span style=\"line-height: 20px;\"><br></span></font></p><p><font color=\"#ffffff\" size=\"3\"><span style=\"line-height: 20px;\">会议地址：金水路355号</span></font></p><p><font color=\"#ffffff\" size=\"3\"><span style=\"line-height: 20px;\">                               近宁海国际会展中心</span></font></p>",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "8px",
                                "top": "361px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "312px",
                                "zIndex": "3",
                                "borderRadius": "0px",
                                "height": "41px",
                                "borderColor": "#000000",
                                "padding": "0px"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "text",
                                "widget_id": "9668259",
                                "id": "419297_1495626_9668259"
                            },
                            "type": "0",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "bounce",
                                "name": "none",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        },
                        {
                            "content": "<p style=\"text-align: center;\"><span style=\"color: rgb(255, 255, 255); line-height: 1.25; background-color: initial;\"><font size=\"5\">|宁海富泉美悦酒店|</font></span></p><p><br></p>",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "0px",
                                "top": "57px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "320px",
                                "zIndex": "4",
                                "borderRadius": "0px",
                                "height": "40px",
                                "borderColor": "#000000",
                                "padding": "0px"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "text",
                                "widget_id": "9668260",
                                "id": "419297_1495626_9668260"
                            },
                            "type": "0",
                            "event": {
                                "name": "none",
                                "value": ""
                            },
                            "effect": {
                                "type": "bounce",
                                "name": "none",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": []
                        }
                    ]
                },
                "9": {
                    "attr": {
                        "bg": "266710_o_1bl0m0gg61om8rqkkn91epd1kh5r"
                    },
                    "pageEffect": [],
                    "elements": [
                        {
                            "content": "立即报名",
                            "css": {
                                "position": "absolute",
                                "opacity": "1",
                                "left": "77px",
                                "top": "207px",
                                "borderWidth": "0px",
                                "borderStyle": "solid",
                                "width": "165px",
                                "zIndex": "1",
                                "height": "40px",
                                "backgroundColor": "rgba(48, 90, 249, 0.8)",
                                "fontSize": "16px",
                                "borderRadius": "40px",
                                "color": "rgb(252, 251, 255)",
                                "borderColor": "#000000"
                            },
                            "element_css": {
                                "borderRadius": "0px",
                                "padding": "0px"
                            },
                            "attr": {
                                "value": "",
                                "type": "button",
                                "id": "419297_1495627_9668261",
                                "widget_id": "9668261"
                            },
                            "type": "2",
                            "event": {
                                "name": "form",
                                "value": ""
                            },
                            "effect": {
                                "type": "other",
                                "name": "tada",
                                "duration": "1",
                                "delay": "0",
                                "infinite": "false"
                            },
                            "layout": [
                                "xCenter"
                            ]
                        }
                    ]
                }
            },
            "url": "",
            "success": true
        }
        var t, a, s;
        null == e.data && (e.data = {}),
        t = {},
        s = 1;
        for (a in e.data) t[s] = e.data[a],
        s++;
        return global.my_page = n,
        global.my_render = new l(t, o),
        global.my_func = new i,
        global.my_render.init(),
        global.my_func.initDelimiter(),
        setTimeout(function() {
            return $("#loading").hide(),
            setTimeout(function() {
                return global.my_func.run(0)
            },
            500)
        },
        2e3),
        n.init()
    // $.get(t,
    // function(e) {
        
    // })
}),
define("wyaoqing.create/client.v3/subject/func", [],
function(e, t, i) {
    var a;
    a = function() {
        function e() {
            this._data = {}
        }
        return e.prototype.register = function(e, t, i) {
            var a, n, o, s, r;
            for (null == (n = this._data)[e] && (n[e] = []), r = this._data[e], o = 0, s = r.length; s > o; o++) if (a = r[o], a.id === t) {
                this._data[e] = [];
                break
            }
            return this._data[e].push({
                id: t,
                effect: i
            })
        },
        e.prototype.init = function(e) {
            var t, i, a, n, o;
            if (null != this._data[e]) {
                for (i = this._data[e], o = [], a = 0, n = i.length; n > a; a++) t = i[a],
                "none" !== t.effect.name ? (animateEnd("#" + t.id), o.push($("#" + t.id).addClass("animate-out"))) : o.push(void 0);
                return o
            }
        },
        e.prototype.initDelimiter = function(e) {
            var t, i, a;
            return null == e && (e = null),
            null == e ? (this.init(1), this.init(2)) : (a = global.pageOrderMap[e], null != this._data[a] && (this.init(a + 1 > global.my_render.maxOrder ? a + 1 - global.my_render.maxOrder: a + 1), this.init(a - 1 > 0 ? a - 1 : global.my_render.maxOrder + (a - 1))), global.my_render.maxOrder > 5 ? (t = a - 2 > 0 ? a - 2 : global.my_render.maxOrder + (a - 2), i = a + 2 > global.my_render.maxOrder ? a + 2 - global.my_render.maxOrder: a + 2, global.my_render.renderPage(i, $(".page").index($(".page").eq(e + 2 - 5))), global.my_render.renderPage(t, $(".page").index($(".page").eq(e - 2)))) : void 0)
        },
        e.prototype.getMaxDelay = function(e) {
            var t, i, a, n, o, s, r, l;
            for (n = null != this._data[e] ? this._data[e] : [], o = 0, s = 0, r = 0, l = n.length; l > r; r++) a = n[r],
            i = a.effect,
            t = parseFloat(i.delay),
            s = parseFloat(i.duration),
            o = t > o ? t: o;
            return o + s
        },
        e.prototype.run = function(e) {
            var t, i, a, n, o, s, r;
            for (n = global.pageOrderMap[e], a = null != this._data[n] ? this._data[n] : [], r = [], o = 0, s = a.length; s > o; o++) i = a[o],
            t = i.effect,
            t.infinite = "false" === t.infinite ? !1 : t.infinite,
            "none" === t.name ? r.push($("#" + i.id).removeClass("animate-out")) : (animate("#" + i.id, t.name, parseFloat(t.duration) + "s", 1e3 * parseFloat(t.delay), t.infinite), r.push(i.id));
            return r
        },
        e
    } (),
    i.exports = a
}),
define("wyaoqing.create/client.v3/subject/render", [],
function(e, t, i) {
    var a, n, o, s, r, l, c = {}.hasOwnProperty,
    d = function(e, t) {
        function i() {
            this.constructor = e
        }
        for (var a in t) c.call(t, a) && (e[a] = t[a]);
        return i.prototype = t.prototype,
        e.prototype = new i,
        e.__super__ = t.prototype,
        e
    };
    l = e("wyaoqing.create/public/widgets"),
    s = e("wyaoqing.create/client.v3/subject/layout"),
    o = e("wyaoqing.create/client.v3/subject/event"),
    a = e("wyaoqing.create/client.v3/plugins/smear"),
    n = e("wyaoqing.create/client.v3/plugins/ad"),
    r = function(e) {
        function t(e, i) {
            var a, n;
            this.data = e,
            this.pageContentPosition = i,
            n = function() {
                var e;
                e = [];
                for (a in this.data) e.push(a);
                return e
            }.call(this),
            this.maxOrder = Math.max.apply(this, n),
            this.event = new o(this.data),
            t.__super__.constructor.call(this, this.data, this.pageContentPosition),
            this.container = $(".page-container")
        }
        return d(t, e),
        t.prototype.renderDom = function(e, t, i) {
            var a, o, s, r, c, d, p, g, u, f, h;
            if (e = e % this.maxOrder === 0 ? this.maxOrder: e % this.maxOrder, s = this.data[e], o = i ? "page" + (e - 1) : "", i) d = $('<div class="page ' + o + '"></div>'),
            1 === parseInt(e) && d.addClass("p-current"),
            global.pageOrderMap[e - 1] = e;
            else if (d = $(".page" + t), global.pageOrderMap[t] = e, $(".order" + e).size() > 0) return d.find(".pageContainer").appendTo($("#recycle")),
            void $(".order" + e).appendTo(d);
            r = $("<div class='pageContainer order" + e + "'>"),
            c = $("<div class='page-content'>").css({
                marginLeft: this.pageContentPosition.left,
                marginTop: this.pageContentPosition.top
            }),
            r.append(c),
            $.isPlainObject(s.attr) || (s.attr = {}),
            r.addClass("order" + e).css("bg" in s.attr ? {
                background: "url(" + global.img_domain + "/" + s.attr.bg + ") no-repeat",
                backgroundSize: "cover",
                backgroundPosition: "50% 50%"
            }: {
                backgroundColor: "#fff"
            }),
            h = s.elements;
            for (u = 0, f = h.length; f > u; u++) {
                switch (a = h[u], p = l[a.type].template, g = $(p).attr(a.attr).addClass("widget_" + a.attr.id).css(a.css), a.attr.type) {
                case "text":
                    g.find(".element").html(a.content).css(a.element_css);
                    break;
                case "button":
                    g.find(".element").html(a.content);
                    break;
                case "image":
                    try {
                        a.content = $.isPlainObject(a.content) ? a.content: $.parseJSON(a.content),
                        g.find(".element").css(a.element_css).css({
                            height: "100%"
                        }).html($("<img src='" + global.img_domain + "/" + a.content.src + "' />").css(a.content.css))
                    } catch(m) {
                        a.content = null
                    }
                    break;
                case "map":
                    g.find(".element").css(a.element_css).addClass("widget_map").attr("id", "map_" + a.attr.id);
                    break;
                case "video":
                    g.find(".element").css(a.element_css).addClass("widget_video").attr("id", "video_" + a.attr.id),
                    g.find(".element").html(a.content),
                    g.find("iframe").css({
                        width: "100%",
                        height: "100%"
                    })
                }
                "none" === a.effect.name && g.removeClass("animate-out"),
                g.appendTo(c),
                null != a.effect && global.my_func.register(e, a.attr.id, a.effect)
            }
            return i ? this.container.append(d.append(r)) : (d.find(".pageContainer").appendTo($("#recycle")), d.html(r)),
            (global.page_setting.loop || !global.page_setting.loop && this.maxOrder !== parseInt(e)) && ("vertical" === global.page_setting.direction ? c.after('<div class="up"></div>') : "horizontal" === global.page_setting.direction && c.after('<div class="left"></div>')),
            this.maxOrder === parseInt(e) && (c.after($("#report").show()), "1" === global.is_show_ad) ? n(this.maxOrder) : void 0
        },
        t.prototype.renderMap = function(e) {
            var t, i, a, n, o, s, r, l, c, d, p;
            for (n = this.data[e], d = n.elements, p = [], l = 0, c = d.length; c > l; l++) t = d[l],
            "map" === t.attr.type ? (i = new BMap.Map("map_" + t.attr.id), i.enableScrollWheelZoom(), s = global.map.x, r = global.map.y, i.centerAndZoom(new BMap.Point(s, r), 17), i.addControl(new BMap.NavigationControl), i.removeOverlay(a), o = new BMap.Point(s, r), a = new BMap.Marker(o), p.push(i.addOverlay(a))) : p.push(void 0);
            return p
        },
        t.prototype.renderPageEffect = function(e, t) {
            var i, n, o, s, r;
            if (i = this.data[e], $.isPlainObject(i.pageEffect) || (i.pageEffect = {}), "name" in i.pageEffect) switch (i.pageEffect.name) {
            case "smear":
                return o = $("<div class='layer smear" + e + "' id='smear" + e + "' style=''></div>").css("zIndex", 301),
                t.append(o),
                r = this.container.width(),
                s = this.container.height(),
                o.on("touchstart mousedown",
                function(e) {
                    return function(t) {
                        return $("#smearTip" + e).hide(),
                        t.stopPropagation()
                    }
                } (e)),
                n = new a(o.get(0), i.pageEffect.src, "image", r, s,
                function(e, t) {
                    return function(i) {
                        return i > 50 ? (t.hide(), global.my_func.run(e)) : void 0
                    }
                } (e, o)),
                n.init("", "", "absolute", i.pageEffect.percent),
                i.pageEffect.tip && t.append("<div id='smearTip" + e + "' style='width:100%;text-align:center;font-size:24px;color:#fff;position: absolute;left:0;bottom:10%;z-index:302;'><span style='background:rgba(0,0,0,.8);padding: 5px; border-radius: 5px;'>" + i.pageEffect.tip + "</span></div>")
            }
        },
        t.prototype.init = function() {
            var e, t;
            t = [];
            for (e in this.data) t.push(5 >= e ? this.renderPage(e, !1, !0) : void 0);
            return t
        },
        t.prototype.renderPage = function(e, t, i) {
            return null == t && (t = !1),
            null == i && (i = !1),
            this.data[e] ? (this.renderDom(e, t, i), this.renderMap(e), this.renderPageEffect(e, this.container.find(".order" + e)), this.layoutPage(e), this.event.eventPage(e)) : void 0
        },
        t
    } (s),
    i.exports = r
}),
define("wyaoqing.create/public/widgets", [],
function(e, t, i) {
    i.exports = {
        0 : {
            name: "text",
            template: '<div class="inside"><div class="element"></div></div>'
        },
        1 : {
            name: "image",
            template: '<div class="inside"><div class="element"></div></div>'
        },
        2 : {
            name: "button",
            template: '<div class="inside"><div class="comp_button"><div class="table_row"><div class="table_cell element"></div></div></div></div>'
        },
        3 : {
            name: "input",
            template: '<div class="inside"><input type="text" class="element" /></div>'
        },
        4 : {
            name: "bg",
            template: ""
        },
        5 : {
            name: "music",
            template: ""
        },
        6 : {
            name: "effect",
            template: ""
        },
        7 : {
            name: "map",
            template: '<div class="inside"><div class="element"></div></div>'
        },
        8 : {
            name: "video",
            template: '<div class="inside"><div class="element"></div></div>'
        }
    }
}),
define("wyaoqing.create/client.v3/subject/layout", [],
function(e, t, i) {
    var a;
    a = function() {
        function e(e, t) {
            this.data = e,
            this.pageContentPosition = t
        }
        return e.prototype.layoutPage = function(t) {
            var i, a, n, o, s, r, l, c, d, p;
            if (s = this.data[t]) {
                for (d = s.elements, p = [], l = 0, c = d.length; c > l; l++) i = d[l],
                p.push($.isEmptyObject(i.layout) ? void 0 : function() {
                    var t, s, l, c;
                    for (l = i.layout, c = [], t = 0, s = l.length; s > t; t++) switch (e = l[t]) {
                    case "left":
                        c.push($("#" + i.attr.id).css({
                            left: -this.pageContentPosition.left
                        }));
                        break;
                    case "right":
                        c.push($("#" + i.attr.id).css({
                            left: "auto",
                            right: -this.pageContentPosition.left
                        }));
                        break;
                    case "top":
                        c.push($("#" + i.attr.id).css({
                            top: -this.pageContentPosition.top
                        }));
                        break;
                    case "bottom":
                        c.push($("#" + i.attr.id).css({
                            top: "auto",
                            bottom: -this.pageContentPosition.top
                        }));
                        break;
                    case "xCenter":
                        r = parseFloat(i.css.width),
                        n = isNaN(r) ? $("#" + i.attr.id).width() : r,
                        c.push($("#" + i.attr.id).css({
                            left: (this.pageContentPosition.width - n) / 2 - this.pageContentPosition.left
                        }));
                        break;
                    case "yCenter":
                        o = parseFloat(i.css.height),
                        a = isNaN(o) ? $("#" + i.attr.id).height() : o,
                        c.push($("#" + i.attr.id).css({
                            top: (this.pageContentPosition.height - a) / 2 - this.pageContentPosition.top
                        }));
                        break;
                    case "xAjust":
                        $("#" + i.attr.id).css({
                            left: -this.pageContentPosition.left,
                            width: this.pageContentPosition.width
                        }),
                        c.push("1" === i.type ? $("#" + i.attr.id).css({
                            height: ""
                        }).find(".element").css({
                            overflow: ""
                        }).find("img").css({
                            width: "100%",
                            height: "auto"
                        }) : void 0);
                        break;
                    case "yAjust":
                        $("#" + i.attr.id).css({
                            top: -this.pageContentPosition.top,
                            height: this.pageContentPosition.height
                        }),
                        c.push("1" === i.type ? $("#" + i.attr.id).css({
                            width: ""
                        }).find(".element").css("overflow", "").find("img").css({
                            width: "auto",
                            height: "100%"
                        }) : void 0);
                        break;
                    case "ajust":
                        $("#" + i.attr.id).css({
                            top: -this.pageContentPosition.top,
                            left: -this.pageContentPosition.left,
                            width: this.pageContentPosition.width,
                            height: this.pageContentPosition.height
                        }),
                        c.push("1" === i.type ? $("#" + i.attr.id).find(".element").css("overflow", "").find("img").css({
                            width: "100%",
                            height: "100%"
                        }) : void 0);
                        break;
                    default:
                        c.push(void 0)
                    }
                    return c
                }.call(this));
                return p
            }
        },
        e
    } (),
    i.exports = a
}),
define("wyaoqing.create/client.v3/subject/event", [],
function(e, t, i) {
    var a, n;
    n = e("wyaoqing.create/client.v3/plugins/formDialog3"),
    a = function() {
        function e(e) {
            this.data = e
        }
        return e.prototype._getDistance = function(e, t, i) {
            var a, n, o, s;
            for (a = 0, n = o = e; i >= e ? i >= o: o >= i; n = i >= e ? ++o: --o) {
                if (n === t) return a;
                a++
            }
            for (n = s = 1; e >= 1 ? e >= s: s >= e; n = e >= 1 ? ++s: --s) {
                if (n === t) return a;
                a++
            }
            return a
        },
        e.prototype._getPage = function(e, t, i) {
            var a, n;
            for (a = n = t; i >= t ? i >= n: n >= i; a = i >= t ? ++n: --n) {
                if (0 === e) return a;
                e--
            }
            return this._getPage(e, 0, i)
        },
        e.prototype.eventPage = function(e) {
            var t, i, a, o, s, r, l;
            if (i = this.data[e]) {
                for (s = i.elements, r = [], a = 0, o = s.length; o > a; a++) switch (t = s[a], t.event.name) {
                case "link":
                    r.push($("#" + t.attr.id).attr({
                        event_name: t.event.name,
                        event_value: t.event.value
                    }).unbind().click(function() {
                        return window.location.href = $(this).attr("event_value")
                    }));
                    break;
                case "phone":
                    r.push($("#" + t.attr.id).attr({
                        event_name: t.event.name,
                        event_value: t.event.value
                    }).unbind().click(function() {
                        return window.location.href = "tel:" + $(this).attr("event_value")
                    }));
                    break;
                case "email":
                    r.push($("#" + t.attr.id).attr({
                        event_name: t.event.name,
                        event_value: t.event.value
                    }).unbind().click(function() {
                        return window.location.href = "mailto:" + $(this).attr("event_value")
                    }));
                    break;
                case "form":
                    r.push(new n("#" + t.attr.id));
                    break;
                case "page":
                    l = this,
                    r.push($("#" + t.attr.id).attr({
                        event_name: t.event.name,
                        event_value: t.event.value
                    }).unbind().click(function() {
                        var t, a, n, o, s, r, l;
                        return t = $(".p-current"),
                        i = $(".page").index(t),
                        e = global.pageOrderMap[i],
                        a = parseInt($(this).attr("event_value")),
                        global.my_render.maxOrder > 5 ? (l = $(".page").index($(".page").eq(i + 1 - 5)), n = a - 1 > 0 ? a - 1 : global.my_render.maxOrder + (a - 1), o = a + 1 > global.my_render.maxOrder ? a + 1 - global.my_render.maxOrder: a + 1, s = a - 2 > 0 ? a - 2 : global.my_render.maxOrder + (a - 2), r = a + 2 > global.my_render.maxOrder ? a + 2 - global.my_render.maxOrder: a + 2, o !== e && global.my_render.renderPage(o, $(".page").index($(".page").eq(l + 1 - 5))), o !== e && global.my_render.renderPage(r, $(".page").index($(".page").eq(l + 2 - 5))), o !== e && global.my_render.renderPage(s, $(".page").index($(".page").eq(l - 2))), global.my_render.renderPage(a, $(".page").index($(".page").eq(l))), global.my_func.init(a), global.my_page.skipTo(l), setTimeout(function() {
                            return global.my_func.run(l),
                            o === e && global.my_render.renderPage(o, $(".page").index($(".page").eq(l + 1 - 5))),
                            o === e && global.my_render.renderPage(r, $(".page").index($(".page").eq(l + 2 - 5))),
                            o === e && global.my_render.renderPage(s, $(".page").index($(".page").eq(l - 2))),
                            global.my_render.renderPage(n, $(".page").index($(".page").eq(l - 1))),
                            global.my_func.initDelimiter(l)
                        },
                        500)) : (l = parseInt(a) - 1, global.my_func.init(a), global.my_page.skipTo(l), setTimeout(function() {
                            return global.my_func.run(l),
                            global.my_func.initDelimiter(l)
                        },
                        500))
                    }));
                    break;
                case "map":
                    r.push($("#" + t.attr.id).attr({
                        event_name: t.event.name,
                        event_value: t.event.value
                    }).unbind().click(function() {
                        return window.location.href = "/show/map/i?location=" + global.map.y + "," + global.map.x + "&title=" + global.map.title + "&content=" + global.map.content + "&output=html"
                    }));
                    break;
                default:
                    r.push(void 0)
                }
                return r
            }
        },
        e
    } (),
    i.exports = a
}),
define("wyaoqing.create/client.v3/plugins/formDialog3", [],
function(e, t, i) {
    function a() { (180 == window.orientation || 0 == window.orientation) && $(".orientation_tip").fadeOut(function() {
            $(this).remove()
        }),
        (90 == window.orientation || -90 == window.orientation) && $("body").prepend(s.clone())
    }
    seajs.importStyle(".formDialog-dialog{transform-origin:50% 50%; -webkit-transform-origin:50% 50%;}p.formDialog-label{display:inline-block;text-align:left;line-height: 24px;margin-top:10px;}p.formDialog-field{line-height:24px;}.formDialog .input,.formDialog .textArea{width:100%;display:inline-block;border-radius:3px;line-height: 24px;  height: 24px; background: #fff; border: 1px solid lightgrey;text-indent:3px;}");
    var n = function(t, i) {
        var a = '<div class="formDialog" style="z-index: 10000000;overflow-y:scroll; -webkit-overflow-scrolling: touch; position: absolute; width: 100%; height: 100%; left: 0px; top: 0px; opacity: 1;display: none;"><div class="formDialog-shadow" style="position: absolute; left: 0px; top: 0px; width: 100%; min-height: 100%; height: 290px;  background: rgba(0, 0, 0, 0.8);opacity:0;"></div><div class="formDialog-dialog"  id="formDialog-formBody" style="width: 70%;-webkit-transform: scale(1, 1); transform: scale(1, 1); padding: 5%;padding-top: 10px; left: 15%; top: 5%; position: absolute; border-radius: 10px; background: rgb(238, 238, 238);display:none;"><div class="formDialog-close" style="display: block; cursor: pointer; width: 16px; height: 16px; position: absolute; top: 10px; right: 10px; background: url(http://www.woyaoqing.com/application/views/mobile/preview/type_4/template_72/source/closemess.png)  no-repeat;background-size:100% 100%;"></div><div class="formDialog-submit" id="formDialog-submit" style="width: 80px; height: 30px; line-height: 30px; text-align: center; margin: 20px auto; margin-bottom:10px; border-radius: 5px; font-size: 14px; color: rgb(255, 255, 255); background: rgb(11, 185, 185);">\u63d0\u4ea4</div></div></div>';
        $("body").append(a);
        var n = {
            text: '<p class="formDialog-label">{{name}}</p><p class="formDialog-field"><input class="input" type="text" id="{{value_key}}" name="{{value_key}}" placeholder="{{placeholder}}"></p>',
            textarea: '<p class="formDialog-label">{{name}}</p><p class="formDialog-field"><textarea  class="textArea" id="{{value_key}}" name="{{value_key}}" placeholder="{{placeholder}}"></textarea></p>',
            radio: '<p class="formDialog-label">{{name}}</p><p class="formDialog-field" style="line-height:30px;">{{each radios as radio}}<input type="radio" {{if $index == 0}}checked{{/if}} name="{{value_key}}" value="{{radio}}" /> <label>{{radio}}</label><br/>{{/each}}</p>',
            select: '<p class="formDialog-label">{{name}}</p><p class="formDialog-field"><select  class="input" name="{{value_key}}">{{each options as option}}<option value="{{option}}">{{option}}</option>{{/each}}</select></p>'
        },
        o = {
            select: '<p class="formDialog-label">\u95e8\u7968 </p><p class="formDialog-field"><select name="campaign_goods_id" class="input">{{each goods as good}}<option value="{{good.id}}">{{good.name}}-{{good.price}}\u5143</option>{{/each}}</select></p>',
            radio: '<p class="formDialog-label">\u95e8\u7968 </p><p class="formDialog-field">{{each goods as good}}<input type="radio" checked name="campaign_goods_id" value="{{good.id}}" /><label>{{good.name}}-{{good.price}}\u5143</label><br/>{{/each}}</p>'
        };
        t && $(t).addClass("formDialog_btn"); {
            var s = e("wyaoqing.create/client.v3/plugins/form");
            new s(i, "#formDialog-formBody", "#formDialog-submit", {
                formColumnTemplate: n,
                campaignGoodsTemplate: o
            })
        }
        $this = this,
        $(".formDialog-close").click(function() {
            $this.hide()
        }),
        $(document).on("click", ".formDialog_btn",
        function() {
            $this.show()
        }),
        $(".formDialog-shadow").click(function() {
            $this.hide()
        }),
        $(".formDialog-field label").click(function() {
            $(this).prev("input").click()
        }),
        $(".formDialog").on("touch touchstart touchmove touchend scroll",
        function(e) {
            e.stopPropagation()
        })
    },
    o = {
        versions: function() {
            {
                var e = navigator.userAgent;
                navigator.appVersion
            }
            return {
                trident: e.indexOf("Trident") > -1,
                presto: e.indexOf("Presto") > -1,
                webKit: e.indexOf("AppleWebKit") > -1,
                gecko: e.indexOf("Gecko") > -1 && -1 == e.indexOf("KHTML"),
                mobile: !!e.match(/AppleWebKit.*Mobile.*/) || !!e.match(/AppleWebKit/),
                ios: !!e.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/),
                android: e.indexOf("Android") > -1 || e.indexOf("Linux") > -1,
                iPhone: e.indexOf("iPhone") > -1 || e.indexOf("Mac") > -1,
                iPad: e.indexOf("iPad") > -1,
                webApp: -1 == e.indexOf("Safari"),
                QQbrw: e.indexOf("MQQBrowser") > -1,
                ucLowEnd: e.indexOf("UCWEB7.") > -1,
                ucSpecial: e.indexOf("rv:1.2.3.4") > -1,
                ucweb: function() {
                    try {
                        return parseFloat(e.match(/ucweb\d+\.\d+/gi).toString().match(/\d+\.\d+/).toString()) >= 8.2
                    } catch(t) {
                        return e.indexOf("UC") > -1 ? !0 : !1
                    }
                } (),
                Symbian: e.indexOf("Symbian") > -1,
                ucSB: e.indexOf("Firefox/1.") > -1
            }
        } ()
    };
    n.prototype = {
        show: function() {
            1 == o.versions.android ? ($(".formDialog").css({
                display: "block"
            }), $(".formDialog-shadow").css({
                opacity: 1,
                height: 1.1 * $(".formDialog-dialog").height()
            }), $(".formDialog-dialog").css({
                scale: 1,
                display: "block"
            }), $(".swiper-container").css({
                display: "none"
            })) : ($(".formDialog").css({
                display: "block"
            }), $(".formDialog-shadow").css({
                opacity: 0
            }).animate({
                opacity: 1,
                height: 1.1 * $(".formDialog-dialog").height()
            },
            500), $(".formDialog-dialog").css({
                scale: 0,
                display: "block"
            }).animate({
                scale: 1
            },
            500,
            function() {
                $(".swiper-container").css({
                    display: "none"
                })
            }))
        },
        hide: function() {
            $(".swiper-container").css({
                display: "block"
            }),
            $(".formDialog-shadow").animate({
                opacity: 0
            },
            500),
            $(".formDialog-dialog").animate({
                scale: 0
            },
            500,
            function() {
                $(".formDialog").css({
                    display: "none"
                })
            })
        }
    };
    var s = $('<div class="orientation_tip" style="background:rgba(0,0,0,9);position:absolute;left:0;top:0;width:100%;height:100%;z-index:99999999;"><table height="100%" width="100%"><tr><td style="width:100%;text-align:center;color:#fff;font-size:2em;">\u8bf7\u7ffb\u8f6c\u624b\u673a\u7ad6\u5c4f\u6d4f\u89c8</td></tr></table></div>');
    window.addEventListener("onorientationchange" in window ? "orientationchange": "resize", a, !1);
    var r = new n(null, campaign_id),
    l = function(e) {
        $(e).addClass("formDialog_btn"),
        $(e).click(function() {
            r.show()
        })
    };
    i.exports = l
}),
define("wyaoqing.create/client.v3/plugins/form", [],
function(e, t, i) {
    function a(t, i, a, n, o) {
        var s = {
            formColumnTemplate: {},
            campaignGoodsTemplate: {},
            onSubmit: function() {
                return ! 0
            },
            onRender: function() {},
            onValidate: function(t) {
                var i = e("wyaoqing.create/public/toast");
                i.show("info", t.message)
            }
        };
        this.option = {},
        this.campaign_id = t;
        var r = e("wyaoqing.create/public/campaignGoods"),
        l = e("wyaoqing.create/public/formColumn");
        this.fc = new l(t),
        this.cg = new r(t),
        this.option = n ? $.extend(s, n) : s,
        this.fc.setGlobalTemplate(this.option.formColumnTemplate),
        this.cg.setTemplate(this.option.campaignGoodsTemplate),
        this.cg.setFieldType("radio"),
        i && this.renderForm.call(this, i),
        a && this.submitBtn.call(this, a, o)
    }
    var n = 2,
    o = 3,
    s = 4,
    r = 5,
    l = 0,
    c = function(t, i) {
        if ("function" == typeof i) i.call(this, t);
        else {
            var a = e("wyaoqing.create/public/toast");
            (a.show("success", t.info, 2e3), setTimeout(function() {
                window.location.reload();
            },
            2e3)) 
        }
    };
    a.prototype = {
        renderForm: function(e) {
            this.option.onRender.call(this),
            $(e).prepend(this.fc.getHtml() + this.cg.getHtml())
        },
        setFormTemplate: function(e) {
            this.fc.setGlobalTemplate(e)
        },
        setCampaignGoodsTemplate: function(e) {
            this.cg.setTemplate(e)
        },
        submitBtn: function(e, t) {
            var i = this;
            $(e).click(function() {
                var e = {},
                a = i.fc.getFormValue();
                if (a.status) {
                    if (i.option.onSubmit.call(i)) {
                        var n = i.cg.getFormValue();
                        e = $.extend(e, a.data, n, {
                            campaign_id: i.campaign_id,
                            is_ajax: "true",
                            '_token': $('meta[name="csrf-token"]').attr('content')
                        }),
                        $.post("/applet/signUp?phone="+referPhone, e,
                        function(e) {
                            console.log(e);
                            c(e, t)
                        },
                        "json")
                    }
                } else i.option.onValidate(a)
            })
        }
    },
    i.exports = a
}),
define("wyaoqing.create/public/toast", [],
function(e, t, i) {
    seajs.importStyle('.icon{font-weight:bold;}#jingle_toast{ display: none; position: absolute; z-index: 99999999999999; color: #fff; } #jingle_toast{ top: 70%; font-size: 1em; text-align: center; width: 100%;;left: 0; } #jingle_toast.top{ top: 50px; opacity: .7; } #jingle_toast>a{ padding: 10px 15px; background: #222; display: inline-block; max-width: 90%; margin: 0 auto; color:#fff; text-align: center; } #jingle_toast.top>a{ width:90%; } #jingle_toast.success>a{background-color: #27AE60;!important} #jingle_toast.error>a{background-color: #E74C3C;!important} #jingle_toast.info>a{background-color: #F1C40F;!important} #jingle_toast i.icon{ margin-right: 10px; }.icon.cancel-circle:before { content: "\xd7"; } .icon.checkmark-circle:before { content: "\u221a"; }.icon.info-2:before { content: "!"; }');
    var a, n, o = "toast",
    s = {
        toast: '<a href="#">{value}</a>',
        success: '<a href="#"><i class="icon checkmark-circle"></i>{value}</a>',
        error: '<a href="#"><i class="icon cancel-circle"></i>{value}</a></div>',
        info: '<a href="#"><i class="icon info-2"></i>{value}</a>'
    },
    r = function() {
        $("body").append('<div id="jingle_toast"></div>'),
        a = $("#jingle_toast"),
        d()
    },
    l = function() {
        a.animate({
            opacity: 0,
            top: $(window).scrollTop()
        },
        500,
        function() {
            a.hide(),
            a.empty()
        })
    },
    c = function(e, t, i) {
        $("#jingle_toast").css({
            top: $(window).scrollTop(),
            opacity: 0,
            display: "block"
        }),
        n && clearTimeout(n);
        var r = e.split(/\s/);
        o = r[0],
        a.attr("class", e).html(s[o].replace("{value}", t)),
        a.animate({
            opacity: 1,
            top: $(window).scrollTop() + 30
        },
        500),
        0 !== i && (n = setTimeout(l, i || 3e3))
    },
    d = function() {
        a.on("click",
        function() {
            l()
        })
    };
    return r(),
    {
        show: c,
        hide: l
    }
}),
define("wyaoqing.create/public/campaignGoods", [],
function(e, t, i) {
    function a(e) {
        this.campaign_id = e,
        this.data = [],
        $this = this,
        this.data = [];
        // $.ajax({
        //     method: "GET",
        //     async: !1,
        //     url: global.service + "/show/campaign/getCampaignGoodsList?campaign_id=" + e,
        //     success: function(e) {
        //         $this.data = e.data
        //     }
        // }),
        this.template = {
            radio: '<p>\u95e8\u7968 {{each goods as good}}<input type="radio" name="campaign_goods_id" value="{{good.id}}" {{if $index == 0}}checked{{/if}} /><label>{{good.name}}</label>{{/each}}</p>',
            select: '<p>\u95e8\u7968 <select name="campaign_goods_id" id="campaign_goods_id">{{each goods as good}}<option value="{{good.id}}">{{good.name}}({{good.price}}\u5143)</option>{{/each}}</select></p>'
        },
        this.fieldType = "select"
    }
    var n = e("wyaoqing.create/public/template");
    a.prototype = {
        setFieldType: function(e) {
            this.fieldType = e
        },
        setTemplate: function(e) {
            this.template = $.extend(this.template, e)
        },
        getHtml: function() {
            return this.data.length > 0 ? n.compile(this.template[this.fieldType])({
                goods: this.data
            }) : ""
        },
        getFormValue: function() {
            return "radio" == this.fieldType ? {
                campaign_goods_id: $(':radio[name="campaign_goods_id"]:checked').val()
            }: "select" == this.fieldType ? {
                campaign_goods_id: $('select[name="campaign_goods_id"]').val()
            }: void 0
        }
    },
    i.exports = a
}),
define("wyaoqing.create/public/template", [],
function(e, t, i) {
    i.exports = function() {
        function e(e) {
            return e.replace(w, "").replace(y, ",").replace(x, "").replace(k, "").replace(_, "").split($)
        }
        function t(e) {
            return "'" + e.replace(/('|\\)/g, "\\$1").replace(/\r/g, "\\r").replace(/\n/g, "\\n") + "'"
        }
        function i(i, a) {
            function n(e) {
                return g += e.split(/\n/).length - 1,
                d && (e = e.replace(/\s+/g, " ").replace(/<!--[\w\W]*?-->/g, "")),
                e && (e = b[1] + t(e) + b[2] + "\n"),
                e
            }
            function o(t) {
                var i = g;
                if (c ? t = c(t, a) : s && (t = t.replace(/\n/g,
                function() {
                    return g++,
                    "$line=" + g + ";"
                })), 0 === t.indexOf("=")) {
                    var n = p && !/^=[=#]/.test(t);
                    if (t = t.replace(/^=[=#]?|[\s;]*$/g, ""), n) {
                        var o = t.replace(/\s*\([^\)]+\)/, "");
                        u[o] || /^(include|print)$/.test(o) || (t = "$escape(" + t + ")")
                    } else t = "$string(" + t + ")";
                    t = b[1] + t + b[2]
                }
                return s && (t = "$line=" + i + ";" + t),
                v(e(t),
                function(e) {
                    if (e && !h[e]) {
                        var t;
                        t = "print" === e ? y: "include" === e ? x: u[e] ? "$utils." + e: f[e] ? "$helpers." + e: "$data." + e,
                        k += e + "=" + t + ",",
                        h[e] = !0
                    }
                }),
                t + "\n"
            }
            var s = a.debug,
            r = a.openTag,
            l = a.closeTag,
            c = a.parser,
            d = a.compress,
            p = a.escape,
            g = 1,
            h = {
                $data: 1,
                $filename: 1,
                $utils: 1,
                $helpers: 1,
                $out: 1,
                $line: 1
            },
            m = "".trim,
            b = m ? ["$out='';", "$out+=", ";", "$out"] : ["$out=[];", "$out.push(", ");", "$out.join('')"],
            w = m ? "$out+=text;return $out;": "$out.push(text);",
            y = "function(){var text=''.concat.apply('',arguments);" + w + "}",
            x = "function(filename,data){data=data||$data;var text=$utils.$include(filename,data,$filename);" + w + "}",
            k = "'use strict';var $utils=this,$helpers=$utils.$helpers," + (s ? "$line=0,": ""),
            _ = b[0],
            $ = "return new String(" + b[3] + ");";
            v(i.split(r),
            function(e) {
                e = e.split(l);
                var t = e[0],
                i = e[1];
                1 === e.length ? _ += n(t) : (_ += o(t), i && (_ += n(i)))
            });
            var T = k + _ + $;
            s && (T = "try{" + T + "}catch(e){throw {filename:$filename,name:'Render Error',message:e.message,line:$line,source:" + t(i) + ".split(/\\n/)[$line-1].replace(/^\\s+/,'')};}");
            try {
                var C = new Function("$data", "$filename", T);
                return C.prototype = u,
                C
            } catch(E) {
                throw E.temp = "function anonymous($data,$filename) {" + T + "}",
                E
            }
        }
        var a = function(e, t) {
            return "string" == typeof t ? m(t, {
                filename: e
            }) : s(e, t)
        };
        a.version = "3.0.0",
        a.config = function(e, t) {
            n[e] = t
        };
        var n = a.defaults = {
            openTag: "<%",
            closeTag: "%>",
            escape: !0,
            cache: !0,
            compress: !1,
            parser: null
        },
        o = a.cache = {};
        a.render = function(e, t) {
            return m(e, t)
        };
        var s = a.renderFile = function(e, t) {
            var i = a.get(e) || h({
                filename: e,
                name: "Render Error",
                message: "Template not found"
            });
            return t ? i(t) : i
        };
        a.get = function(e) {
            var t;
            if (o[e]) t = o[e];
            else if ("object" == typeof document) {
                var i = document.getElementById(e);
                if (i) {
                    var a = (i.value || i.innerHTML).replace(/^\s*|\s*$/g, "");
                    t = m(a, {
                        filename: e
                    })
                }
            }
            return t
        };
        var r = function(e, t) {
            return "string" != typeof e && (t = typeof e, "number" === t ? e += "": e = "function" === t ? r(e.call(e)) : ""),
            e
        },
        l = {
            "<": "&#60;",
            ">": "&#62;",
            '"': "&#34;",
            "'": "&#39;",
            "&": "&#38;"
        },
        c = function(e) {
            return l[e]
        },
        d = function(e) {
            return r(e).replace(/&(?![\w#]+;)|[<>"']/g, c)
        },
        p = Array.isArray ||
        function(e) {
            return "[object Array]" === {}.toString.call(e)
        },
        g = function(e, t) {
            var i, a;
            if (p(e)) for (i = 0, a = e.length; a > i; i++) t.call(e, e[i], i, e);
            else for (i in e) t.call(e, e[i], i)
        },
        u = a.utils = {
            $helpers: {},
            $include: s,
            $string: r,
            $escape: d,
            $each: g
        };
        a.helper = function(e, t) {
            f[e] = t
        };
        var f = a.helpers = u.$helpers;
        a.onerror = function(e) {
            var t = "Template Error\n\n";
            for (var i in e) t += "<" + i + ">\n" + e[i] + "\n\n";
            "object" == typeof console && console.error(t)
        };
        var h = function(e) {
            return a.onerror(e),
            function() {
                return "{Template Error}"
            }
        },
        m = a.compile = function(e, t) {
            function a(i) {
                try {
                    return new l(i, r) + ""
                } catch(a) {
                    return t.debug ? h(a)() : (t.debug = !0, m(e, t)(i))
                }
            }
            t = t || {};
            for (var s in n) void 0 === t[s] && (t[s] = n[s]);
            var r = t.filename;
            try {
                var l = i(e, t)
            } catch(c) {
                return c.filename = r || "anonymous",
                c.name = "Syntax Error",
                h(c)
            }
            return a.prototype = l.prototype,
            a.toString = function() {
                return l.toString()
            },
            r && t.cache && (o[r] = a),
            a
        },
        v = u.$each,
        b = "break,case,catch,continue,debugger,default,delete,do,else,false,finally,for,function,if,in,instanceof,new,null,return,switch,this,throw,true,try,typeof,var,void,while,with,abstract,boolean,byte,char,class,const,double,enum,export,extends,final,float,goto,implements,import,int,interface,long,native,package,private,protected,public,short,static,super,synchronized,throws,transient,volatile,arguments,let,yield,undefined",
        w = /\/\*[\w\W]*?\*\/|\/\/[^\n]*\n|\/\/[^\n]*$|"(?:[^"\\]|\\[\w\W])*"|'(?:[^'\\]|\\[\w\W])*'|\s*\.\s*[$\w\.]+/g,
        y = /[^\w$]+/g,
        x = new RegExp(["\\b" + b.replace(/,/g, "\\b|\\b") + "\\b"].join("|"), "g"),
        k = /^\d[^,]*|,\d[^,]*/g,
        _ = /^,+|,+$/g,
        $ = /^$|,+/;
        n.openTag = "{{",
        n.closeTag = "}}";
        var T = function(e, t) {
            var i = t.split(":"),
            a = i.shift(),
            n = i.join(":") || "";
            return n && (n = ", " + n),
            "$helpers." + a + "(" + e + n + ")"
        };
        return n.parser = function(e) {
            e = e.replace(/^\s/, "");
            var t = e.split(" "),
            i = t.shift(),
            n = t.join(" ");
            switch (i) {
            case "if":
                e = "if(" + n + "){";
                break;
            case "else":
                t = "if" === t.shift() ? " if(" + t.join(" ") + ")": "",
                e = "}else" + t + "{";
                break;
            case "/if":
                e = "}";
                break;
            case "each":
                var o = t[0] || "$data",
                s = t[1] || "as",
                r = t[2] || "$value",
                l = t[3] || "$index",
                c = r + "," + l;
                "as" !== s && (o = "[]"),
                e = "$each(" + o + ",function(" + c + "){";
                break;
            case "/each":
                e = "});";
                break;
            case "echo":
                e = "print(" + n + ");";
                break;
            case "print":
            case "include":
                e = i + "(" + t.join(",") + ");";
                break;
            default:
                if (/^\s*\|\s*[\w\$]/.test(n)) {
                    var d = !0;
                    0 === e.indexOf("#") && (e = e.substr(1), d = !1);
                    for (var p = 0,
                    g = e.split("|"), u = g.length, f = g[p++]; u > p; p++) f = T(f, g[p]);
                    e = (d ? "=": "=#") + f
                } else e = a.helpers[i] ? "=#" + i + "(" + t.join(",") + ");": "=" + e
            }
            return e
        },
        a
    } ()
}),
define("wyaoqing.create/public/formColumn", [],
function(e, t, i) {
    function a(e) {
        this.campaign_id = e,
        $this = this,
        this.data = [
                    {
                        "id": "170369",
                        "campaign_id": "419297",
                        "name": "姓　名",
                        "type": "1",
                        "value_key": "real_name",
                        "value": "",
                        "placeholder": "",
                        "max_length": "0",
                        "is_must": "1",
                        "order": "0",
                        "template": ""
                    },
                    {
                        "id": "170370",
                        "campaign_id": "419297",
                        "name": "手机号",
                        "type": "1",
                        "value_key": "mobile",
                        "value": "",
                        "placeholder": "",
                        "max_length": "0",
                        "is_must": "1",
                        "order": "1",
                        "template": ""
                    },
                    {
                        "id": "170371",
                        "campaign_id": "419297",
                        "name": "公司名称",
                        "type": "1",
                        "value_key": "email",
                        "value": "",
                        "placeholder": "",
                        "max_length": "0",
                        "is_must": "1",
                        "order": "2",
                        "template": ""
                    },
                    {
                        "id": "170372",
                        "campaign_id": "419297",
                        "name": "职务",
                        "type": "1",
                        "value_key": "company",
                        "value": "",
                        "placeholder": "",
                        "max_length": "0",
                        "is_must": "1",
                        "order": "3",
                        "template": ""
                    },
                    {
                        "id": "170373",
                        "campaign_id": "419297",
                        "name": "行业",
                        "type": "1",
                        "value_key": "title",
                        "value": "",
                        "placeholder": "",
                        "max_length": "0",
                        "is_must": "1",
                        "order": "4",
                        "template": ""
                    }
                ],
        // $.ajax({
        //     method: "GET",
        //     async: !1,
        //     url: global.service + "/show/campaign/getFormColumnList?campaign_id=" + e,
        //     success: function(e) {
        //         $this.data = e.data
        //     }
        // }),
        this.customTemplate = {},
        this.fieldType = {
            1 : "text",
            2 : "radio",
            3 : "select",
            4 : "textarea"
        },
        this.globalTemplate = {
            text: '<p>{{name}} <input type="text" name="{{value_key}}" id="{{value_key}}" placeholder="{{placeholder}}" /></p>',
            textarea: '<p>{{name}} <textarea placeholder="{{placeholder}}" name="{{value_key}}" id="{{value_key}}" rows="2"></textarea></p>',
            radio: '<p>{{name}} {{each radios as radio}}<input type="radio" name="{{value_key}}" {{if $index == 0}}checked{{/if}} value="{{radio}}" /> <label>{{radio}}</label>{{/each}}</p>',
            select: '<p>{{name}} <select name="{{value_key}}">{{each options as option}}<option value="{{option}}">{{option}}</option>{{/each}}</select></p>'
        };
        for (var t in this.data) 2 == this.data[t].type && (this.data[t].radios = this.data[t].value.split("<|>")),
        3 == this.data[t].type && (this.data[t].options = this.data[t].value.split("<|>")),
        this.data[t].template.length > 0 && (this.customTemplate[this.data[t].value_key] = this.data[t].template)
    }
    var n = e("wyaoqing.create/public/template");
    a.prototype = {
        setGlobalTemplate: function(e) {
            this.globalTemplate = $.extend(this.globalTemplate, e)
        },
        setCustomTemplate: function() {
            1 == arguments.length ? this.customTemplate = $.extend(this.customTemplate, arguments[0]) : 2 == arguments.length && (this.customTemplate[arguments[0]] = arguments[1])
        },
        getTemplate: function(e) {
            return this.customTemplate[e.value_key] ? this.customTemplate[e.value_key] : this.globalTemplate[this.fieldType[e.type]]
        },
        getHtml: function() {
            var e = "";
            for (var t in this.data) e += n.compile(this.getTemplate(this.data[t]))(this.data[t]);
            return e
        },
        getValue: function(e) {
            var t = this.fieldType[e.type];
            return "text" == t || "textarea" == t ? $('[name="' + e.value_key + '"]').val() : "radio" == t ? $(':radio[name="' + e.value_key + '"]:checked').val() : "select" == t ? $('select[name="' + e.value_key + '"]').val() : void 0
        },
        checkValidate: function(e, t) {
            var i = {
                status: 1,
                message: ""
            };
            if ("is_must" == e && !t) return i.status = 0,
            i.message = "{{name}}\u4e0d\u53ef\u4e3a\u7a7a",
            i;
            if ("mobile" == e) {
                var a = /^(((13[0-9]{1})|159|170|177|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
                if (!a.test(t)) return i.status = 0,
                i.message = "\u8bf7\u586b\u5199\u6b63\u786e\u7684{{name}}",
                i
            }
            return i
        },
        validate: function(e) {
            var t = {
                status: 1,
                message: ""
            };
            return 1 == e.is_must && (t = this.checkValidate("is_must", e.formValue)),
            t.message = n.compile(t.message)(e),
            t
        },
        getFormValue: function() {
            var e = {
                status: 1,
                data: [],
                message: ""
            };
            for (var t in this.data) {
                this.data[t].formValue = this.getValue(this.data[t]);
                var i = this.data[t],
                a = this.validate(i);
                if (1 != a.status) return e.status = 0,
                e.message = a.message,
                e;
                e.data.push(i)
            }
            var n = {};
            for (var o in e.data) n[e.data[o].value_key] = e.data[o].formValue;
            return e.data = n,
            e
        }
    },
    i.exports = a
}),
define("wyaoqing.create/client.v3/plugins/smear", [],
function(e, t, i) {
    function a(e, t, i, a, n, o) {
        this.conNode = e,
        this.background = null,
        this.backCtx = null,
        this.mask = null,
        this.maskCtx = null,
        this.lottery = null,
        this.lotteryType = "image",
        this.cover = t || "#000",
        this.coverType = i,
        this.pixlesData = null,
        this.width = a,
        this.height = n,
        this.lastPosition = null,
        this.drawPercentCallback = o,
        this.vail = !1
    }
    a.prototype = {
        createElement: function(e, t) {
            var i = document.createElement(e);
            for (var a in t) i.setAttribute(a, t[a]);
            return i
        },
        getTransparentPercent: function(e, t, i) {
            for (var a = e.getImageData(0, 0, t, i), n = a.data, o = [], s = 0, r = n.length; r > s; s += 4) {
                var l = n[s + 3];
                128 > l && o.push(s)
            }
            return (100 * (o.length / (n.length / 4))).toFixed(2)
        },
        resizeCanvas: function(e, t, i) {
            e.width = t,
            e.height = i,
            e.getContext("2d").clearRect(0, 0, t, i)
        },
        resizeCanvas_w: function(e, t, i) {
            e.width = t,
            e.height = i,
            e.getContext("2d").clearRect(0, 0, t, i),
            this.vail ? this.drawLottery() : this.drawMask()
        },
        drawPoint: function(e, t) {
            this.maskCtx.beginPath(),
            this.maskCtx.arc(e, t, 15, 0, 2 * Math.PI),
            this.maskCtx.fill(),
            this.maskCtx.beginPath(),
            this.maskCtx.lineWidth = 30,
            this.maskCtx.lineCap = this.maskCtx.lineJoin = "round",
            this.lastPosition && this.maskCtx.moveTo(this.lastPosition[0], this.lastPosition[1]),
            this.maskCtx.lineTo(e, t),
            this.maskCtx.stroke(),
            this.lastPosition = [e, t],
            this.mask.style.zIndex = 20 == this.mask.style.zIndex ? 21 : 20
        },
        bindEvent: function(e) {
            var t = this,
            i = /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()),
            a = i ? "touchstart": "mousedown",
            n = i ? "touchmove": "mousemove";
            if (i) t.conNode.addEventListener("touchmove",
            function(e) {
                o && e.preventDefault(),
                e.cancelable ? e.preventDefault() : window.event.returnValue = !1
            },
            !1),
            t.conNode.addEventListener("touchend",
            function() {
                o = !1;
                var i = t.getTransparentPercent(t.maskCtx, t.width, t.height);
                i >= e && "function" == typeof t.drawPercentCallback && t.drawPercentCallback(i)
            },
            !1);
            else {
                var o = !1;
                t.conNode.addEventListener("mouseup",
                function(i) {
                    i.preventDefault(),
                    o = !1;
                    var a = t.getTransparentPercent(t.maskCtx, t.width, t.height);
                    a >= e && "function" == typeof t.drawPercentCallback && t.drawPercentCallback(a)
                },
                !1)
            }
            this.mask.addEventListener(a,
            function(e) {
                e.preventDefault(),
                o = !0;
                var a = i ? e.touches[0].clientX: e.offsetX || e.pageX,
                n = i ? e.touches[0].clientY: e.offsetY || e.pageY;
                t.drawPoint(a, n, o)
            },
            !1),
            this.mask.addEventListener(n,
            function(e) {
                if (e.preventDefault(), !o) return ! 1;
                e.preventDefault();
                var a = i ? e.touches[0].clientX: e.offsetX || e.pageX,
                n = i ? e.touches[0].clientY: e.offsetY || e.pageY;
                t.drawPoint(a, n, o)
            },
            !1)
        },
        drawLottery: function() {
            if ("image" == this.lotteryType) {
                var e = new Image,
                t = this;
                e.onload = function() {
                    this.width = t.width,
                    this.height = t.height,
                    t.resizeCanvas(t.background, t.width, t.height),
                    t.backCtx.drawImage(this, 0, 0, t.width, t.height),
                    t.drawMask()
                },
                e.src = this.lottery
            } else if ("text" == this.lotteryType) {
                this.width = this.width,
                this.height = this.height,
                this.resizeCanvas(this.background, this.width, this.height),
                this.backCtx.save(),
                this.backCtx.fillStyle = "#FFF",
                this.backCtx.fillRect(0, 0, this.width, this.height),
                this.backCtx.restore(),
                this.backCtx.save();
                var i = 30;
                this.backCtx.font = "Bold " + i + "px Arial",
                this.backCtx.textAlign = "center",
                this.backCtx.fillStyle = "#F60",
                this.backCtx.fillText(this.lottery, this.width / 2, this.height / 2 + i / 2),
                this.backCtx.restore(),
                this.drawMask()
            }
        },
        drawMask: function() {
            if ("color" == this.coverType) this.maskCtx.fillStyle = this.cover,
            this.maskCtx.fillRect(0, 0, this.width, this.height),
            this.maskCtx.globalCompositeOperation = "destination-out";
            else if ("image" == this.coverType) {
                var e = new Image,
                t = this;
                e.onload = function() {
                    t.resizeCanvas(t.mask, t.width, t.height),
                    /android/i.test(navigator.userAgent.toLowerCase()),
                    t.maskCtx.globalAlpha = .98,
                    t.maskCtx.drawImage(this, 0, 0, this.width, this.height, 0, 0, t.width, t.height);
                    var e = 50,
                    i = "",
                    a = t.maskCtx.createLinearGradient(0, 0, t.width, 0);
                    a.addColorStop("0", "#fff"),
                    a.addColorStop("1.0", "#000"),
                    t.maskCtx.font = "Bold " + e + "px Arial",
                    t.maskCtx.textAlign = "left",
                    t.maskCtx.fillStyle = a,
                    t.maskCtx.fillText(i, t.width / 2 - t.maskCtx.measureText(i).width / 2, 100),
                    t.maskCtx.globalAlpha = 1,
                    t.maskCtx.globalCompositeOperation = "destination-out"
                },
                e.src = this.cover
            }
        },
        init: function(e, t, i, a) {
            var a = a ? a: 30,
            n = i ? i: "fixed";
            e && (this.lottery = e, this.lottery.width = this.width, this.lottery.height = this.height, this.lotteryType = t || "image", this.vail = !0),
            this.vail && (this.background = this.background || this.createElement("canvas", {
                style: "position:" + n + ";left:0;top:0;width:100%;height:100%;background-color:transparent;"
            })),
            this.mask = this.mask || this.createElement("canvas", {
                style: "position:" + n + ";left:0;top:0;width:100%;height:100%;background-color:transparent;"
            }),
            this.mask.style.zIndex = 20,
            this.conNode.innerHTML.replace(/[\w\W]| /g, "") || (this.vail && this.conNode.appendChild(this.background), this.conNode.appendChild(this.mask), this.bindEvent(a)),
            this.vail && (this.backCtx = this.backCtx || this.background.getContext("2d")),
            this.maskCtx = this.maskCtx || this.mask.getContext("2d"),
            this.vail ? this.drawLottery() : this.drawMask();
            var o = this;
            $(window).resize(function() {
                o.width = $(".swiper-container").width(),
                o.height = $(".swiper-container").height(),
                o.resizeCanvas_w(o.mask, o.width, o.height)
            })
        }
    },
    i.exports = a
}),
define("wyaoqing.create/client.v3/plugins/ad", [],
function(e, t, i) {
    var a;
    a = function(e) {
        var t, i, a;
        return a = {
            id: "ad_1",
            template: '<div class="inside animate-out" type="button" style="position: absolute; opacity: 1; left: 0; bottom: 0; border: 0px solid rgb(157, 67, 67); width: 100%; z-index: 2; height: 24px; font-size: 16px; color: rgb(252, 251, 255); -webkit-animation: 1s; background-color: rgba(48, 48, 48, 0.490196);" id="ad_1"><div class="comp_button"><div class="table_row"><div class="table_cell element"><font size="2" style="color: #FFFFFF;">会搜云提供技术支持</font></div></div></div></div>',
            effect: {
                type: "bounce",
                name: "bounce-in-up-big",
                duration: "3s",
                delay: 0,
                infinite: !1
            },
            getADElement: function() {
                return $(this.template).attr("id", this.id)
            }
        },
        global.my_func.register(e, a.id, a.effect),
        t = a.getADElement(),
        t.click(function() {
            return window.location.href = global.ad_link
        }),
        i = 0,
        $("body").find(".order" + e).find(".inside").each(function() {
            return i = $(this).css("zIndex") > i ? $(this).css("zIndex") : i
        }),
        $("body").find(".order" + e).append(t.css("zIndex", parseInt(i) + 1))
    },
    i.exports = a
}),
define("wyaoqing.create/client.v3/subject/page", [],
function(e, t, i) {
    var a = {
        triggerLoop: !1,
        _initEvent: function() {
            var e = $("body"),
            t = $(window),
            i = e.find(".page"),
            a = null,
            n = !1,
            o = null,
            s = 0,
            r = 0,
            l = 0,
            c = 0,
            d = !1,
            p = !1,
            g = !0,
            u = this;
            $(function(f) {
                t.on("scroll.elasticity",
                function(e) {
                    e.preventDefault()
                }).on("touchmove.elasticity",
                function(e) {
                    e.preventDefault()
                }),
                t.delegate("img", "mousemove",
                function(e) {
                    e.preventDefault()
                }),
                e.on("mousedown touchstart",
                function(e) {
                    n || (a = i.filter(".p-current").get(0), o = null, a && (d = !0, p = !1, g = !0, l = 0, c = 0), "touchstart" == e.type ? (s = e.originalEvent.changedTouches[0].pageX, r = e.originalEvent.changedTouches[0].pageY) : (s = e.pageX, r = e.pageY), a.classList.add("moving"), a.style.webkitTransition = "none")
                }).on("mousemove touchmove",
                function(e) {
                    if (d && (o || g)) if ("touchmove" == e.type ? (l = e.originalEvent.changedTouches[0].pageX - s, c = e.originalEvent.changedTouches[0].pageY - r) : (l = e.pageX - s, c = e.pageY - r), "vertical" == global.page_setting.direction && Math.abs(c) > Math.abs(l)) {
                        if (c > 0) {
                            if (!global.page_setting.loop && f(a).find(".order1").size() > 0) return ! 1;
                            p || g ? (p = !1, g = !1, o ? (o.classList.remove("p-active"), o.classList.remove("moving")) : o = a.previousElementSibling && a.previousElementSibling.classList.contains("page") ? a.previousElementSibling: u.triggerLoop ? i.last().get(0) : !1, o && o.classList.contains("page") ? (o.classList.add("p-active"), o.classList.add("moving"), o.style.webkitTransition = "none", o.style.webkitTransform = "translateY(-100%)", f(o).trigger("active"), a.style.webkitTransformOrigin = "bottom center") : (a.style.webkitTransform = "translateY(0px) scale(1)", o = null)) : ("cover" == global.page_setting.flipEffect ? a.style.webkitTransform = "translateY(0px)": "push" == global.page_setting.flipEffect && (a.style.webkitTransform = "translateY(" + c + "px)"), o && (o.style.webkitTransform = "translateY(-" + (window.innerHeight - c) + "px)"))
                        } else if (0 > c) {
                            if (!global.page_setting.loop && f(a).find(".order" + global.my_render.maxOrder).size() > 0) return ! 1; ! p || g ? (p = !0, g = !1, o ? (o.classList.remove("p-active"), o.classList.remove("moving")) : a.nextElementSibling && a.nextElementSibling.classList.contains("page") ? o = a.nextElementSibling: (o = i.first().get(0), u.triggerLoop = !0), o && o.classList.contains("page") ? (o.classList.add("p-active"), o.classList.add("moving"), o.style.webkitTransition = "none", o.style.webkitTransform = "translateY(" + window.innerHeight + "px)", f(o).trigger("active"), a.style.webkitTransformOrigin = "top center") : (a.style.webkitTransform = "translateY(0px) scale(1)", o = null)) : "cover" == global.page_setting.flipEffect ? (a.style.webkitTransform = "translateY(0px)", o.style.webkitTransform = "translateY(" + (window.innerHeight + c) + "px)") : "push" == global.page_setting.flipEffect && (a.style.webkitTransform = "translateY(" + c + "px)", o.style.webkitTransform = "translateY(" + (window.innerHeight + c) + "px)")
                        }
                    } else if ("horizontal" == global.page_setting.direction && Math.abs(l) > Math.abs(c)) if (l > 0) {
                        if (!global.page_setting.loop && f(a).find(".order1").size() > 0) return ! 1;
                        p || g ? (p = !1, g = !1, o ? (o.classList.remove("p-active"), o.classList.remove("moving")) : o = a.previousElementSibling && a.previousElementSibling.classList.contains("page") ? a.previousElementSibling: u.triggerLoop ? i.last().get(0) : !1, o && o.classList.contains("page") ? (o.classList.add("p-active"), o.classList.add("moving"), o.style.webkitTransition = "none", o.style.webkitTransform = "translateX(-100%)", f(o).trigger("active"), a.style.webkitTransformOrigin = "bottom center") : (a.style.webkitTransform = "translateX(0px) scale(1)", o = null)) : ("cover" == global.page_setting.flipEffect ? a.style.webkitTransform = "translateX(0px)": "push" == global.page_setting.flipEffect && (a.style.webkitTransform = "translateX(" + l + "px)"), o && (o.style.webkitTransform = "translateX(-" + (window.innerWidth - l) + "px)"))
                    } else if (0 > l) {
                        if (!global.page_setting.loop && f(a).find(".order" + global.my_render.maxOrder).size() > 0) return ! 1; ! p || g ? (p = !0, g = !1, o ? (o.classList.remove("p-active"), o.classList.remove("moving")) : a.nextElementSibling && a.nextElementSibling.classList.contains("page") ? o = a.nextElementSibling: (o = i.first().get(0), u.triggerLoop = !0), o && o.classList.contains("page") ? (o.classList.add("p-active"), o.classList.add("moving"), o.style.webkitTransition = "none", o.style.webkitTransform = "translateX(" + window.innerWidth + "px)", f(o).trigger("active"), a.style.webkitTransformOrigin = "left center") : (a.style.webkitTransform = "translateX(0px) scale(1)", o = null)) : "cover" == global.page_setting.flipEffect ? (a.style.webkitTransform = "translateX(0px)", o.style.webkitTransform = "translateX(" + (window.innerWidth + l) + "px)") : "push" == global.page_setting.flipEffect && (a.style.webkitTransform = "translateX(" + l + "px)", o.style.webkitTransform = "translateX(" + (window.innerWidth + l) + "px)")
                    }
                }).on("mouseup touchend",
                function() {
                    d && (d = !1, o ? (n = !0, a.style.webkitTransition = "-webkit-transform 0.4s ease-out", o.style.webkitTransition = "-webkit-transform 0.4s ease-out", "vertical" == global.page_setting.direction ? Math.abs(c) > Math.abs(l) && Math.abs(c) > 100 ? (p ? "cover" == global.page_setting.flipEffect ? (a.style.webkitTransform = "translateY(-0px)", o.style.webkitTransform = "translateY(0px)") : "push" == global.page_setting.flipEffect && (a.style.webkitTransform = "translateY(-" + window.innerHeight + "px)", o.style.webkitTransform = "translateY(0px)") : "cover" == global.page_setting.flipEffect ? (a.style.webkitTransform = "translateY(0px)", o.style.webkitTransform = "translateY(0px)") : "push" == global.page_setting.flipEffect && (a.style.webkitTransform = "translateY(" + window.innerHeight + "px)", o.style.webkitTransform = "translateY(0px)"), setTimeout(function() {
                        o && (o.classList.remove("p-active"), o.classList.remove("moving"), o.classList.add("p-current")),
                        a.classList.remove("p-current"),
                        a.classList.remove("moving"),
                        a = f(o).trigger("current").get(0),
                        f(a).trigger("hide"),
                        n = !1;
                        var e = i.index(f(a));
                        global.my_func.initDelimiter(e),
                        global.my_func.run(e)
                    },
                    500)) : (p ? (a.style.webkitTransform = "scale(1)", o.style.webkitTransform = "translateY(100%)") : (a.style.webkitTransform = "scale(1)", o.style.webkitTransform = "translateY(-100%)"), setTimeout(function() {
                        o.classList.remove("p-active"),
                        o.classList.remove("moving"),
                        n = !1
                    },
                    500)) : "horizontal" == global.page_setting.direction && (Math.abs(l) > Math.abs(c) && Math.abs(l) > 100 ? (p ? "cover" == global.page_setting.flipEffect ? (a.style.webkitTransform = "translateX(-0px)", o.style.webkitTransform = "translateX(0px)") : "push" == global.page_setting.flipEffect && (a.style.webkitTransform = "translateX(-" + window.innerWidth + "px)", o.style.webkitTransform = "translateX(0px)") : "cover" == global.page_setting.flipEffect ? (a.style.webkitTransform = "translateX(0px)", o.style.webkitTransform = "translateX(0px)") : "push" == global.page_setting.flipEffect && (a.style.webkitTransform = "translateX(" + window.innerWidth + "px)", o.style.webkitTransform = "translateX(0px)"), setTimeout(function() {
                        o && (o.classList.remove("p-active"), o.classList.remove("moving"), o.classList.add("p-current")),
                        a.classList.remove("p-current"),
                        a.classList.remove("moving"),
                        a = f(o).trigger("current").get(0),
                        f(a).trigger("hide"),
                        n = !1;
                        var e = i.index(f(a));
                        global.my_func.initDelimiter(e),
                        global.my_func.run(e)
                    },
                    500)) : (p ? (a.style.webkitTransform = "scale(1)", o.style.webkitTransform = "translateX(100%)") : (a.style.webkitTransform = "scale(1)", o.style.webkitTransform = "translateX(-100%)"), setTimeout(function() {
                        o.classList.remove("p-active"),
                        o.classList.remove("moving"),
                        n = !1
                    },
                    500)))) : a.classList.remove("moving"))
                })
            })
        },
        init: function() {
            this._initEvent.apply(this, arguments)
        },
        skipTo: function(e) {
            var t = $(".p-current"),
            i = this;
            t.addClass("moving");
            var a = $(".page" + e).addClass("moving").addClass("p-active").get(0);
            "vertical" == global.page_setting.direction ? (a.style.webkitTransform = "translateY(100%)", t.get(0).style.webkitTransform = "translateY(0px)", setTimeout(function() {
                a.style.webkitTransition = "-webkit-transform 0.4s ease-out",
                t.get(0).style.webkitTransition = "-webkit-transform 0.4s ease-out",
                a.style.webkitTransform = "translateY(0%)",
                "cover" == global.page_setting.flipEffect ? t.get(0).style.webkitTransform = "translateY(0px)": "push" == global.page_setting.flipEffect && (t.get(0).style.webkitTransform = "translateY(-" + window.innerHeight + "px)")
            },
            0)) : "horizontal" == global.page_setting.direction && (a.style.webkitTransform = "translateX(100%)", t.get(0).style.webkitTransform = "translateX(0px)", setTimeout(function() {
                a.style.webkitTransition = "-webkit-transform 0.4s ease-out",
                t.get(0).style.webkitTransition = "-webkit-transform 0.4s ease-out",
                a.style.webkitTransform = "translateX(0%)",
                "cover" == global.page_setting.flipEffect ? t.get(0).style.webkitTransform = "translateX(0px)": "push" == global.page_setting.flipEffect && (t.get(0).style.webkitTransform = "translateX(-" + window.innerWidth + "px)")
            },
            0)),
            setTimeout(function() {
                t.removeClass("p-current").removeClass("moving").addClass("p-link"),
                $(a).addClass("p-current").removeClass("moving").removeClass("p-active"),
                i.triggerLoop = !0
            },
            500)
        }
    };
    i.exports = a
}),
define("wyaoqing.create/client.v3/plugins/report", [],
function(e, t) {
    t.init = function() {
        return $("#report").on("touch, mousedown",
        function(e) {
            return $("#report0").fadeIn(),
            e.stopPropagation()
        }),
        $(".page-container").on("touch, mousedown",
        function() {
            return $("#report0").fadeOut()
        }),
        $("#reportList li").on("touch, mousedown",
        function() {
            return $("#report_type").val($(this).attr("value")),
            $(this).addClass("active").siblings().removeClass("active")
        }),
        $("#reportSubmit").on("touch, mousedown",
        function() {
            var e;
            return e = {
                report_type: $("#report_type").val(),
                campaign_id: campaign_id
            },
            $.post("/show/preview/report", e,
            function(e) {
                return 1 === e.code && alert("\u4e3e\u62a5\u6210\u529f\uff01"),
                $("#report0").fadeOut(),
                $("#report").fadeOut()
            })
        })
    }
}),
define("wyaoqing.create/client.v3/plugins/orientation", [],
function() {
    function e() { (180 == window.orientation || 0 == window.orientation) && $(".orientation_tip").fadeOut(function() {
            $(this).remove()
        }),
        (90 == window.orientation || -90 == window.orientation) && $("body").prepend(t.clone())
    }
    var t = $('<div class="orientation_tip" style="background:rgba(0,0,0,9);position:absolute;left:0;top:0;width:100%;height:100%;z-index:99999999;"><table height="100%" width="100%"><tr><td style="width:100%;text-align:center;color:#fff;font-size:2em;">\u8bf7\u7ffb\u8f6c\u624b\u673a\u7ad6\u5c4f\u6d4f\u89c8</td></tr></table></div>');
    window.addEventListener("onorientationchange" in window ? "orientationchange": "resize", e, !1)
});