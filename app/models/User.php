<?php

namespace App\Models;


use App\Services\RolesService;
use Phalcon\Mvc\MongoCollection;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;
use Phalcon\Validation\Validator\Uniqueness;

/**
 * @OA\Schema()
 */

class User extends MongoCollection
{

    /**
     *
     * @var string
     *
     * @OA\Property(
     *   property="username",
     *   type="string",
     *   description="The name of user"
     * )
     */
    protected $username;

    /**
     *
     * @var string
     *
     * @OA\Property(
     *   property="authKey",
     *   type="string",
     *   description="The x-api-key for admin authorization"
     * )
     */
    protected $authKey;

    /**
     *
     * @var string
     */
    protected $passwordHash;

    /**
     *
     * @var string
     *
     * @OA\Property(
     *   property="email",
     *   type="string",
     *   description="Email of admin"
     * )
     */
    protected $email;

    /**
     *
     * @var boolean
     *
     * @OA\Property(
     *   property="enabled",
     *   type="boolean",
     *   description="Is user active"
     * )
     */
    protected $enabled;

    /**
     *
     * @var integer
     */
    protected $createdAt;

    /**
     *
     * @var integer
     */
    protected $updatedAt;

    /**
     *
     * @var string
     *
     * @OA\Property(
     *   property="role",
     *   type="string",
     *   description="Role of user"
     * )
     */
    protected $role;



    /**
     * Method to set the value of field username
     *
     * @param string $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Method to set the value of field authKey
     *
     * @param string $authKey
     * @return $this
     */
    public function setAuthKey($authKey)
    {
        $this->authKey = $authKey;

        return $this;
    }

    /**
     * Method to set the value of field passwordHash
     *
     * @param string $passwordHash
     * @return $this
     */
    public function setPasswordHash($passwordHash)
    {
        $this->passwordHash = $passwordHash;

        return $this;
    }

    /**
     * Method to set the value of field email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Method to set the value of field enabled
     *
     * @param integer $enabled
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Method to set the value of field createdAt
     *
     * @param integer $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Method to set the value of field updatedAt
     *
     * @param integer $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Method to set the value of field role
     *
     * @param string $role
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Returns the value of field username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns the value of field authKey
     *
     * @return string
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * Returns the value of field passwordHash
     *
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }


    /**
     * Returns the value of field email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Returns the value of field enabled
     *
     * @return integer
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Returns the value of field createdAt
     *
     * @return integer
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Returns the value of field updatedAt
     *
     * @return integer
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Returns the value of field role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Validations and business logic
     *
     * @return Validation
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            ['role', 'email', 'username'],
            new Validation\Validator\PresenceOf(
                [
                    'message' => [
                        'role' => 'role is required',
                        'email' => 'email is required',
                        'username' => 'username is required',
                    ],
                ]
            )
        );

        $validator->add(
            'email',
            new EmailValidator(
                [
                    'message' => 'Please enter a correct email address',
                ]
            )
        );

        $validator->add(
            'username',
            new Uniqueness(
                [
                    'model' => $this,
                    'message' => 'Username ' . $this->username . ' is already taken'
                ]
            )
        );

        $validator->add(
            'role',
            new Validation\Validator\Callback(
                [
                    'message' => 'Role is not exist',
                    'callback' => function($data) {
                        return RolesService::getRoleByKey($data->role) ? true : false;
                    }
                ]
            )
        );

        return $validator;
    }


    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'user';
    }


}