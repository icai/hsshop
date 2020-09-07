export default [
  {
    path: '/kefu',
    component: () => import('../../views/kefu/index.vue'),
    children: [
      {
        path: 'list',
        name: 'list',
        meta: {
          layout: true,
          title: '接待列表'
        },
        component: () => import('../../views/kefu/list.vue')
      },
      {
        path: 'chat',
        name: 'chat',
        meta: {
          layout: true,
          title: '客服聊天'
        },
        component: () => import('../../views/kefu/chat.vue')
      },
      {
        path: 'set',
        name: 'set',
        meta: {
          layout: true,
          title: '客服消息设置'
        },
        component: () => import('../../views/kefu/set.vue')
      },
      {
        path: 'setAuto',
        name: 'setAuto',
        meta: {
          layout: true,
          title: '设置自动接入'
        },
        component: () => import('../../views/kefu/setAuto.vue')
      },
      {
        path: 'addKefu',
        name: 'addKefu',
        meta: {
          layout: true,
          title: '添加客服'
        },
        component: () => import('../../views/kefu/addKefu.vue')
      },
      {
        path: 'userDetail',
        name: 'userDetail',
        meta: {
          layout: true,
          title: '客户资料'
        },
        component: () => import('../../views/kefu/userDetail.vue')
      },
      {
        path: 'manager',
        name: 'manager',
        meta: {
          layout: true,
          title: '店铺管理员'
        },
        component: () => import('../../views/kefu/manager.vue')
      },
      {
        path: 'orderList',
        name: 'orderList',
        meta: {
          layout: true,
          title: '订单列表'
        },
        component: () => import('../../views/kefu/orderList.vue')
      },
      {
        path: 'kefuList',
        name: 'kefuList',
        meta: {
          layout: true,
          title: '订单列表'
        },
        component: () => import('../../views/kefu/kefuList.vue')
      },
      {
        path:'manyCustomer',
        name:'manyCustomer',
        meta: {
          layout: true,
          title: '多客户接待'
        },
        component: () => import('../../views/kefu/manyCustomer.vue')
      },
      {
        path:'transferList',
        name:'transferList',
        meta: {
          layout: true,
          title: '客服转接'
        },
        component: () => import('../../views/kefu/transferList.vue')
      },
      {
        path:'agreement',
        name:'agreement',
        meta: {
          layout: true,
          title: '隐私协议'
        },
        component: () => import('../../views/kefu/agreement.vue')
      }
    ]
  },
  {
    path: '/transfer',
    component: () => import('../../views/kefu/transfer.vue')
  }
]
