<?php

namespace Godforheart\LaravelOptimizer\Factory;

use Godforheart\LaravelOptimizer\Kernel\Strategy\Ip;
use Godforheart\LaravelOptimizer\Kernel\Strategy\Request;
use Godforheart\LaravelOptimizer\Kernel\Strategy\Uri;
use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;

class StrategyFactory
{
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function createStrategy($strategy)
    {
        $driverMethod = 'create' . ucfirst($strategy) . 'Driver';
        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}();
        }

        throw new InvalidArgumentException("Strategy [$strategy] is not supported.");
    }

    public function createRequestDriver(): Request
    {
        return new Request();
    }

    public function createIpDriver(): Ip
    {
        return new Ip();
    }

    public function createUriDriver(): Uri
    {
        return new Uri();
    }
}