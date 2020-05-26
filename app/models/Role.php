<?php

namespace App\Models;


use Phalcon\Validation;
use Phalcon\Validation\Validator\Callback;
use Phalcon\Validation\Validator\ExclusionIn;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\InclusionIn;

/**
 * @OA\Schema()
 */

class Role extends \Phalcon\Mvc\MongoCollection
{

    /**
     * @var string
     *
     * @OA\Property(
     *   description="The key of role"
     * )
     */
    protected $key;

    /**
     * @var string
     * @OA\Property(
     *   description="The name of role"
     * )
     */
    protected $name;

    /**
     * @var boolean
     *
     * @OA\Property(
     *   description="role is active"
     * )
     */
    protected $active;


    /**
     * @var array
     *
     */

    protected $permissions;

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param string $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param array $permissions
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'active',
            new InclusionIn(
                [
                    'message' => 'Active must be bool',
                    'domain' => [
                        true,
                        false,
                    ]
                ]
            )
        );

        $validator->add(
            'key',
            new Callback(
                [
                    'message' => 'Role key already used',
                    'callback' => function($data) {
                        return !self::findFirst([
                            [
                                'key' => $data->key
                            ]
                        ]);
                    }
                ]
            )
        );

        $validator->add(
            'key',
            new Validation\Validator\PresenceOf(
                [
                    "message" => "The key is required",
                ]
            )
        );

        $validator->add(
            'name',
            new Validation\Validator\PresenceOf(
                [
                    "message" => "The name is required",
                ]
            )
        );

        $validator->add(
            'active',
            new Validation\Validator\PresenceOf(
                [
                    "message" => "The active is required",
                ]
            )
        );

        return $validator;

    }


}