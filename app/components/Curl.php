<?php

namespace App\Components;

use Phalcon\Http\Request\Exception;


/**
 * Class Connector
 * @package common\connectors
 */
class Curl
{


    const METHOD_TYPE_GET = 'GET';
    const METHOD_TYPE_POST = 'POST';
    const METHOD_TYPE_PUT = 'PUT';
    const METHOD_TYPE_DELETE = 'DELETE';
    const METHOD_TYPE_HEAD = 'HEAD';
    /**
     * @var string
     */

    private $serverUrl;

    /**
     * @var string
     */

    private $action;

    /**
     * @var array
     */

    private $params;

    /**
     * @var string
     */

    private $method = self::METHOD_TYPE_GET;

    /**
     * @var string
     */

    private $response;

    /**
     * @var array
     */

    private $headers;

    /**
     * @var string
     */

    private $login;

    /**
     * @var string
     */

    private $password;

    /**
     * @var integer
     */
    private $timeout = 5;


    private $_statusCode;

    /**
     * @param $server
     * @return $this
     */

    public function setServer($server)
    {
        $this->serverUrl = $server;
        return $this;
    }

    /**
     * @param $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @param $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }


    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param $method
     * @return $this
     */

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @param $headers array
     * @return $this
     */

    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return array
     */

    public function getHeaders()
    {
        $headers = [];
        foreach ($this->headers as $key => $header) {
            $headers[] = $key . ':' . $header;
        }
        return $headers;
    }

    /**
     * @param $login
     * @return $this
     */

    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @param $password
     * @return $this
     */

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param int $timeout
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function generateQueryString()
    {
        if ($this->method == self::METHOD_TYPE_GET) {
            $string = $this->serverUrl . '/' . $this->action;
            $params = null;
            if (!$this->params) {
                return $string;
            }
            foreach ($this->params as $key => $param) {
                if (!empty($param)) {
                    if (!$params) {
                        $params = '?' . $key . '=' . $param;
                    } else {
                        $params .= '&' . $key . '=' . $param;
                    }
                }
            }
            return $string . $params;
        }

    }


    public function getStatusCode()
    {

        return $this->_statusCode;

        $curl = curl_init($this->serverUrl . $this->action);

        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        if ($this->login && $this->password) {
            curl_setopt($curl, CURLOPT_USERPWD, $this->login . ":" . $this->password);
        }

        curl_exec($curl);

        return curl_getinfo($curl, CURLINFO_HTTP_CODE);
    }

    /**
     * Test if server exists
     */
    public function testConnection()
    {
        $curl = curl_init($this->serverUrl);

        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        if ($this->login && $this->password) {
            curl_setopt($curl, CURLOPT_USERPWD, $this->login . ":" . $this->password);
        }

        curl_exec($curl);

        // TODO тимчасова заглушка
        return curl_getinfo($curl, CURLINFO_HTTP_CODE);

        return true;
    }

    /**
     * Initialize curl and send request
     * @throws Exception
     */
    public function run()
    {
        /*$serverStatus = $this->testConnection();

        if($serverStatus != 200) {
            return false;
        }*/

        $curl = curl_init();

        if ($this->method == self::METHOD_TYPE_GET) {
            curl_setopt($curl, CURLOPT_URL, $this->generateQueryString());
        } else {
            curl_setopt($curl, CURLOPT_URL, $this->serverUrl . $this->action);
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        if ($this->method == self::METHOD_TYPE_POST) {
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($this->getParams()) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->getParams()));
            }
        }

        if ($this->method == self::METHOD_TYPE_PUT) {
            if ($this->getParams()) {
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->getParams()));
            }
        }

        if ($this->headers) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getHeaders());
        }

        if ($this->login && $this->password) {
            curl_setopt($curl, CURLOPT_USERPWD, $this->login . ":" . $this->password);
        }

        try {
            $response = curl_exec($curl);
            $this->_statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            $body = substr($response, $header_size);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $body;
    }


    /**
     * @param bool $asObject
     * @return bool|mixed|object|string
     * @throws Exception
     */
    public function getJson($asObject = true)
    {
        $response = $this->run();
        $response = json_decode($response, JSON_PRETTY_PRINT);

        return $response ? ($asObject ? (object)$response : $response) : false;
    }


}