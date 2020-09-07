# 营销活动

## 调用
#### 获取活动实例
```php
ActivityFactory::make($activityId);

//指定用户（默认为当前用户）
ActivityFactory::make($activityId)
    ->setMemberId($memberId)
```
#### 参与活动
```php
ActivityFactory::make($activityId)->attend();
```
> 一般来说，此函数返回中奖结果。若未中奖，返回空数组。返回数据格式如下：  
但此方法的具体实现在子类中，也可能在具体实现中返回其他格式。

```php
array(
    "rule_id" => 2 // 规则ID
    "award_id" => 1 //marketing_activity_award表奖品ID
    "num" => 2 //奖品数量
)
```

#### 获取剩余可参与次数
```php
ActivityFactory::make($activityId)->getUserRemainingTimes();
```
> 非负整数代表剩余次数。负数代表不限制参与次数。

#### 判断用户能否参与活动
```php
ActivityFactory::make($activityId)->canAttend();
```

#### 获取活动规则
```php
ActivityFactory::make($activityId)->getRules();
```

#### 添加访问日志
```php
//参与日志的逻辑会自动调用，但访问日志逻辑需要手工调用
(new ActivityService)->addViewLog(1);
```

## 扩展活动
1. 实现`ActivityAbstract`类
 - `attend()`：参与活动
 - `getUserRemainingTimes()`：获取用户剩余可参与次数
> 由于活动逻辑差异较大，大部分逻辑都是写在attend()方法里的。基类中只是实现了一些操作方法供子类调用。

2. 实现`ActivityRuleAbstract`类（可选，没有规则的活动可以不实现此抽象方法）
 - `addRules()`：添加活动规则
 - `getRules()`：获取活动规则

## 中奖算法
目前支持3种中奖算法组合，程序并不限定某个活动使用某种对应的算法。由创建活动逻辑确定中奖算法组合形式。

### 算法一
根据`marketing_activity`表`percent`字段计算用户是否中奖。此算法仅返回是否中奖，**不能获取奖项**。  
此逻辑在**基类**中实现，若不希望这个概率生效，则将概率值设为100即可。

### 算法二
根据`marketing_activity_rule_*`表`percent`字段计算用户所中奖项。  
percent字段总和不能大于100，也就是说这个算法**包含**未中奖情况。  
此逻辑在**子类**中自行实现。可参考`EggRulesService::randAward`实现。

### 算法三
根据`marketing_activity_rule_*`表`remaining`（剩余奖品数）字段计算用户所中奖项。  
此算法一般配合算法一使用（独立使用的话中奖概率为100%）。以剩余奖品数为权重，随机出用户所中奖项。此算法**不包含**未中奖情况  
此逻辑在**子类**中自行实现。可参考`CardRulesService::randAward`实现。