<?php

namespace Godforheart\LaravelOptimizer\Kernel;

use Godforheart\LaravelOptimizer\Contracts\Factory;
use Godforheart\LaravelOptimizer\Kernel\Storage\Database;
use Godforheart\LaravelOptimizer\Kernel\Storage\Logger;
use Godforheart\LaravelOptimizer\Kernel\Storage\Platform;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class StorageManage implements Factory
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $systemStorage = [];

    /**
     * @var array
     */
    private $customStorage = [];

    /**
     * Create a new Log manager instance.
     *
     * @param array $config
     * @return void
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function driver($name = null)
    {
        if ($name == null) {
            $name = Arr::get($this->config, 'default');
        }

        return $this->resolve($name);
    }

    public function resolve($name)
    {
        $config = $this->configurationFor($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Log [{$name}] is not defined.");
        }

        if (isset($this->customStorage[$config['driver']])) {
            return $this->callCustomCreator($config);
        }

        $driverMethod = 'create' . ucfirst($config['driver']) . 'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        }

        throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
    }

    public function configurationFor($name)
    {
        return Arr::get($this->config, "storage.$name", '');
    }

    /**
     * Call a custom driver creator.
     *
     * @param array $config
     * @return mixed
     */
    protected function callCustomCreator(array $config)
    {
        return $this->customStorage[$config['driver']]($config);
    }

    protected function createLoggerDriver(): Logger
    {
        return new Logger();
    }

    protected function createPlatformDriver(): Platform
    {
        return (new Platform())->setConfig($this->configurationFor(Arr::get($this->config, 'default')));
    }

    protected function createDatabaseDriver(): Database
    {
        return (new Database())->setConfig($this->configurationFor(Arr::get($this->config, 'default')));
    }
}