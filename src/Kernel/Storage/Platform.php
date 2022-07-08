<?php

namespace Godforheart\LaravelOptimizer\Kernel\Storage;

use Godforheart\LaravelOptimizer\Contracts\Storage;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class Platform extends StorageAbstract implements Storage
{
    public function persist(array $log)
    {
        $log["method"] = "juli.optimizer.analyse.api";
        $log["platform_id"] = $this->config['platform_id'];
        $log["platform_secret"] = $this->config['platform_secret'];
        $log["project_id"] = $this->config['project_id'];
        $log["timestamp"] = date('Y-m-d H:i:s');
        $log["sign_type"] = $this->config['sign_type'];

        $signParam = Arr::sortRecursive(Arr::only($log, ['method', 'platform_id', 'platform_secret', 'project_id', 'timestamp',]));
        if ($this->config['sign_type'] == 'MD5') {
            $log['sign'] = md5(implode('&', $signParam));
        }

        unset($log['platform_secret']);

        $client = new Client([
            'connect_timeout' => $this->config['timeout'] ?? 1,
            'timeout' => $this->config['timeout'] ?? 1,
            'allow_redirects' => false,
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $client->post(
            "https://gateway.optimizer.juli-jianzhan.com",
            [
                'json' => $log,
            ]
        );
    }
}
