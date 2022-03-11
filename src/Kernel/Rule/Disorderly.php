<?php

namespace Godforheart\LaravelOptimizer\Kernel\Rule;

use Godforheart\LaravelOptimizer\Contracts\Rule;

class Disorderly extends RuleAbstract implements Rule
{
    public function isThrough(string $cacheKey): bool
    {
        return in_array(random_int(0, 100), range(1, $this->rate));
    }
}