<?php

namespace Godforheart\LaravelOptimizer\Kernel\Rule;

use Godforheart\LaravelOptimizer\Contracts\Rule;

class Orderly extends RuleAbstract implements Rule
{
    public function isThrough(string $cacheKey): bool
    {
        $preCount = $this->cache->get($cacheKey, 0);
        $nextCount = $this->cache->increment($cacheKey);

        return intval($preCount * $this->rate / 100) != intval($nextCount * $this->rate / 100);
    }
}