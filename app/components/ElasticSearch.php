<?php

namespace App\Components;


use Phalcon\Http\Request\Exception;

class ElasticSearch extends Curl
{
    private $_host;

    private $_username;

    private $_password;

    private $_port;

    protected $_query;

    protected $_index;

    protected $_type;

    protected $_body;


    /**
     * ElasticSearch constructor.
     * @param $config
     * @throws Exception
     */
    public function __construct($config)
    {

        $this->_host = $config['host'] ?: 'http://localhost';
        $this->_port = $config['port'] ?: 9200;

        $this->setServer($this->_host . ':' . $this->_port);

        $this->setMethod(Curl::METHOD_TYPE_GET);

        if ($config['username']) {
            $this->_username = $config['username'];
            $this->setLogin($this->_username);
        }

        if ($config['password']) {
            $this->_password = $config['password'];
            $this->setPassword($this->_password);
        }

        if (!$this->serverExists()) {
            throw new Exception('ElasticSearch server not found');
        }

    }

    public function setQuery($query)
    {

        if (isset($query['index'])) {
            $this->setIndex($query['index']);
            unset($query['index']);
        }

        if (isset($query['type'])) {
            $this->setType($query['type']);
            unset($query['type']);
        }

        if (isset($query['body'])) {
            $this->setBody($query['body']);
            unset($query['body']);
        }

        $this->_query = $query;
    }

    public function setIndex($index)
    {
        $this->_index = $index;
        return $this;
    }

    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    public function setBody($body)
    {
        $this->_body = $body;
        return $this;
    }

    /**
     * @param $index
     * @param $type
     * @param null $mapping
     * @return bool
     * @throws Exception
     */
    public function createIndex($index, $type, $mapping = null)
    {
        $this->setMethod(Curl::METHOD_TYPE_PUT);
        $this->setAction('/' . $index);
        $this->setParams([
            'mappings' => [
                $type => [
                    'properties' => $mapping
                ]
            ]
        ]);


        $this->run();

        return $this->getStatusCode() == '200' ? true : false;
    }


    /**
     * @param $index
     * @return bool
     * @throws Exception
     */
    public function issetIndex($index)
    {

        $this->setMethod(Curl::METHOD_TYPE_HEAD);
        $this->setAction('/' . $index);
        $this->run();

        $response = $this->getStatusCode();

        $result = $response === '200' ? true : false;


        return $result;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function serverExists()
    {
        $this->setMethod(Curl::METHOD_TYPE_GET);
        $this->setAction('/');
        $this->run();
        $response = $this->getStatusCode();
        return $response == "200";

    }

    /**
     * @throws Exception
     */
    public function save()
    {

        $this->setMethod(Curl::METHOD_TYPE_POST);

        if (!$this->_index || !$this->_type) {
            throw new Exception('Must set index and type', 400);
        }

        $this->setAction('/' . $this->_type . '/' . $this->_index);
        $this->setParams($this->_body);
        return $this->getJson();

    }
}