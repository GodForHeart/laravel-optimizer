<?php

namespace Godforheart\LaravelOptimizer\Middleware;

use Closure;
use Godforheart\LaravelOptimizer\Kernel\OptimizerLimiter;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OptimizerLog
{
    /**
     * @var OptimizerLimiter
     */
    private $optimizerLimiter;
    private $logs;

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

        $this->listenSql();

        $response = $next($request);

        if (!$this->optimizerLimiter->isSafeMode() && $response instanceof JsonResponse) {
            $responseContent = $response->getOriginalContent();
        }

        $endTime = microtime(true);

        $this->optimizerLimiter->persist([
            'api_uri' => $request->path(),
            'request_method' => $request->method(),
            'time' => $endTime - $apiStartTime,
            'business_time' => $endTime - $businessStartTime,
            'request_params' => $requestParams,
            'response_content' => $responseContent,
            "logs" => $this->logs
        ]);

        return $response;
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
}
