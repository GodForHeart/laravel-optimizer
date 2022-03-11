<?php

namespace Godforheart\LaravelOptimizer\Kernel\Rule;

use Illuminate\Contracts\Cache\Repository as Cache;

abstract class RuleAbstract
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var int
     */
    protected $rate;

    public function __construct(Cache $cache, int $rate)
    {
        $this->cache = $cache;
        $this->rate = $rate;
    }
}