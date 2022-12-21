<?php

namespace Godforheart\LaravelOptimizer\Kernel;

use Godforheart\LaravelOptimizer\Contracts\Rule;
use Godforheart\LaravelOptimizer\Contracts\Storage;
use Godforheart\LaravelOptimizer\Contracts\Strategy;
use Godforheart\LaravelOptimizer\Jobs\OptimizerPersistJob;
use Godforheart\LaravelOptimizer\Kernel\Storage\Platform;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OptimizerLimiter
{
    /**
     * @var array
     */
    private $config;

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
        array    $config,
        Storage  $storage,
        Strategy $strategy,
        Rule     $rule
    )
    {
        $this->config = $config;
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
        if (!Arr::get($this->config, 'enable')) {
            return false;
        }
        if (in_array($request->getRequestUri(), (array)Arr::get($this->config, 'ignore_uri'))) {
            return false;
        }

        $rate = intval(Arr::get($this->config, 'limiter.rate'));
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

        if (mb_strlen(json_encode($log['request_params'])) > Arr::get($this->config, 'max_request_length')) {
            $log['request_params'] = [];
        }

        if (mb_strlen(json_encode($log['response_content'])) > Arr::get($this->config, 'max_response_length')) {
            $log['response_content'] = [];
        }

        if (Arr::get($this->config, 'persist_way') == 'sync') {
            dispatch_sync(new OptimizerPersistJob($this->storage, 'persist', $log));
        } else {
            dispatch(new OptimizerPersistJob($this->storage, 'persist', $log));
        }

        if (!($this->storage instanceof Platform) && Arr::get($this->config, 'enable_platform_log') == true) {
            $platformConfig = array_merge($this->config, ['default' => 'platform']);

            if (Arr::get($this->config, 'persist_way') == 'sync') {
                dispatch_sync(new OptimizerPersistJob((new StorageManage($platformConfig))->driver(), 'persist', $log));
            } else {
                dispatch(new OptimizerPersistJob((new StorageManage($platformConfig))->driver(), 'persist', $log));
            }
        }
    }

    public function isSafeMode(): bool
    {
        return (bool)Arr::get($this->config, 'safe_mode');
    }

    public function persistSingleSql(array $singleSql)
    {
        [$sql, $bindings, $connection, $time] = [
            Arr::get($singleSql, 'sql', ''),
            Arr::get($singleSql, 'bindings', []),
            Arr::get($singleSql, 'connection', ''),
            Arr::get($singleSql, 'time', 0)
        ];

        $tmp = $sql;
        $tmp = str_replace('%', '%%', $tmp);
        $tmp = str_replace('?', '%s', $tmp);
        $tmp = vsprintf(
            $tmp,
            collect($bindings)->map(function ($item) {
                if (is_string($item)) {
                    return '"' . $item . '"';
                } elseif (is_bool($item)) {
                    return (int)$item;
                } elseif ($item instanceof \DateTime) {
                    return $item->format('Y-m-d H:i:s');
                }
                return $item;
            })->toArray()
        );
        $tmp = str_replace("\\", "", $tmp);

        $job = new OptimizerPersistJob(
            $this->storage,
            'persistSingleSql',
            '[connection:' . $connection . '] execution times: ' . $time * 1000 . 'ms; ' . $tmp . "\n\t"
        );

        if (Arr::get($this->config, 'persist_way') == 'sync') {
            dispatch_sync($job);
        } else {
            dispatch($job);
        }
    }

    public function persistSingleRedisCommand(array $singleSql)
    {
        //  修复监听redis事件本身队列事件
        if (Arr::get($singleSql, 'command', '') == 'eval') {
            if (current(explode(':', Arr::get($singleSql, 'parameters.1.0', ''))) == 'queues') {
                return;
            }
        }

        [$time, $command, $parameters, $connectionName] = [
            Arr::get($singleSql, 'time', 0),
            Arr::get($singleSql, 'command', ''),
            Arr::get($singleSql, 'parameters', []),
            Arr::get($singleSql, 'connection_name', ''),
        ];

        $tmp = $command . ' ' . collect($parameters)->map(function ($item) {
                if (is_string($item)) {
                    return '"' . $item . '"';
                } elseif (is_bool($item)) {
                    return (int)$item;
                } elseif ($item instanceof \DateTime) {
                    return $item->format('Y-m-d H:i:s');
                }
                return $this->serialize($item);
            })->implode(' ');

        $job = new OptimizerPersistJob(
            $this->storage,
            'persistSingleRedis',
            '[connection:' . $connectionName . '] execution times: ' . $time * 1000 . 'ms; ' . $tmp . "\n\t"
        );

        if (Arr::get($this->config, 'persist_way') == 'sync') {
            dispatch_sync($job);
        } else {
            dispatch($job);
        }
    }

    protected function serialize($value)
    {
        return is_numeric($value) && !in_array($value, [INF, -INF]) && !is_nan($value) ? $value : serialize($value);
    }
}
