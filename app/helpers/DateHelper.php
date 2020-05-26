<?php


namespace App\Helpers;


class DateHelper
{
    public static function toObject($date)
    {
        $date = explode('-', $date);


        $d = new \stdClass();
        $d->day = $date[2];
        $d->month = $date[1];
        $d->year = $date[0];

        return $d;
    }

}