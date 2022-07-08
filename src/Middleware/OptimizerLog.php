<?php

namespace Godforheart\LaravelOptimizer\Middleware;

use Closure;
use Godforheart\LaravelOptimizer\Kernel\OptimizerLimiter;
use Godforheart\LaravelOptimizer\OptimizerRedisServiceProvider;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Redis\Events\CommandExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class OptimizerLog
{
    /**
     * @var OptimizerLimiter
     */
    private $optimizerLimiter;
    private $logs;
    private $redisLogs;

    public function __construct(OptimizerLimiter $optimizerLimiter)
    {
        $this->optimizerLimiter = $optimizerLimiter;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->logs = [];
        $this->redisLogs = [];
        if (!$this->optimizerLimiter->allowStorage($request)) {
            return $next($request);
        }

        $businessStartTime = microtime(true);

        if (!defined('LARAVEL_START')) {
            $apiStartTime = microtime(true);
        } else {
            $apiStartTime = LARAVEL_START;
        }

        $requestParams = $responseContent = [];

        if (!$this->optimizerLimiter->isSafeMode()) {
            $requestParams = $request->except((array)config()->get('optimizer.except_request_key'));
        }

        array_map(function ($removeKey) use (&$requestParams) {
            Arr::set($requestParams, $removeKey, 'This is a file');
        }, array_keys(Arr::dot($request->allFiles())));

        $this->listenSql();
        $this->listenRedis();

        $response = $next($request);

        try {
            if (!$this->optimizerLimiter->isSafeMode() && $response instanceof JsonResponse) {
                $responseContent = $response->getContent();
            }

            $endTime = microtime(true);

            $log = [
                'api_uri' => $request->path(),
                'request_method' => $request->method(),
                'time' => $endTime - $apiStartTime,
                'business_time' => $endTime - $businessStartTime,
                'request_params' => $requestParams,
                'response_content' => $responseContent,
                "logs" => $this->logs,
                "redis_logs" => $this->redisLogs,
            ];

            if ($additionalRequestParams = (array)config()->get('optimizer.additional_request_params')) {
                foreach ($additionalRequestParams as $key => $value) {
                    if ($value instanceof Closure) {
                        $log[$key] = call_user_func($value);
                    } else {
                        $log[$key] = $value;
                    }
                }
            }

            $this->optimizerLimiter->persist($log);
        } catch (Throwable $throwable) {
            Log::error($throwable);
        } finally {
            return $response;
        }
    }

    public function listenSql()
    {
        DB::listen(function (QueryExecuted $query) {
            $newLog = [
                "sql" => $query->sql,
                "bindings" => $query->bindings,
                "time" => $query->time / 1000,
                "connection" => $query->connectionName,
            ];
            if ($this->optimizerLimiter->isSafeMode()) {
                $newLog['md5_sql'] = md5($newLog['sql']);

                unset($newLog['sql'], $newLog['bindings'], $newLog['connection']);
            }

            $this->logs[] = $newLog;

            $this->optimizerLimiter->persistSingleSql($newLog);
        });
    }

    public function listenRedis()
    {
        if (in_array(OptimizerRedisServiceProvider::class, config('app.providers'))) {
            $callback = function (CommandExecuted $commandExecuted) {
                $newRedisLog = [
                    'time' => bcdiv($commandExecuted->time, 1000, 5),
                    'command' => $commandExecuted->command,
                    'parameters' => $commandExecuted->parameters,
                    'connection_name' => $commandExecuted->connectionName,
                ];
                $this->redisLogs[] = $newRedisLog;

                $this->optimizerLimiter->persistSingleRedisCommand($newRedisLog);
            };

            app('redis')->connection()->listen($callback);
            app('redis')->connection('cache')->listen($callback);
        }
    }
}
