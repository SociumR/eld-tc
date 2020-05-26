<?php


namespace App\Models;


use Phalcon\Mvc\MongoCollection;
use Phalcon\Validation;


/**
 * @OA\Schema()
 *
 * Class Song
 * @package App\Models
 */
class Song extends AbstractModel
{
    /**
     *
     * @var string
     *
     * @OA\Property(
     *   type="string",
     *   description="The title of song"
     * )
     */

    public $title;

    /**
     *
     * @var string
     *
     * @OA\Property(
     *   type="string",
     *   description="The author of song"
     * )
     */

    public $author;


    public function getSource()
    {
        return 'song';
    }
    

    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            ['title', 'author'],
            new Validation\Validator\PresenceOf()
        );

        return $validator->validate($this);
    }
}