<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
            'enableCookieValidation' => false,
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null
        ],
        'session' => [
            // this is the name of the session cookie used for login on the api
            'name' => 'advanced-api',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'v1/author/delete/<id:([0-9]+)>' => 'v1/author/delete',
                'v1/genre/delete/<id:([0-9]+)>' => 'v1/genre/delete',
                'v1/book/delete/<id:([0-9]+)>' => 'v1/book/delete',
                'v1/author/update/<id:([0-9]+)>' => 'v1/author/update',
                'v1/genre/update/<id:([0-9]+)>' => 'v1/genre/update',
                'v1/book/update/<id:([0-9]+)>' => 'v1/book/update',
            ],
        ],
    ],
    'params' => $params,
];
