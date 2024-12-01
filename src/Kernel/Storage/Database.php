<?php

namespace Godforheart\LaravelOptimizer\Kernel\Storage;

use Godforheart\LaravelOptimizer\Contracts\Storage;
use Illuminate\Support\Facades\DB;

class Database extends StorageAbstract implements Storage
{
    public function persist(array $log)
    {
        $responseContent = $log['response_content'] ?? [];
        if (is_string($responseContent) && !$this->isJson($responseContent)){
            $responseContent = [$responseContent];
        }

        DB::connection($this->config['channels'])->table($this->config['table'])
            ->insert([
                'request_id' => $log['request_id'] ?? '',
                'api_uri' => $log['api_uri'] ?? '',
                'method' => $log['request_method'] ?? '',
                'millisecond' => ($log['time'] ?? 0) * 1000,
                'business_millisecond' => ($log['business_time'] ?? 0) * 1000,
                'execution_sql' => json_encode($log['logs'] ?? []),
                'sql_count' => count($log['logs'] ?? []),
                'request_params' => json_encode($log['request_params'] ?? []),
                'request_params_length' => mb_strlen(json_encode($log['request_params'] ?? [])),
                'response_content' => is_string($responseContent) ? $responseContent : json_encode($responseContent),
                'response_content_length' => is_string($responseContent) ? mb_strlen($responseContent) : mb_strlen(json_encode($responseContent)),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }
    public function isJson($value): bool
    {

        if (! is_string($value)) {
            return false;
        }

        json_decode($value);

        return json_last_error() == JSON_ERROR_NONE;
    }
}
