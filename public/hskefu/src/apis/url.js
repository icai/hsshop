const prefix = '/hsapp'
// 项目中用到的api接口统一放在url.js下面 方便统一管理
export default {
  // 商品相关接口
  goods: {
    // 商品详情
    productDetail: `${prefix}/sellerapp/product/detail`,
    // 商品规格
    productRules: `${prefix}/sellerapp/product/getSkusByProductId`
  },
  hsStatistic: {
    // 营收
    revenue: `${prefix}/sellerapp/statistics/shopStatistics`
  },
  customer: {
    // 用户
    revenue: `${prefix}/sellerapp/statistics/memberStatistics`
  },
  flow: {
    // 流量
    revenue: `${prefix}/sellerapp/statistics/shopPageStatistics`
  },
  trans: {
    //交易
    revenue: `${prefix}/sellerapp/statistics/shopOrderStatistics`
  }
}
