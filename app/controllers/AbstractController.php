<?php

namespace App\Controllers;

use App\Components\Authorization;
use App\Components\Story;
use DateTime;
use Phalcon\Cache\Backend;
use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\Postgresql;
use Phalcon\DI\Injectable;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Collection\Exception;
use Phalcon\Mvc\Router;

/**
 * Class AbstractController
 *
 * @property Request $request
 * @property Router $router
 * @property Response $htmlResponse
 * @property Backend $modelsCache;
 * @property Postgresql $db
 * @property Config $config
 */
abstract class AbstractController extends Injectable
{

    private $enableCache = false;
    public $cacheTime;
    public $cacheKeyParam;
    public $cacheKey;
    protected $response;
    public $query;
    /**
     * Route not found. HTTP 404 Error
     */
    const ERROR_NOT_FOUND = 1;

    /**
     * Invalid Request. HTTP 400 Error.
     */
    const ERROR_INVALID_REQUEST = 2;

    /**
     * Errors from model
     */

    public $errors;

    /**
     * @param mixed $content
     * @param string$category
     * @param null|string $description
     * @throws Exception
     */
    public function story($content, $category, $description = null)
    {
        /** @var Story $story */
        $story = $this->di->get('story');
        $story->description = $description;
        $story->content = $content;
        $story->category = $category;

        /** @var Authorization $auth */

        $auth = $this->di->get('authorization');
        $user = $auth->getUser();

        if ($user) {
            $user = new \stdClass();
            $user->id = $auth->getUserId();
            $user->username = $auth->getUser()->getUsername();
            $story->user = $user;
        }
        $story->action = $this->router->getMatchedRoute()->getName();
        $story->date = (new DateTime())->format('Y-m-d H:i:s');

        $story->save();
    }
    public function __construct()
    {
        $this->enableCache = $this->config->get('enableCache');
        $this->isNeedCache();
        $this->buildQuery();
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response)
    {
        if ($this->enableCache) {
            $this->modelsCache->save($this->cacheKey, $response, $this->cacheTime);
        }
        return $this->response = $response;
    }

    /**
     * Перевірка на необхідність кешувати відповідь певного методу
     * @return bool
     */
    public function isNeedCache()
    {

        //$this->validatePermissions();

        if (!$this->enableCache) {
            return true;
        }

        $this->cacheKey = $this->request->getURI();
        $this->cacheKeyParam = $this->router->getMatchedRoute()->getPattern();
        $this->cacheTime = $this->config->get('routesCache')[$this->cacheKeyParam];

        if ($this->cacheTime) {
            $this->cacheKey = $this->request->getURI() . '_' . $this->cacheTime;
            $this->setResponse($this->modelsCache->get($this->cacheKey));
        }

        return true;
    }

    public function buildQuery()
    {
        $this->query = $this->request->getQuery();
        array_shift($this->query);
    }

}