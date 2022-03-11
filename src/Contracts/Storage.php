<?php

namespace Godforheart\LaravelOptimizer\Contracts;

interface Storage
{
    //  持久化实现
    public function persist(array $log);
}