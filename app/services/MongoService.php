<?php

namespace App\Services;


use MongoDB\BSON\ObjectId;

class MongoService
{
    /**
     *@param string
     *@return boolean
     */
    public static function validateMongoId($id)
    {
        if(strlen($id) == "24" && ctype_xdigit($id)) {
            return true;
        }

        return false;
    }

    public static function toMongoObjectId($id)
    {
        return new ObjectId($id);
    }

}