<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => [
        'queue',
    ],
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'queue' => [
            'class' => \yii\queue\amqp_interop\Queue::class,
            'host' => 'localhost',
            'port' => 5672,
            'user' => 'yii2advanced',
            'password' => 'secret',
            'queueName' => 'queue',
        ],
    ],
];
