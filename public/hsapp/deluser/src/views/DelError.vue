<template>
  <div>
    <section>
      <div class="header">
        <icon type="warn" is-msg></icon>
        <p class="header-warn">抱歉，无法注销</p>
        <p class="header-info">由于以下原因，导致账号无法注销</p>
      </div>
      <div class="content">
        <div class="reason">
          <div class="reason-titile">还没有退出某某某1企业（显示企业名称）</div>
          <div class="reason-info">您可以联系该企业管理员关闭名片权限，并且完成您名片下的客户交接</div>
        </div>
        <div class="reason">
          <div class="reason-titile">还没有退出某某某2企业（显示企业名称）</div>
          <div class="reason-info">您可以联系该企业管理员关闭名片权限，并且完成您名片下的客户交接</div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { Icon } from 'vux';

export default {
  components: {
    Icon,
  },
};
</script>
<style lang="less" scoped>
@import '../assets/global.less';
.weui-icon_msg {
  font-size: 60px !important;
}
.header {
  text-align: center;
  padding-top: 30px;
  .header-warn {
    .fonColor(#000,18px);
    margin-top: 16px;
  }
  .header-info {
    .fonColor(#999,15px);
    margin-top: 8px;
  }
}
.content {
  padding: 40px 13px 0px 13px;
}
.reason {
  text-align: left;
  .reason-titile {
    margin: 20px 0 8px 0px;
    .fonColor(#000,15px);
  }
  .reason-info {
    .fonColor(#666,15px);
  }
}
</style>
