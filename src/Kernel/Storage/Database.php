<?php

namespace Godforheart\LaravelOptimizer\Kernel\Storage;

use Godforheart\LaravelOptimizer\Contracts\Storage;
use Illuminate\Support\Facades\DB;

class Database extends StorageAbstract implements Storage
{
    public function persist(array $log)
    {
        DB::connection($this->config['channels'])->table($this->config['table'])
            ->insert([
                'request_id' => $log['request_id'] ?? '',
                'api_uri' => $log['api_uri'] ?? '',
                'method' => $log['request_method'] ?? '',
                'millisecond' => ($log['time'] ?? 0) * 1000,
                'execution_sql' => json_encode($log['logs'] ?? []),
                'sql_count' => count($log['logs'] ?? []),
                'request_params' => json_encode($log['request_params'] ?? []),
                'request_params_length' => mb_strlen(json_encode($log['request_params'] ?? [])),
                'response_content' => json_encode($log['response_content'] ?? []),
                'response_content_length' => mb_strlen(json_encode($log['response_content'] ?? [])),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }
}