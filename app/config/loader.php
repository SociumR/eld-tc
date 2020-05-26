<?php

$loader = new \Phalcon\Loader();
$loader->registerNamespaces(
    [
        'App\Components' => realpath(__DIR__ . '/../components/'),
        'App\Services' => realpath(__DIR__ . '/../services/'),
        'App\Controllers' => realpath(__DIR__ . '/../controllers/'),
        'App\Models' => realpath(__DIR__ . '/../models/'),
        'App\Common' => realpath(__DIR__ . '/../common/'),
        'App\Exceptions' => realpath(__DIR__ . '/../common/Exceptions'),
        'App\Adapters' => realpath(__DIR__ . '/../adapters'),
        'App\Interfaces' => realpath(__DIR__ . '/../interfaces'),
        'App\Validators' => realpath(__DIR__ . '/../validators'),
        'App\Helpers' => realpath(__DIR__ . '/../helpers'),
        'App\System' => realpath(__DIR__ . '/../system'),
    ]
);

$loader->registerFiles([
    realpath(__DIR__ . '/../../vendor/autoload.php')
]);

$loader->register();