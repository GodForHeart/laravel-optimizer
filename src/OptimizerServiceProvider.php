<?php

namespace Godforheart\LaravelOptimizer;

use Godforheart\LaravelOptimizer\Console\PublishCommand;
use Godforheart\LaravelOptimizer\Factory\RuleFactory;
use Godforheart\LaravelOptimizer\Factory\StrategyFactory;
use Godforheart\LaravelOptimizer\Kernel\OptimizerLimiter;
use Godforheart\LaravelOptimizer\Kernel\StorageManage;
use Illuminate\Console\Application as Artisan;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;

class OptimizerServiceProvider extends ServiceProvider
{
    /**
     * @var string[]
     */
    protected $commands = [
        PublishCommand::class
    ];

    public function register()
    {
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$this->getConfigPath() => config_path('optimizer.php')], 'laravel-optimizer-config');
            $this->publishes([$this->getMigratePath() => database_path('migrations')], 'laravel-optimizer-migrations');
        }

        $this->mergeConfigFrom(
            $this->getConfigPath(),
            'optimizer'
        );

        $this->app->singleton('optimizer.storage', function ($app) {
            return new StorageManage($app);
        });

        $this->app->singleton('optimizer.strategy', function ($app) {
            return (new StrategyFactory($app))->createStrategy($app['config']->get('optimizer.limiter.strategy'));
        });

        $this->app->singleton('optimizer.rule', function ($app) {
            return (new RuleFactory($app, $app->make('cache')->driver()))->createRule(
                $app['config']->get('optimizer.limiter.rule'),
                $app['config']->get('optimizer.limiter.rate')
            );
        });

        $this->app->singleton(OptimizerLimiter::class, function ($app) {
            return new OptimizerLimiter(
                $app,
                $app->make('optimizer.storage')->driver(),
                $app->make('optimizer.strategy'),
                $app->make('optimizer.rule')
            );
        });

        Artisan::starting(function ($artisan) {
            $artisan->resolveCommands($this->commands);
        });
    }

    public function getConfigPath(): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'optimizer.php';
    }

    public function getMigratePath(): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            dirname(__DIR__),
            'database',
            'migrations'
        ]);
    }
}