<?php

namespace Godforheart\LaravelOptimizer\Kernel\Storage;

use Godforheart\LaravelOptimizer\Contracts\Storage;
use Illuminate\Support\Arr;

class Logger extends StorageAbstract implements Storage
{
    public function persist(array $log)
    {
        app('log')->channel(Arr::get($this->config, 'channels'))->info(
            'optimizer ' . json_encode($log, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n\t"
        );
    }

    public function persistSingleSql(string $singleSql)
    {
        if (Arr::get($this->config, 'single_sql') === true) {
            app('log')->channel(Arr::get($this->config, 'channels'))->info('optimizer:singleSql:' . $singleSql);
        }
    }

    public function persistSingleRedis(string $singleRedis)
    {
        if (Arr::get($this->config, 'single_redis') === true) {
            app('log')->channel(Arr::get($this->config, 'channels'))->info('optimizer:singleRedis:' . $singleRedis);
        }
    }
}
