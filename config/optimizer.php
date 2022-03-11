<?php
return [
    //  是否开启
    'enable' => (bool)env('OPTIMIZER_ENABLE', true),

    //  默认日志存储类型
    'default' => env('OPTIMIZER_DEFAULT', 'logger'),

    //  是否安全模式，安全模式下，不会记录请求参数和响应内容，以及只记录MD5(sql)
    'safe_mode' => (bool)env('OPTIMIZER_SAFE_MODE', false),

    //  最大请求参数记录长度（超过不记录），平台方日志最大值为【32768】，请根据实际情况处理
    'max_request_length' => (int)env('OPTIMIZER_MAX_REQUEST_LENGTH', 32768),

    //  最大响应内容记录长度（超过不记录），平台方日志最大值为【32768】，请根据实际情况处理
    'max_response_length' => (int)env('OPTIMIZER_MAX_RESPONSE_LENGTH', 32768),

    //  忽略的请求key，防止隐私字段：如【password】字段加入到日志中
    'except_request_key' => [
        'password',
    ],

    /*
     * storage(存储)：可自定义driver
     *          logger：本地日志
     *          database：数据库
     *          platform：日志分析平台
    */
    'storage' => [
        //  全量日志
        'logger' => [
            'driver' => 'logger',
            'channels' => env('LOG_CHANNEL', 'stack'),
        ],
        'database' => [
            'driver' => 'database',
            'channels' => env('DB_CONNECTION', 'mysql'),
            'table' => 'optimizer_logs'
        ],
        'platform' => [
            'driver' => 'platform',
            'platform_id' => env('OPTIMIZER_PLATFORM_ID', ''),
            'project_id' => env('OPTIMIZER_PROJECT_ID', ''),
            //  当前只支持【MD5】，请勿修改
            'sign_type' => env('OPTIMIZER_SIGN_TYPE', 'MD5'),
            'timeout' => 1
        ],
    ],

    'limiter' => [
        /**
         * strategy(日志策略)：
         *          request：独立请求
         *          ip：根据独立ip
         *          uri：根据相同uri
         */
        'strategy' => env('OPTIMIZER_LIMITER_STRATEGY', 'request'),

        /**
         * rule(日志规则)：
         *          orderly：有序的
         *          disorderly：无序的，伪随机（请注意，大方向上会超过设定的占比）
         */
        'rule' => env('OPTIMIZER_LIMITER_RULE', 'orderly'),

        /**
         * rate(占比)：一百次中记录多少次，1-100
         */
        'rate' => (int)env('OPTIMIZER_LIMITER_RATE', 50),
    ]
];