<?php

return [
    'application' => [
        'controllersDir' => "app/controllers/",
        'modelsDir' => "app/models/",
        'baseUri' => "/",
        'superUserRole' => 'admin',
        'dir' => realpath(__DIR__ . '/../../')
    ],

    /**
     * Налаштування підключення до MongoDB
     */
    'mongo' => [
        'host' => 'mongodb',
        'port' => 27017,
        /*'username' => 'user',
        'password' => 'root',*/
        'dbname' => 'eld',
    ],
    /**
     * Налаштування підключення до Redis
     */
    'redis' => [
        'host' => 'redis',
        'statsKey' => '_PHCR'
    ],
    /**
     * Параметр статусу кешування - true = кешування увімкнено
     */
    'enableCache' => true,
    /**
     * Час життя кешу перевірки авторизації клієнта в секундах
     */
    'authorizedCache' => 1,
    /**
     * Час життя кешу по роутам в секундах
     */
    'routesCache' => [],
    /**
     * Методи для яких не потрібно перевіряти авторизацію
     */
    'whiteListMethods' => [
        '/admin/login',
        '/songs',
        '/songs/{id}',
        '/admin/permissions'
    ],
    /**
     * Налаштування ElasticSearch
     */
    'elasticSearch' => [
        'host' => 'elastic',
        'port' => 9200,
    ],
    'pages' => [
        'limits' => [
            'songs' => 50
        ]
    ],
    'cors'  => [
        // restrict access to domains:
        'Origin'      => [
            'http://192.168.166.62',
            'http://localhost',
            'http://localhost:8080',

        ],
        'Access-Control-Allow-Origin'      => [
            'http://192.168.166.62',
            'http://localhost',
            'http://localhost:8080',
        ],
        'Access-Control-Allow-Methods'    => ['POST', 'GET', 'OPTIONS', 'PUT', 'DELETE', 'UPDATE'],
        'Access-Control-Allow-Credentials' => true,
        'Access-Control-Max-Age'           => 3600,
        'Access-Control-Allow-Headers' => 'Content-Type, X-API-KEY'
    ],
];