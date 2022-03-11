<?php

namespace Godforheart\LaravelOptimizer\Factory;

use Godforheart\LaravelOptimizer\Kernel\Rule\Disorderly;
use Godforheart\LaravelOptimizer\Kernel\Rule\Orderly;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;

class RuleFactory
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var Cache
     */
    protected $cache;

    public function __construct(Application $app, Cache $cache)
    {
        $this->app = $app;
        $this->cache = $cache;
    }

    public function createRule($rule, int $rate)
    {
        $driverMethod = 'create' . ucfirst($rule) . 'Driver';
        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($rate);
        }

        throw new InvalidArgumentException("Rule [$rule] is not supported.");
    }

    public function createOrderlyDriver(int $rate): Orderly
    {
        return new Orderly($this->cache, $rate);
    }

    public function createDisorderlyDriver(int $rate): Disorderly
    {
        return new Disorderly($this->cache, $rate);
    }
}