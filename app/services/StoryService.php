<?php


namespace App\Services;


use App\Components\Story;
use Exception;

class StoryService extends ApiService
{

    /**
     * @param $query
     * @return array
     * @throws Exception
     */
    public static function filter($query)
    {

        if (!$query) {
           throw new Exception('Filtered params is required');
        }

        $pageLimit = 20;
        $page = (integer) $query->page ?: 1;


        $find = self::findBuilder($query);

         $model = Story::find([
             $find,
             'sort' => ['date' => -1],
             'limit' => $pageLimit,
             'position' => $page * $pageLimit
        ]);

        return $model;

    }

    private static function findBuilder($object, $param = null)
    {
        $a = [];

        foreach ($object as $key => $item) {
            if(is_object($item)) {
                $a = array_merge(self::findBuilder($item, $param ? $param . '.' . $key : $key), $a);
            } elseif (gettype($item) != 'array') {
                $between = preg_match('/^between(.*),(.*)$/', $item, $betweenMatches);
                $in = preg_match('/^in\[(.*)\]$/', $item, $inMatches);
                if($between) {
                    $a[$param ? $param . '.' . $key : $key] = ['$gte' => $betweenMatches[1], '$lte' => $betweenMatches[2]];
                } elseif ($in) {
                   $a[$param ? $param . '.' . $key : $key] = ['$in' => explode(',', str_replace('\'', '', $inMatches[1]))];
                }else {
                    $a[$param ? $param . '.' . $key : $key] = $item;
                }
            }
        }

        return $a;
    }
}