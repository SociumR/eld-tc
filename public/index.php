<?php

if(!extension_loaded('phalcon')) {
    throw new Exception('Phalcon extension not found');
}

use App\Common\AbstractHttpBody;
use Phalcon\Config;
use Phalcon\Mvc\Micro;

error_reporting(1);

try {
    date_default_timezone_set('Europe/Kiev');
    // Loading Configs

    require __DIR__ . '/../app/config/loader.php';

    $config = new Config(require __DIR__ . '/../app/config/config.php');

    if (!file_exists(__DIR__ . '/../app/config/config-local.php')) {
        echo 'Not found: ' . __DIR__ . '/../app/config/config-local.php';
        exit(1);
    }

    $configLocal = new Config (require __DIR__ . '/../app/config/config-local.php');
    $config->merge($configLocal);


    // Initializing DI container
    /** @var \Phalcon\DI\FactoryDefault $di */
    $di = require __DIR__ . '/../app/config/di.php';

    $app = new Micro();

    $app->setDI($di);

    require __DIR__ . '/../app/config/routes.php';


    foreach ($config->get('cors') as $header => $value)
    {
        if(is_object($value)) {
            $app->response->setHeader($header, implode(', ', get_object_vars($value)));

        } else {
            $app->response->setHeader($header, (string) $value);
        }

    }

    $app->response->setHeader('Origin', $app->request->getHeader('Origin'));
    $app->response->setHeader('Access-Control-Allow-Origin', $app->request->getHeader('Origin'));
    if($app->request->getMethod() == 'OPTIONS') {
        $app->response->setStatusCode(200);
        $app->response->send();
        exit();
    }

    $app->before(
        function () use ($app, $config) {
            $whiteRequest = false;
            if ($config->get('whiteListMethods')) {
                $whiteRequest = in_array($app->router->getMatchedRoute()->getPattern(), (array)$config->get('whiteListMethods'));
            }

            if (!$whiteRequest) {
                /** @var \App\Components\Authorization $auth */
                $auth = $app->di->get('authorization');
                if (!$auth->isAuthorized()) {
                    $app->response->setStatusCode('401', 'Not authorized');
                    $app->response->setContent(json_encode([
                        AbstractHttpBody::KEY_MESSAGE => 'Not authorized, or IP address is not allowed',
                    ]));
                    $app->response->send();
                    exit();
                }

                /** @var \App\Components\RBAC $rbac */
                $rbac = $app->di->get('rbac');
                $roleAccess = $rbac::validate($app);

                if (!$roleAccess) {
                    $app->response->setStatusCode('403', 'Forbidden');
                    $app->response->send();
                    exit();
                }
            }
        }
    );
    $app->after(
        function () use ($app) {

            // Getting the return value of method
            $return = $app->getReturnedValue();

            if($return === false) {

            } else {
                $app->response->setStatusCode('200', 'OK');
                if($return !== true) {
                    $app->response->setContent(json_encode($return));
                }
            }

        }
    );

    $app->handle();
    return $app->response->send();

} catch (\Phalcon\Http\Request\Exception $e) {
    $app->response->setStatusCode(400, 'Bad request')
        ->setJsonContent([
            AbstractHttpBody::KEY_MESSAGE => $e->getMessage(),
        ]);
    $app->response->send();
} catch (\Exception $e) {
    // Standard error format
    $result = [
        AbstractHttpBody::KEY_MESSAGE => $e->getMessage()
    ];

    $app->response->setStatusCode($e->getCode() ?: 500, 'Internal Server Error')
        ->setJsonContent($result);
    $app->response->send();
}