<?php

return [
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=localhost:3306;dbname=yii2advanced',
            'username' => 'yii2advanced',
            'password' => 'secret',
            'charset' => 'utf8',
        ],
    ],
];
