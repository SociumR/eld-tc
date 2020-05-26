<?php

namespace App\Components;

use App\Models\User;
use Phalcon\Http\Request;

/**
 * Class AuthorizationComponent
 *
 * @property \Phalcon\Http\Request $request
 */
class Authorization
{
    private $authToken;

    /** @var \App\Models\User $user */
    private $user;

    /** @var integer $cacheTime */
    private $cacheTime;

    public function __construct($cacheTime = 10)
    {
        $this->setAuthToken();
        $this->cacheTime = $cacheTime;
    }

    public function setAuthToken()
    {
        $this->authToken = (new Request())->getHeader('X-API-Key');
    }

    public function getAuthToken()
    {
        return $this->authToken;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return (string) $this->user->getId();
    }

    public function isAuthorized()
    {
        if (!$this->getAuthToken() || !$this->getUserByAuthToken()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function getUserByAuthToken()
    {
        if (!$this->getAuthToken()) {
            return false;
        }

        $this->user = User::findFirst([[
            'authKey' => $this->getAuthToken(),
        ]
        ]);

        return !empty($this->user) && $this->user->getEnabled();
    }

}