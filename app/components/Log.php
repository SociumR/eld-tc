<?php

namespace App\Components;

use App\Models\ApiLogs;
use Phalcon\Http\Request;
use Phalcon\Http\Request\Exception;


class Log
{
    /**
     * @param $app
     * @throws Exception
     */
    public static function save($app)
    {

        $logModel = self::getModel($app);

        /** @var ElasticSearch $el */

        $el = $app->di->get('elastic');

        try {

            $indexIsset = $el->issetIndex($logModel->getSource());

            if (!$indexIsset) {
                $mapping = [
                    'datetime' => [
                        'type' => 'date',
                        'format' => 'yyyy-MM-dd H:m:s'
                    ]
                ];

                $el->createIndex($logModel->getSource(), $logModel->getSource(), $mapping);
            }

            $el
                ->setType($logModel->getSource())
                ->setIndex($logModel->getSource())
                ->setBody($logModel->toArray())
                ->save();
        } catch (Request\Exception $e) {
            throw $e;
        }
    }

    /**
     * Функція генерує екземпляр класу логування
     * @param $app
     * @return ApiLogs
     */
    public static function getModel($app)
    {

        /** @var \Phalcon\Http\Request $request */
        /** @var \Phalcon\Http\Response $response */
        /** @var \Phalcon\Mvc\Router $router */

        $request = $app->request;
        $response = $app->response;
        $router = $app->router;

        /** @var Authorization $agent */
        $agent = $app->authorization;

        $logModel = new ApiLogs();
        $logModel->setDatetime((new \DateTime())->format('Y-m-d H:i:s'));

        if($agent->getUser()) {
            $logModel->setAgentId((integer) $agent->getUserId());
        }

        $logModel->setAuthToken($agent->getAuthToken());
        $logModel->setRequestPattern($router->getMatchedRoute()->getPattern());
        $logModel->setRequestType($request->getMethod());
        $logModel->setRefererIp($request->getClientAddress());
        $logModel->setRequest($router->getRewriteUri());
        $logModel->setRequestBody(json_encode($request->getJsonRawBody(), JSON_UNESCAPED_SLASHES));
        $logModel->setRequestHeaders(json_encode($request->getHeaders(), JSON_UNESCAPED_SLASHES));
        $logModel->setResponseHeaders(json_encode((array) $response->getHeaders(), JSON_UNESCAPED_SLASHES));
        $logModel->setResponseBody($response->getContent());

        $logModel->setResponseStatus($response->getStatusCode());

        return $logModel;
    }
}