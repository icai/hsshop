import hsGoodsDetail from './hsGoodsDetail'
import hsStatistic from './hsStatistic'

export default [
  {path: '/', redirect: '/flow/flow'},
  {
    path: '/',
    meta: {
      layout: true
    },
    component: () => import('../../views/index/index.vue')
    // 这里可以插入子级的路由模块
    // children: [
    //   {
    //     name: 'home',
    //     path: '',
    //     meta: {
    //       layout: true,
    //       title: '首页',
    //     },
    //     component: () => import('../../views/index/home.vue')
    //   }
    // ],
  },
  ...hsGoodsDetail,
  ...hsStatistic
]
