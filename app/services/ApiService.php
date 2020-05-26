<?php

namespace App\Services;

use App\Models\Partner;
use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\DI\Injectable;
use Phalcon\Http\Request\Exception;
use Phalcon\Mvc\Collection;
use Phalcon\Validation\Message\Group;

/**
 * Class AbstractService
 *
 * @property Mysql $db
 * @property Config $config
 */
abstract class ApiService extends Injectable
{

    public static $query = [
        []
    ];

    public $page = 1;
    public $pageLimit;
    public $totalPages;
    public $totalCount = 0;

    /**
     * Invalid parameters anywhere
     */
    const ERROR_INVALID_PARAMETERS = 10001;

    /**
     * Record already exists
     */
    const ERROR_ALREADY_EXISTS = 10002;

    /**
     * @param $message
     * @param int $code
     * @throws Exception
     */
    public function sendException($message, $code = 400)
    {
        throw new Exception($message, $code);
    }

    /**
     * @param Collection $model
     * @throws Exception
     */
    public function buildErrorsFromModel(Collection $model)
    {
        $errors = '';
        foreach ($model->getMessages() as $message) {
            $errors .= '[ ' . $message . ' ] ';
        }


        $this->sendException($errors);
    }

    /**
     * @param array $errorsArray
     * @throws Exception
     */
    public function buildErrorsFromArray(array $errorsArray)
    {
        $errors = '';
        foreach ($errorsArray as $message) {
            $errors .= '[ ' . $message . ' ]';
        }

        $this->sendException($errors);
    }

    /**
     * @param Group $messages
     * @throws Exception
     */
    public function buildErrorsFromValidator(Group $messages)
    {
        $errors = '';
        foreach ($messages as $message) {
            $errors .= '[ ' . $message . ' ] ';
        }

        $this->sendException($errors);
    }

    /**
     * @param object $raw
     * @param array $attributes
     * @throws Exception
     */
    protected function validateRaw($raw, $attributes)
    {
        $errors = '';

        foreach ($attributes as $attribute => $message) {
            if (!isset($raw->$attribute)) {
                $errors .= '[ ' . $message . ' ] ';
            }
        }

        if ($errors != '') {
            $this->sendException($errors);
        }
    }

    public function find($model, $config)
    {
        if (!$this->pageLimit) {
            $this->pageLimit = $this->config->get('pages')['limits'][$config];
        }
        $this->totalCount = $model::count([self::$query[0]]);

        self::$query['limit'] = $this->pageLimit;
        self::$query['skip'] = ($this->page - 1) * $this->pageLimit;

        return $model::find(self::$query);

    }

    /**
     * @OA\Schema(
     *     schema="PaginatedResponse",
     *     type="object",
     *              @OA\Property(
     *                   property="currentPage",
     *                   type="number"
     *               ),
     *               @OA\Property(
     *                   property="pageLimit",
     *                   type="number"
     *               ),
     *               @OA\Property(
     *                   property="totalPages",
     *                   type="number"
     *               ),
     *               @OA\Property(
     *                   property="totalCount",
     *                   type="number"
     *               ),
     *              @OA\Property(
     *                   property="data",
     *                   type="array",
     *                   @OA\Items()
     *               )
     * )
     *
     * @param $modelArray
     * @return array
     */
    public function pageResponse($modelArray)
    {
        $response = [
            'currentPage' => $this->page,
            'pageLimit' => $this->pageLimit,
            'totalPages' => ceil($this->totalCount / $this->pageLimit),
            'totalCount' => $this->totalCount,
            'data' => []
        ];

        $response['data'] = MainService::structuralMongoObjectToJson($modelArray);


        return $response;
    }

    public function sort($sort, $class)
    {
        $availableSortParams = [
            'DESC' => -1,
            'ASC' => 1
        ];

        $attributes = get_class_vars($class);
        $attributes = array_keys($attributes);


        $sortParams = $sort = explode(';', $sort);

        foreach ($sortParams as $param) {
            $sort = explode(':', $param);

            if (sizeof($sort) !== 2) {
                throw new \Exception("sort params must be {attribute}:{type}, example: name: ASC or DESC, you set {$param}");
            }

            if (!in_array($sort[0], $attributes)) {
                throw new \Exception("attribute {$sort[0]} does not exist");
            }

            if (!isset($availableSortParams[$sort[1]])) {
                throw new \Exception("sort type {$sort[1]} does not exist, available ASC and DESC");
            }

            self::$query['sort'][$sort[0]] = $availableSortParams[$sort[1]];
            self::$query['collation']['locale'] = 'uk';
        }



    }

    public function setPage(int $page)
    {
        $this->page = $page < 0 ? 1 : $page;
    }

    /**
     * @param $query
     * @param $class
     * @throws \Exception
     */
    public function setFilters($query, $class)
    {
        $attributes = get_class_vars($class);
        $attributes = array_keys($attributes);

        foreach ($query as $attr => $value) {
            if ($attr == "id") {
                $attr = "_id";
            }

            if($value == "true") {
                $value = true;
            } elseif ($value == "false") {
                $value = false;
            }
            if (in_array($attr, $attributes)) {
                if(preg_match('/"IN\[(.*?)\]"/', $value, $matches)) {

                    $v  = explode(",", $matches[1]);
                    if($attr == "_id") {
                        foreach ($v as $i => $val) {
                            if(!MongoService::validateMongoId($val)) {
                                $this->sendException('Invalid mongoId: ' . $val);
                            }
                            $v[$i] = MongoService::toMongoObjectId($val);
                        }
                    }
                    self::$query[0][$attr] = [
                        '$in' => $v,
                    ];

                } elseif(preg_match('/":(.*?):"/', $value, $matches)) {
                    self::$query[0][$attr] = [
                        '$regex' => "$matches[1]",
                        '$options' => 'i'
                    ];
                } else {
                    self::$query[0][$attr] = $value;
                }
            } elseif ($attr == 'sort') {
                $this->sort($value, $class);
            } elseif ($attr == 'page') {
                $this->setPage($value);
            } elseif ($attr == 'pageLimit') {
                $this->pageLimit = (int)$value;
            }
        }
    }

    /**
     * @param \stdClass $raw
     * @param $filters
     * @return \stdClass
     */
    public function filterRawByAttributes($raw, $filters)
    {


        foreach ($filters as $key) {

            if(is_object($raw) && property_exists($raw, $key)) {
                unset($raw->$key);
            }
        }

        return $raw;
    }
}