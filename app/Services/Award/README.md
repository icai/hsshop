# 奖励

目前只是一个想法，把所有发放奖励的逻辑统一起来。并没有这方面经验，实际可用性有待考验。

## 调用代码
```php
AwardFactory::make($award_id)
    ->setMemberId($this->getMemberId())//不指定则默认为当前用户
    ->send($award['num']);
```

> 目前仅实现了送积分逻辑。但“积分”的业务逻辑其实并没有实现，需要调用“用户模块”，但用户模块还没实现。