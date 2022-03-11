<?php

namespace Godforheart\LaravelOptimizer\Kernel;

use Godforheart\LaravelOptimizer\Contracts\Factory;
use Godforheart\LaravelOptimizer\Kernel\Storage\Database;
use Godforheart\LaravelOptimizer\Kernel\Storage\Logger;
use Godforheart\LaravelOptimizer\Kernel\Storage\Platform;
use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;

class StorageManage implements Factory
{
    /**
     * @var Application
     */
    private $app;

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
     * @param Application $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function driver($name = null)
    {
        if ($name == null) {
            $name = $this->app['config']->get('optimizer.default');
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
        return $this->app['config']->get("optimizer.storage.$name", '');
    }

    /**
     * Call a custom driver creator.
     *
     * @param array $config
     * @return mixed
     */
    protected function callCustomCreator(array $config)
    {
        return $this->customStorage[$config['driver']]($this->app, $config);
    }

    protected function createLoggerDriver(): Logger
    {
        return new Logger();
    }

    protected function createPlatformDriver(): Platform
    {
        return (new Platform())->setConfig($this->configurationFor($this->app['config']->get('optimizer.default')));
    }

    protected function createDatabaseDriver(): Database
    {
        return (new Database())->setConfig($this->configurationFor($this->app['config']->get('optimizer.default')));
    }
}