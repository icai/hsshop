import Vue from 'vue'
// 商品评价组件
import GoodsEvaluate from './evaluate/index.vue'
// 统计底部栏封装组件
import StaticBottom from './staticBottom/index.vue'
// 引入线性图数据组件 ---- 营收统计
import LineStatis from './line'
// 引入线性图数据组件 --- 交易统计
import TransLine from './transLine'

// 这里注册一下全局的组件
Vue.component('goods-evaluate', GoodsEvaluate)
Vue.component('static-bottom', StaticBottom)
Vue.component('line-statis', LineStatis)
Vue.component('line-trans', TransLine)
