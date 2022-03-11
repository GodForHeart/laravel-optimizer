<h1 align="center"> laravel-optimizer </h1>

<p align="center"> .</p>

# [LaravelOptimizer](https://www.juli-jianzhan.com)

📦 一个 PHP 日志服务 SDK。

## 环境需求

- PHP >= 7.2

## 安装

```bash
composer require godforheart/laravel-optimizer
```

## 生成 config 配置文件及迁移文件（数据库日志类型使用）
```bash
php artisan optimizer:publish
```

## 使用方式（app/Http/Kernel.php 中增加【OptimizerLog】中间件）
```php
<?php

namespace App\Http;

use Godforheart\LaravelOptimizer\Middleware\OptimizerLog;
...

class Kernel extends HttpKernel
{
    protected $middleware = [
        ...
        OptimizerLog::class
    ];
}

```


## 贡献

你可以通过以下三种方式之一做出贡献:

1. 使用 [issue tracker](https://github.com/godforheart/laravel-optimizer/issues).
2. 回答问题或修复bug [issue tracker](https://github.com/godforheart/laravel-optimizer/issues).
3. 贡献新功能.

## 许可证

MIT
