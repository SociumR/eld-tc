<?php

namespace App\Models;


use Phalcon\Mvc\MongoCollection;

abstract class AbstractModel extends MongoCollection
{
    /**
     * @param object $raw
     * @return bool
     */
    public function load($raw)
    {
        $this->beforeLoad($raw);


        $a = get_object_vars($raw);

        foreach ($a as $k => $c) {
            if(property_exists($this, $k)) {
                $this->$k = $c;
            }
        }

        $this->afterLoad($raw);

        return true;
    }

    public function beforeLoad($raw)
    {

    }

    public function afterLoad($raw)
    {

    }

}