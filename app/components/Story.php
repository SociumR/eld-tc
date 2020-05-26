<?php


namespace App\Components;


use Phalcon\Mvc\MongoCollection;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Callback;
use Phalcon\Validation\Validator\Date;
use Phalcon\Validation\Validator\PresenceOf;

/**
 * @OA\Schema()
 */


class Story extends MongoCollection
{
    const CATEGORY_CONTRACT = 'CONTRACT';
    const CATEGORY_PARTNER = 'PARTNER';
    const CATEGORY_DIRECTORY = 'DIRECTORY';
    const CATEGORY_SETTINGS = 'SETTINGS';

    /**
     * @var object
     *
     * @OA\Property(
     *   description="User",
     *   type="object",
     *   @OA\Schema(
     *      @OA\Property(
     *        property="id"
     *     )
     *   )
     * )
     */

    public $user;

    /**
     * @var string
     *
     * @OA\Property(
     *   description="date"
     * )
     */

    public $date;

    /**
     * @var string
     *
     * @OA\Property(
     *   description="action"
     * )
     */

    public $action;

    /**
     * @var string
     *
     * @OA\Property(
     *   description="description for operation"
     * )
     */

    public $description;

    /**
     * @var mixed
     *
     * @OA\Property(
     *   description="refreshed content"
     * )
     */

    public $content;

    /**
     * @var string
     *
     * @OA\Property(
     *  
     * )
     */

    public $category;


    public function validator()
    {
        $validator = new Validation();
        $validator->add(
            ['user.id', 'user.name', 'date', 'action', 'content.before', 'content.after'],
            new PresenceOf()
        );

        $validator->add(
            ['user.id'],
            new Callback([
                'message' => 'user not exist',
                'callback' => function($data) {

                }
            ])
        );

        $validator->add(
            ['date'],
            new Date([
                'format' => [
                    'date' => 'Y-m-d',
                ],
                'message' => [
                    'date' => 'date format must be d.m.Y'
                ]
            ])
        );

        return $validator->validate($this);
    }
}