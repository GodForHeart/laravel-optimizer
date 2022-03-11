<?php

namespace Godforheart\LaravelOptimizer\Kernel\Storage;

use Godforheart\LaravelOptimizer\Contracts\Storage;

class Logger extends StorageAbstract implements Storage
{
    public function persist(array $log)
    {
        app('log')->info('optimizer', $log);
    }
}