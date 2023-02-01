<?php

return [
    /**
     * 默认渠道
     */
    'default' => 'weWorkBot',
    /**
     * 所有渠道定义
     * key 为渠道的 name
     * class 是渠道的 Channel 类
     * 其他配置见 Channel 类下的 $config 参数
     */
    'channels' => [
        /*'weWorkBot' => [
            'class' => \Kriss\Notification\Channels\WeWorkBotChannel::class,
            'key' => '3f0f8cc3-xxxx-xxxx-xxxx-a86ff4a080b8',
        ],*/
    ],
    /**
     * 日志相关
     */
    'log' => [
        /**
         * 是否启用日志
         */
        'enable' => false,
        /**
         * PSR3 LoggerInterface 的实现
         * callable|null
         */
        'instance' => null,
        /**
         * 渠道，当 instance 为 null 时，不同框架下可以用于切换渠道
         */
        'channel' => null,
    ],
    /**
     * 缓存相关
     */
    'cache' => [
        /**
         * PSR16 CacheInterface 的实现
         * callable|null
         */
        'instance' => null,
        /**
         * 驱动，当 instance 为 null 时，不同框架下可以用于切换驱动
         */
        'driver' => null,
    ],
    /**
     * 异常相关
     */
    'exception' => [
        /**
         * 接管异常处理
         * callable|null|className
         */
        'handler' => null,
        /**
         * 当 handler 为 null 时，是否抛出异常
         */
        'throw' => true,
    ],
    /**
     * 模版相关
     */
    'template' => [
        /**
         * 统一处理模版的 toString
         * callable|null|className
         */
        'handler' => null,
    ],
];