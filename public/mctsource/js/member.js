$(function(){
	 $('.js-help-notes').mouseover(function() {
            $('.js-intro-popover').show();
        }).mouseout(function() {
            $('.js-intro-popover').hide();
        })
        $.get('../../static/js/china.json', function (chinaJson) {
            // echarts.registerMap('china', chinaJson);
            var chart = echarts.init(document.getElementById('left_area'));
            optionArea = {
                    tooltip: {
                        trigger: 'item'
                    },
                    dataRange: {
                        min: 0,
                        max: 2500,
                        x: 'left',
                        y: 'top',
                        text: ['高', '低'], // 文本，默认为数值文本
                        calculable: false,
                        orient: 'horizontal',
                        itemGap: 10,
                        itemHeight: 30
                    },
                    series: [{
                        name: 'iphone3',
                        type: 'map',
                        mapType: 'china',
                        roam: false,
                        itemStyle: {
                            normal: {
                                label: {
                                    show: false
                                }
                            },
                            emphasis: {
                                label: {
                                    show: false
                                }
                            }
                        },
                        data: [{
                            name: '北京',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '天津',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '上海',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '重庆',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '河北',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '河南',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '云南',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '辽宁',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '黑龙江',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '湖南',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '安徽',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '山东',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '新疆',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '江苏',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '浙江',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '江西',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '湖北',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '广西',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '甘肃',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '山西',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '内蒙古',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '陕西',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '吉林',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '福建',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '贵州',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '广东',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '青海',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '西藏',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '四川',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '宁夏',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '海南',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '台湾',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '香港',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '澳门',
                            value: Math.round(Math.random() * 1000)
                        }]
                    }, {
                        name: 'iphone4',
                        type: 'map',
                        mapType: 'china',
                        itemStyle: {
                            normal: {
                                label: {
                                    show: false
                                }
                            },
                            emphasis: {
                                label: {
                                    show: false
                                }
                            }
                        },
                        data: [{
                            name: '北京',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '天津',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '上海',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '重庆',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '河北',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '安徽',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '新疆',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '浙江',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '江西',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '山西',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '内蒙古',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '吉林',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '福建',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '广东',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '西藏',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '四川',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '宁夏',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '香港',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '澳门',
                            value: Math.round(Math.random() * 1000)
                        }]
                    }, {
                        name: 'iphone5',
                        type: 'map',
                        mapType: 'china',
                        itemStyle: {
                            normal: {
                                label: {
                                    show: false
                                }
                            },
                            emphasis: {
                                label: {
                                    show: false     
                                }
                            }
                        },
                        data: [{
                            name: '北京',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '天津',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '上海',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '广东',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '台湾',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '香港',
                            value: Math.round(Math.random() * 1000)
                        }, {
                            name: '澳门',
                            value: Math.round(Math.random() * 1000)
                        }]
                    }]
                };
            chart.setOption(optionArea);
        });
        // require.config({
        //     paths: {
        //         echarts: 'public/static/js/echarts'
        //     }
        // });

        // 使用
        // require(
        //     [
        //         'echarts',
        //         'echarts/chart/pie', // 使用柱状图就加载bar模块，按需加载
        //         'echarts/chart/map' // 使用柱状图就加载bar模块，按需加载
        //     ],
        //     function(ec) {
                // 基于准备好的dom，初始化echarts图表
        var myChart = echarts.init(document.getElementById('echarts_left'));
        var myChartRight = echarts.init(document.getElementById('echarts_right'));
        // var myChartArea = echarts.init(document.getElementById('left_area'));
        
        option = {
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                x: '100',
                y: 'top',
                data: ['男', '女', '未知']
            },
            calculable: false,
            series: [{
                name: '访问来源',
                type: 'pie',
                radius: ['40px', '80px'],
                itemStyle: {
                    normal: {
                        label: {
                            show: false
                        },
                        labelLine: {
                            show: false
                        }
                    },
                    emphasis: {
                        label: {
                            show: false,
                            position: 'center',
                            textStyle: {
                                fontSize: '30',
                                fontWeight: 'bold'
                            },
                        }
                    },

                },
                data: [{
                    value: 335,
                    name: '男'
                }, {
                    value: 310,
                    name: '女'
                }, {
                    value: 234,
                    name: '未知'
                }],
            }]
        };
        optionRight = {
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                x: 'left',
                data: ['购买过的粉丝', '未购买过的粉丝']
            },
            calculable: false,
            series: [{
                name: '访问来源',
                type: 'pie',
                radius: ['40px', '80px'],
                itemStyle: {
                    normal: {
                        label: {
                            show: false
                        },
                        labelLine: {
                            show: false
                        }
                    },
                    emphasis: {
                        label: {
                            show: false,
                            position: 'center',
                            textStyle: {
                                fontSize: '30',
                                fontWeight: 'bold'
                            },
                        }
                    },

                },
                data: [{
                    value: 335,
                    name: '购买过的粉丝'
                }, {
                    value: 310,
                    name: '未购买过的粉丝'
                }],
            }]
        };
        
        // 为echarts对象加载数据 
        myChart.setOption(option);
        myChartRight.setOption(optionRight);
            // myChartArea.setOption(optionArea);

})