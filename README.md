# 阿里云api Laravel 相关

## 配置

```dotenv
ALI_CLOUD_API_VERIFY_ID_CARD_APP_CODE=
ALI_CLOUD_API_VERIFY_ID_CARD_LOG_CHANNEL=
# none, success, failed
ALI_CLOUD_API_VERIFY_ID_CARD_FAKE_TYPE=
```

## 使用

```php
use Zhan3333\AliCloudApi\AliVerifyIdCardService;

$service = app(AliVerifyIdCardService::class);
$result = $service->verify('张三', '420222199212040000');
// boolean 是否验证通过
dump($result['valid']);
// 不通过时的原因
dump($result['reason']);
```
