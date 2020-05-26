<?php

use Phalcon\Cache\Backend\Redis as Redis;
use Phalcon\Cache\Frontend\Data as FrontendData;
use Phalcon\Db\Adapter\Mongo\Client;

$di = new \Phalcon\DI\FactoryDefault();

$di->setShared('config', $config);


$di->setShared(
    'response',
    function () {
        $response = new Phalcon\Http\Response();
        $response->setContentType('application/json', 'utf-8');

        return $response;
    }
);



$di->set('collectionManager', function(){
    return new Phalcon\Mvc\Collection\Manager();
}, true);

$di->set('mongo', function () use ($config) {

    if (!$config->get('mongo')->username || !$config->get('mongo')->password) {
        $dsn = sprintf(
            'mongodb://%s:%s',
            $config->get('mongo')->host,
            $config->get('mongo')->port
        );
    } else {
        $dsn = sprintf(
            'mongodb://%s:%s@%s:%s',
            $config->get('mongo')->username,
            $config->get('mongo')->password,
            $config->get('mongo')->host,
            $config->get('mongo')->port
        );
    }
    $mongo = new Phalcon\Db\Adapter\MongoDB\Client($dsn);
    return $mongo->selectDatabase($config->get('mongo')->dbname);
}, true);

$di->setShared(
    'modelsCache',
    function () use ($config) {
        if (!$config->get('redis')) {
            throw new \Phalcon\Db\Exception('Configuration for redis not found');
        }

        if(!extension_loaded('redis')) {
            throw new Exception('Redis extension not loaded');
        }

        // По умолчанию данные кэша хранятся один день
        $frontCache = new FrontendData(
            [
                'lifetime' => 86400,
            ]
        );

        $cache = new Redis(
            $frontCache,
            (array)$config->get('redis')
        );

        return $cache;
    }
);


$di->setShared(
    'authorization',
    function () use ($config) {
        $cacheTime = $config->get('authorizedCache') ?: 1;
        $authorization = new \App\Components\Authorization($cacheTime);
        return $authorization;
    }
);

$di->setShared(
    'elastic',
    function () use ($config) {

        if (!$config->get('elasticSearch')) {
            throw new \Phalcon\Db\Exception('Configuration for ElasticSearch not found');
        }

        $elastic = new \App\Components\ElasticSearch(
            $config->get('elasticSearch')
        );
        return $elastic;
    }
);

$di->set(
    'rbac',
    function () {
        return new \App\Components\RBAC();
    }
);

$di->set(
    'story',
    function () {
        return new \App\Components\Story();
    }
, true);

$di->set(
    'translation',
    function () {
        return (new \App\Components\Locale())->getTranslator();
    }
    , true);



return $di;