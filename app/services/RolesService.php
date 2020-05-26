<?php

namespace App\Services;


use App\Models\Role;
use Phalcon\Mvc\Collection\Exception;

class RolesService extends ApiService
{

    public function getRoles()
    {
        return self::response(self::getAll());
    }

    public static function getAll()
    {
        return Role::find();
    }

    public static function response($roles)
    {
        $response = [];

        if(is_array($roles)) {
            foreach ($roles as $r) {
                $response[] = self::toResponse($r);
            }
        } else {
            $response = self::toResponse($roles);
        }

        return $response;
    }

    /**
     * @param Role $role
     * @return array
     */
    public static function toResponse($role)
    {
        return $role ? [
            'id' => (string) $role->getId(),
            'name' => $role->getName(),
            'key' => $role->getKey(),
            'active' => $role->getActive()
        ] : [];
    }

    public static function getRoleById($id)
    {
        return Role::findById($id);
    }

    public static function getRoleByKey($key)
    {
        return Role::findFirst([
            [
                'key' => $key
            ]
        ]);
    }



    public static function isRoleExist($key)
    {
        $r = self::getRoleByKey($key);

        return $r ? true : false;
    }
    /**
     * @param $raw
     * @return array
     * @throws \Phalcon\Http\Request\Exception
     * @throws Exception
     */
    public function createRole($raw)
    {
        $attributes = [
            'key' => 'Role key is required',
            'name' => 'Role name is required',
        ];


        $this->validateRaw($raw, $attributes);

        $role = new Role();

        $errors = $role->validation()->validate($raw);

        if (count($errors) > 0) {
            $this->buildErrorsFromValidator($errors);
        }
        $role->setKey($raw->key);
        $role->setName($raw->name);
        $role->setActive($raw->active);
        $role->setPermissions([]);

        $save = $role->save();

        if(!$save) {
            $this->buildErrorsFromModel($role);
        }

        return $this->response($role);
    }

    /**
     *  @OA\Schema(
     *     schema="RolePermissions",
     *     type="array",
     *     description="Role permissions",
     *     @OA\Items(
     *         type="string"
     *     ),
     * )
     * Class CopyrightRights
     */

    public function getPermissions($role = null)
    {
        $permissions = [];
        $actions = $this->router->getRoutes();

        foreach ($actions as $action) {
            $permissionName = $action->getName();
            if(!is_null($permissionName)) {
                $permissions[] = $permissionName;
            }
        }
        return $permissions;
    }

    /**
     * @param Role $role
     * @param array $permissions
     * @return bool
     * @throws \Phalcon\Http\Request\Exception
     * @throws Exception
     */
    public function setPermissions(&$role, $permissions)
    {
        if(is_null($permissions) || gettype($permissions) != 'array') {
            $this->sendException('permissions must be array');
        }

        if(count(array_intersect($this->getPermissions(), $permissions)) != count($permissions)) {
            $this->sendException('Invalid permissions');
        }

        $role->setPermissions($permissions);
        return $role->save();
    }

}