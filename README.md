<h1 align="center"> laravel-optimizer </h1>

<p align="center"> .</p>


## Installing

```shell
$ composer require godforheart/laravel-optimizer -vvv
```

php artisan optimizer:publish
或者
php artisan vendor:publish --provider="Godforheart\LaravelOptimizer\OptimizerS erviceProvider"



## Usage

TODO

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/godforheart/laravel-optimizer/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/godforheart/laravel-optimizer/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT



















# [EasyWeChat](https://www.easywechat.com)

📦 一个 PHP 微信开发 SDK，开源 Saas 平台提供商 [微擎](https://www.easywechat.com/w7team.jpg) 旗下开源产品。

[![Test Status](https://github.com/w7corp/easywechat/workflows/Test/badge.svg)](https://github.com/w7corp/easywechat/actions)
[![Lint Status](https://github.com/w7corp/easywechat/workflows/Lint/badge.svg)](https://github.com/w7corp/easywechat/actions)
[![Latest Stable Version](https://poser.pugx.org/w7corp/easywechat/v/stable.svg)](https://packagist.org/packages/w7corp/easywechat)
[![Latest Unstable Version](https://poser.pugx.org/w7corp/easywechat/v/unstable.svg)](https://packagist.org/packages/w7corp/easywechat)
[![Total Downloads](https://poser.pugx.org/w7corp/easywechat/downloads)](https://packagist.org/packages/w7corp/easywechat)
[![License](https://poser.pugx.org/w7corp/easywechat/license)](https://packagist.org/packages/w7corp/easywechat)

## 环境需求

- PHP >= 8.0.2
- [Composer](https://getcomposer.org/) >= 2.0

## 安装

```bash
composer require w7corp/easywechat
```

## 使用示例

基本使用（以公众号服务端为例）:

```php
<?php

use EasyWeChat\OfficialAccount\Application;

$config = [
    'app_id' => 'wx3cf0f39249eb0exxx',
    'secret' => 'f1c242f4f28f735d4687abb469072xxx',
    'aes_key' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
    'token' => 'easywechat',
];

$app = new Application($config);

$app->getServer()->with(fn() => "您好！EasyWeChat！");

$response = $server->serve();

$response->send();
```

## 文档和链接

[官网](https://www.easywechat.com) · [讨论](https://github.com/w7corp/easywechat/discussions) · [更新策略](https://github.com/w7corp/easywechat/security/policy)

## :heart: 支持我

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me.svg?raw=true)](https://github.com/sponsors/overtrue)

如果你喜欢我的项目并想支持它，[点击这里 :heart:](https://github.com/sponsors/overtrue)

## 由 JetBrains 赞助

非常感谢 Jetbrains 为我提供的 IDE 开源许可，让我完成此项目和其他开源项目上的开发工作。

[![](https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg)](https://www.jetbrains.com/?from=https://github.com/overtrue)

## 可爱的贡献者们

<a href="https://github.com/w7corp/easywechat/graphs/contributors"><img src="https://opencollective.com/wechat/contributors.svg?width=890" /></a>

## License

MIT
