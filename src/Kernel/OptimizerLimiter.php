<?php

namespace Godforheart\LaravelOptimizer\Kernel;

use Godforheart\LaravelOptimizer\Contracts\Rule;
use Godforheart\LaravelOptimizer\Contracts\Storage;
use Godforheart\LaravelOptimizer\Contracts\Strategy;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OptimizerLimiter
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @var Strategy
     */
    protected $strategy;

    /**
     * @var Rule
     */
    protected $rule;

    public function __construct(
        Application $app,
        Storage     $storage,
        Strategy    $strategy,
        Rule        $rule
    )
    {
        $this->app = $app;
        $this->storage = $storage;
        $this->strategy = $strategy;
        $this->rule = $rule;
    }

    /**
     * 是否允许存储
     * @return bool
     */
    public function allowStorage(Request $request): bool
    {
        if (!$this->app['config']->get('optimizer.enable')) {
            return false;
        }

        $rate = intval($this->app['config']->get('optimizer.limiter.rate'));
        if ($rate <= 0 || $rate > 100) {
            throw new InvalidArgumentException("Rate range [$rate] is not supported.");
        }
        if ($rate == 100) {
            return true;
        }
        $key = 'optimizer:' . md5(get_class($this->strategy) . $this->strategy->getKey($request)) . ':limiter';

        return $this->rule->isThrough($key);
    }

    public function persist(array $log)
    {
        $log = array_merge(
            [
                "request_id" => Str::uuid()->toString(),
            ],
            $log
        );

        if (mb_strlen(json_encode($log['request_params'])) > $this->app['config']->get('optimizer.max_request_length')) {
            $log['request_params'] = [];
        }

        if (mb_strlen(json_encode($log['response_content'])) > $this->app['config']->get('optimizer.max_response_length')) {
            $log['response_content'] = [];
        }

        $this->storage->persist($log);
    }

    public function isSafeMode(): bool
    {
        return (bool)$this->app['config']->get('optimizer.safe_mode');
    }
}