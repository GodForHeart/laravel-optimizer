<?php
return [
    //  是否开启扩展
    'enable' => (bool)env('OPTIMIZER_ENABLE', true),

    //  是否开启平台日志（可 本地日志+平台日志 双线）
    'enable_platform_log' => (bool)env('OPTIMIZER_ENABLE_PLATFORM_LOG', false),

    /**
     * 默认日志存储类型，默认日志文件，可选：
     *          logger：本地日志
     *          database：数据库
     *          platform：日志分析平台
     */
    'default' => env('OPTIMIZER_DEFAULT', 'logger'),

    //  是否安全模式，安全模式下，不会记录请求参数和响应内容，以及只记录MD5(sql)
    'safe_mode' => (bool)env('OPTIMIZER_SAFE_MODE', false),

    //  最大请求参数记录长度（超过不记录），平台方日志最大值为【32768】，请根据实际情况处理
    'max_request_length' => (int)env('OPTIMIZER_MAX_REQUEST_LENGTH', 32768),

    //  最大响应内容记录长度（超过不记录），平台方日志最大值为【32768】，请根据实际情况处理
    'max_response_length' => (int)env('OPTIMIZER_MAX_RESPONSE_LENGTH', 32768),

    //  持久化方式，【sync：同步持久化】，【queue：使用队列（具体队列执行方式根据queue）】
    'persist_way' => env('OPTIMIZER_PERSIST_WAY', 'sync'),

    //  忽略的接口地址
    'ignore_uri' => [
//        '/auth/login'
    ],

    //  忽略的请求key，防止隐私字段：如【password】字段加入到日志中
    'except_request_key' => [
        'password',
    ],

    //  额外日志参数（公共额外参数请注意防止和业务请求参数同key导致的问题，如user_id字段比较常用，记录访问用户id，可用【additional_request_login_user_id】来替代）
    'additional_request_params' => [
        //  示例：日志增加记录访问用户id
//        'user_id' => function () {
//            return app(\Illuminate\Http\Request::class)->user()?->id;
//        }
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
            //  日志类型
            'channels' => env('OPTIMIZER_LOG_CHANNEL', env('LOG_CHANNEL', 'stack')),
            //  是否开启单条sql日志，默认开启
            'single_sql' => (bool)env('LOG_SINGLE_SQL', true),
            //  是否开启单条redis日志，默认关闭
            'single_redis' => (bool)env('LOG_SINGLE_REDIS', false),
        ],
        //  数据库日志模式
        'database' => [
            'driver' => 'database',
            'channels' => env('DB_CONNECTION', 'mysql'),
            //  默认表名，如更改迁移文件表名，请对应更改
            'table' => 'optimizer_logs'
        ],
        'platform' => [
            'driver' => 'platform',
            'platform_id' => env('OPTIMIZER_PLATFORM_ID', ''),
            'platform_secret' => env('OPTIMIZER_PLATFORM_SECRET', ''),
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
        'rate' => (int)env('OPTIMIZER_LIMITER_RATE', 100),
    ]
];
