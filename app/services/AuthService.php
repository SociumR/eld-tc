<?php

namespace App\Services;


use App\Models\User;

class AuthService extends ApiService
{

    /**
     * @param object $raw
     * @return array
     * @throws \Phalcon\Http\Request\Exception
     */
    public function login($raw)
    {
        if (!$raw->email || !$raw->password) {
            $this->sendException('Email and password is required');
        }

        $email = $raw->email;
        $password = $raw->password;

        /** @var User $user */
        $user = User::findFirst(
            [
                [
                    'email' => $email,
                ]
            ]
        );

        if (!$user) {
            $this->sendException('User not found');
        }

        $passwordValid = $this->validatePassword($password, $user->getPasswordHash());

        if (!$passwordValid) {
            $this->sendException('Invalid email or password');
        }

        return $this->getProfile($user);
    }

    /**
     * @param $raw
     * @return array
     * @throws \Phalcon\Http\Request\Exception
     * @throws \Exception
     */
    public function signUp($raw)
    {

        $user = new User();

        $validateErrors = $user->validation()->validate($raw);

        if (count($validateErrors) > 0) {
            $this->buildErrorsFromValidator($validateErrors);
        }

        if (AuthService::getUserByUserEmail($raw->email)) {
            return $this->sendException('Email already used');
        }

        if (AuthService::getUserByUserName($raw->username)) {
            return $this->sendException('Username already used');
        }

        $user->setUsername($raw->username);
        $user->setEmail($raw->email);
        $user->setPasswordHash($this->generatePasswordHash($raw->password));
        $user->setAuthKey($this->generateAuthKey($raw->username, $user->getPasswordHash()));
        $user->setCreatedAt((new \DateTime())->getTimestamp());
        $user->setUpdatedAt((new \DateTime())->getTimestamp());
        $user->setRole($raw->role);
        $user->setEnabled($raw->enabled ? $raw->enabled : true);

        if (!$user->save()) {
            $this->buildErrorsFromModel($user);
        }

        return $this->getProfile($user);

    }

    /**
     * @param null | User $user
     * @return array
     */
    public function getProfile($user = null)
    {
        /** @var User $auth */
        $auth = $user ? $user : $this->di->get('authorization')->getUser();


        return self::userObjToArray($auth);
    }

    /**
     * @param $users
     * @return array
     */
    public static function response($users)
    {
        $response = [];
        if(sizeof($users) > 0) {
            foreach ($users as $user) {
                $response[] = self::userObjToArray($user);
            }
        } else {
            $response = self::userObjToArray($users);
        }

        return $response;
    }

    /**
     * @param User $user
     * @return array
     */
    public static function userObjToArray($user)
    {
        return [
            'id' => (string) $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'authKey' => $user->getAuthKey(),
            'role' => $user->getRole(),
            'enabled' => $user->getEnabled()
        ];

    }

    private function generatePasswordHash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    private function validatePassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public static function getUserByUserName($username)
    {
        $user = User::findFirst([
            [
                'username' => $username,
            ],
        ]);

        return $user;
    }

    public static function getUserByUserEmail($email)
    {
        $user = User::findFirst([
            [
                'email' => $email,
            ],
        ]);

        return $user;
    }

    public function generateAuthKey($userName, $password)
    {
        return md5($userName . $password);
    }

    public static function getUsers()
    {
        return User::find();
    }

}