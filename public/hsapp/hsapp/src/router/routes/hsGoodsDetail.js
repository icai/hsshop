export default [
  {
    path: '/hsGoods',
    component: () => import('../../views/goods/index.vue'),
    children: [{
      path: 'goodsdetail',
      name: 'goods-detail',
      meta: {
        layout: true,
        title: '商品详情'
      },
      component: () => import('../../views/goods/goodsDetail/goods-detail.vue')
    }]
  }
]
