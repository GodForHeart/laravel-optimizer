<?php

namespace Godforheart\LaravelOptimizer\Contracts;

interface Rule
{
    public function isThrough(string $cacheKey): bool;
}