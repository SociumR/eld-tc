<?php

namespace App\Components;


use App\Models\Role;
use App\Services\RolesService;
use Phalcon\Http\Request\Exception;
use Phalcon\Mvc\Micro;

class RBAC
{
    /**
     * @param Micro $app
     * @return bool
     * @throws Exception
     */
    public static function validate(&$app)
    {

        $userRole = $app->di->get('authorization')->getUser()->getRole();

        $superUserRole = $app->di->get('config')['application']['superUserRole'];

        if ($userRole == $superUserRole) {
            return true;
        }

        $routeName = $app->router->getMatchedRoute()->getName();

        /** @var Role $roles */
        $roles = RolesService::getRoleByKey($userRole);

        if (!$roles->getActive()) {
            return false;
        }

        $permissions = $roles->getPermissions();

        return in_array($routeName, $permissions);

    }
}