<?php

namespace Godforheart\LaravelOptimizer\Kernel\Storage;

use Godforheart\LaravelOptimizer\Contracts\Storage;
use Illuminate\Support\Arr;

class Logger extends StorageAbstract implements Storage
{
    public function persist(array $log)
    {
        app('log')->info('optimizer', $log);
    }

    public function persistSingleSql(string $singleSql)
    {
        if (Arr::get($this->config, 'single_sql') === true) {
            app('log')->info('optimizer:singleSql:' . $singleSql);
        }
    }
}