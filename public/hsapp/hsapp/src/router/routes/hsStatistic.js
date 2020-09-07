export default [
  {
    path: '/hsStatistic',
    component: () => import('../../views/statistic/index.vue'),
    children: [
      {
        path: 'revenue',
        name: 'revenue',
        meta: {
          layout: true,
          title: '营收统计'
        },
        component: () => import('../../views/statistic/revenue/revenue.vue')
      },
      {
        path: 'income-detail',
        name: 'income-detail',
        meta: {
          layout: true,
          title: '收支明细'
        },
        component: () => import('../../views/statistic/revenue/incomeDetail.vue')
      },
      {
        path: 'transation',
        name: 'transation',
        meta: {
          layout: true,
          title: '交易统计'
        },
        component: () => import('../../views/statistic/transation/transation.vue')
      },
      {
        path: 'customer',
        name: 'customer',
        meta: {
          layout: true,
          title: '客户统计'
        },
        component: () => import('../../views/statistic/customer/customer.vue')
      },
      {
        path: '/flow',
        component: () => import('../../views/statistic/flow/index.vue'),
        children: [
          {
            path: 'flow',
            name: 'flow',
            meta: {
              title: '流量统计'
            },
            component: () => import('../../views/statistic/flow/flow.vue')
          },
          {
            path: 'flow-day',
            name: 'flow-day',
            meta: {
              title: '每日流量统计'
            },
            component: () => import('../../views/statistic/flow/childflow/day-month-flow.vue')
          },
          {
            path: 'flow-month',
            name: 'flow-month',
            meta: {
              title: '每月流量统计'
            },
            component: () => import('../../views/statistic/flow/childflow/day-month-flow.vue')
          },
          {
            path: 'look-page',
            name: 'look-page',
            meta: {
              title: '页面浏览排行'
            },
            component: () => import('../../views/statistic/flow/childflow/look-page.vue')
          }
        ]
      }
    ]
  }
]
