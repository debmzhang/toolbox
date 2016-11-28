web toolbox
===========
## 安装
`composer require debmzhang/toolbox`
## 使用
```php
require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use debmzhang\toolbox\Tool;

$tool = new Tool;
// 避免中文被转换成unicode
echo $tool->json_encode2('欢迎使用 toolbox ^_^');
// 生成手机短信校验码
// int $type 0 纯字母 1 纯数字(默认) 2 混合
// int $len 长度 默认6位
$type = 1;
$len = 6;
echo $tool->generateSmsCode($type, $len);
// 生成一个订单号
// string $prefix 订单前缀, 默认空
// string $connectors 订单前缀与数字串之间的连接符, 默认空
// int $len 随机数位数, 默认8位
$prefix = 'alipay';
$connectors = '-';
$len = 8;
echo $tool->generateOrderNO($prefix, $connectors, $len);
// 把秒转换成 x天x时x分x秒 的格式
$seconds = 12345678;
echo $tool->formatTime($seconds);
// 验证是否是一个手机号码(是正确的手机返回 true)
$phone = 13838333333;
var_dump($tool->checkIsPhoneNumber($phone));
// 验证是否是一个正确的身份证号码
$idCard = 110101201501023656;
var_dump($tool->checkIsIdentityCard($idCard));
// 把结果以 json 格式返回
// mixed $code 0:正常 大于0 都是非正常
// mixed $msg
// array $result
// bool $isJsonp 是否使用 jsonp 方式提交数据
// string $param jsonp 方式提交参数名
$code = 0;
$msg = 'success';
$result = array(
    'name' => 'debm',
    'sex' => 'M',
    'age' => '18',
);
return $tool->ajaxMessage($code, $msg, $result);

// 更多功能等待添加...
```
