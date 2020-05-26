<?php

namespace App\Helpers;

use Exception;

class ArrayHelper
{
    /**
     * @param $array
     * @return object
     * @throws Exception
     */
    public static function toObject($array) {

        $obj = new \stdClass();
        $type = gettype($array);

        if ($type !== 'array' && $type !== 'object') {
            return $array;
            throw new Exception('Must be array, got ' . $type . $array);
        }

        foreach ($array as $key => $item) {
            if(gettype($item) == 'array') {

                if(in_array('0', array_keys($item))) {

                    foreach ($item as $it => $a) {
                        $item[$it] = ArrayHelper::toObject($a);
                    }
                    $obj->$key = $item;
                } elseif ((count($item) == 0 || count($item) == 1) && gettype($item[0] == 'array')) {

       /*             if(in_array(0, array_keys($item))) {
                        foreach ($item as $k => $u) {

                            echo "<pre>";
                            print_r($u);
                            die();
                            $item[$k] = self::toObject($u);
                        }

                        echo "<pre>";
                        print_r($item);
                        die();
                    }*/

                    switch (count($item)) {
                        case 0:
                            $obj->$key = [];
                            break;
                        case 1:
                            $obj->$key[] = self::toObject($item[0]);
                            break;
                        default:
                            echo 1;
                            die();
                    }
                } else {
                    $obj->$key = self::toObject($item);
                }
            } else if(gettype($key) == 'integer') {
                $obj->$item = '';
            } elseif(gettype($item) == 'object') {
                $obj->$key = self::toObject($item);
            } else {
                $obj->$key = $item;
            }
        }

        return $obj;
    }

    public static function objectDiff($before, $after, $savedKey = false)
    {
        $diff = new \stdClass();
        $typesV = ['string', 'integer', 'float', 'boolean', 'double'];


        $afterValue = null;

        foreach ($before as $k => $value) {
            if($k === '_id') {
                continue;
            }

            if($savedKey === $k) {
                $diff->$k = $value;
                continue;
            }

            if(is_array($after)) {
                $afterValue = $after[$k];

            } else {
                $afterValue = $after->$k;
            }

            if(key_exists($k, $after) || property_exists($k, $afterValue)) {

                if(is_object($value)) {
                    $d = self::objectDiff($value, $afterValue, $savedKey);
                    if ($d) {
                        $diff->$k = $d;
                    }
                } elseif (is_array($value)) {
                    if(sizeof($value) != sizeof($afterValue)) {
                        $diff->$k = $afterValue;
                        continue;
                    }
                    $d = self::objectDiff($value, $afterValue, $savedKey);

                    if ($d) {
                        $diff->$k = $d;
                    }
                } elseif (in_array(gettype($value), $typesV)) {
                    if ($value != $afterValue) {
                        $diff->$k = $afterValue;
                    }
                }
            }
        }

        return $diff == new \stdClass() ? false : $diff;
    }

    public static function arrayToString($array, $withKeys = true)
    {
        $string = "";

        foreach ($array as $key => $item) {
            if($withKeys) {
                $string .= '|' . $key . ':';
            }
            $string .= (is_array($item) || is_object($item)) ? self::arrayToString($item, $withKeys) : $item;
        }

        return $string;
    }

    public static function arrayToArrayString($array, $withKeys = true)
    {
        $string = "";

        foreach ($array as $key => $item) {
            $string .= "[ ";
            if($withKeys) {
                $string .= $key . ':';
            }
            $string .= (is_array($item) || is_object($item)) ? self::arrayToString($item, $withKeys) : $item;
            $string .= " ]";
        }

        return $string;
    }
}