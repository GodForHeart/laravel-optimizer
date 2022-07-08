<?php

namespace Godforheart\LaravelOptimizer;

use Godforheart\LaravelOptimizer\Events\RedisEvent;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Redis\RedisManager;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class OptimizerRedisServiceProvider extends \Illuminate\Redis\RedisServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->singleton('redis', function ($app) {
            $config = $app->make('config')->get('database.redis', []);

            $redis = new RedisManager($app, Arr::pull($config, 'client', 'phpredis'), $config);

            //  增加cache门面redis事件，默认redis事件
            $redis->connection('cache')->setEventDispatcher(new RedisEvent());
            $redis->connection()->setEventDispatcher(new RedisEvent());

            return $redis;
        });
    }
}
