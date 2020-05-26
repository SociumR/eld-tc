<?php

namespace App\Controllers;


use App\Components\Story;
use App\Models\Attributes\MainSettings;
use App\Models\Role;
use App\Services\AuthService;
use App\Services\MainService;
use App\Services\MongoService;
use App\Services\RolesService;
use Phalcon\Http\Request\Exception;

class AdminController extends AbstractController
{


    /**
     * @OA\Post(
     *     path="/admin/login",
     *     tags={"Admin"},
     *     summary="Login for admin users",
     *       @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *       mediaType="application/json",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="email",
     *                   description="email for authorization",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="password",
     *                   description="Password for authorization",
     *                   type="string"
     *               ),
     *           )
     *       )
     *   ),
     *     @OA\Response(
     *     response=200,
     *     description="User authorized",
     *     @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     * )
     */

    /**
     * @throws Exception
     */


    public function signIn()
    {
        return (new AuthService())->login($this->request->getJsonRawBody());
    }

    /**
     * @OA\Get(
     *     path="/admin/users",
     *     tags={"Admin"},
     *     operationId="getAdminUsers",
     *     summary="List of admin users",
     *     security={{"api_key":{}}},
     *     @OA\Response(
     *     description="Returned all admin users",
     *     response=200,
     *     @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                  ref="#/components/schemas/User"
     *              )
     *         ),
     *     ),
     * )
     */

    public function getUsers()
    {
        return AuthService::response(AuthService::getUsers());
    }

    /**
     * @OA\Get(
     *     path="/admin/profile",
     *     tags={"Admin"},
     *     summary="Information about current admin user",
     *     @OA\Response(
     *     description="Returned full information about user by headers",
     *     response=200,
     *     @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     *      security={{"api_key":{}}},
     * )
     */

    public function profile()
    {
        return (new AuthService())->getProfile();
    }

    /**
     * @OA\Post(
     *     path="/admin/users",
     *     tags={"Admin"},
     *     summary="Create user",
     *      security={{"api_key":{}}},
     *       @OA\RequestBody(
     *       required=true,
     *     @OA\MediaType(
     *       mediaType="application/json",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="email",
     *                   description="email for authorization",
     *                   type="string"
     *               ),
     *               @OA\Property(
     *                   property="password",
     *                   description="Password for authorization",
     *                   type="string"
     *               ),
     *     @OA\Property(
     *                   property="username",
     *                   description="username for user",
     *                   type="string"
     *               ),
     *     @OA\Property(
     *                   property="role",
     *                   description="role for user",
     *                   type="string"
     *               ),
     *     @OA\Property(
     *                   property="enabled",
     *                   description="is user enabled",
     *                   type="boolean"
     *               ),
     *           )
     *       )
     *   ),
     *     @OA\Response(
     *     response=200,
     *     description="User authorized",
     *     @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     *     security={{"api_key":{}}},
     * )
     */

    /**
     * @throws Exception
     */
    public function signUp()
    {
        return (new AuthService())->signUp($this->request->getJsonRawBody());
    }


    /**
     * @OA\Post(
     *     path="/admin/roles",
     *     tags={"Roles"},
     *     summary="Create role",
     *      security={{"api_key":{}}},
     *       @OA\RequestBody(
     *       required=true,
     *     @OA\JsonContent(ref="#/components/schemas/Role"),
     *   ),
     *     @OA\Response(
     *     response=200,
     *     description="Role created",
     *     @OA\JsonContent(ref="#/components/schemas/Role"),
     *     ),
     *     security={{"api_key":{}}},
     * )
     */

    /**
     * @return array
     * @throws Exception
     * @throws \Phalcon\Mvc\Collection\Exception
     */
    public function createRole()
    {
        $this->story($this->request->getJsonRawBody(), 'roles', 'Created role');

        return (new RolesService())->createRole($this->request->getJsonRawBody());
    }

    /**
     *
     * @OA\Get(
     *     path="/admin/roles",
     *     summary="Get all roles",
     *     tags={"Roles"},
     *     description="List of all roles",
     *     operationId="getRoles",
     *      security={{"api_key":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                  ref="#/components/schemas/Role"
     *              )
     *         ),
     *     ),
     *     @OA\Response(
     *     description="All errors in response boby",
     *         response="400",
     *     )
     * )
     *
     *  @OA\Get(
     *     path="/admin/roles/{id}",
     *     summary="Get role by id",
     *     tags={"Roles"},
     *     description="Information about role",
     *     operationId="getRole",
     *      security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of role",
     *         required=true,
     *         @OA\Schema(
     *           type="string"
     *         ),
     *         style="form"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/Role"),
     *     ),
     *     @OA\Response(
     *     description="All errors in response boby",
     *         response="400",
     *     )
     * )
     *
     *
     * @param null $id
     * @return array
     * @throws Exception
     */
    public function getRoles($id = null)
    {
        $roleService = new RolesService();

        if($id && !MongoService::validateMongoId($id)) {
            throw new Exception("Invalid id", 400);
        }

        $roles = $id ? $roleService::getRoleById($id) : $roleService::getAll();

        return RolesService::response($roles);
    }

    /**
     *
     * @OA\Get(
     *     path="/admin/permissions",
     *     summary="Get application permissions",
     *     tags={"Roles"},
     *     description="All permissions in application",
     *     operationId="getPermissions",
     *      security={{"api_key":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/RolePermissions"),
     *     ),
     *     @OA\Response(
     *     description="All errors in response boby",
     *         response="400",
     *     )
     * )
     *
     * @OA\Get(
     *     path="/admin/roles/{id}/permissions",
     *     summary="Get role permissions",
     *     tags={"Roles"},
     *     description="Role permissions",
     *     operationId="getRolePermissions",
     *      security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of role",
     *         required=true,
     *         @OA\Schema(
     *           type="string"
     *         ),
     *         style="form"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/RolePermissions"),
     *     ),
     *     @OA\Response(
     *     description="All errors in response boby",
     *         response="400",
     *     )
     * )
     * @param null $id
     * @return array
     * @throws Exception
     */
    public function getRolesPermissions($id = null)
    {
        $rS = new RolesService();

        if($id) {

            if (!MongoService::validateMongoId($id)) {
                throw new Exception("Invalid id", 400);
            }

            /** @var Role $role */
            $role = $rS->getRoleById($id);

            if (!$role) {
                throw new Exception("Not found", 404);
            }

            return $role->getPermissions();

        }

        return $rS->getPermissions();
    }

    /**
     *
     * @OA\Put(
     *     path="/admin/roles/{id}/permissions",
     *     summary="Set role permissions",
     *     tags={"Roles"},
     *     description="Update Role permissions",
     *     operationId="putRolesPermissions",
     *      security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of role",
     *         required=true,
     *         @OA\Schema(
     *           type="string"
     *         ),
     *         style="form"
     *     ),
     *       @OA\RequestBody(
     *       required=true,
     *      @OA\JsonContent(ref="#/components/schemas/RolePermissions"),
     *
     *   ),
     *     @OA\Response(
     *     response=200,
     *     description="Permissions saved",
     *     @OA\JsonContent(ref="#/components/schemas/RolePermissions"),
     *     ),
     * )
     *
     * @param null $id
     * @return array
     * @throws Exception
     * @throws \Phalcon\Mvc\Collection\Exception
     */
    public function putRolesPermissions($id)
    {
        $rS = new RolesService();

        if (!MongoService::validateMongoId($id)) {
            throw new Exception("Invalid id", 400);
        }

        /** @var Role $role */
        $role = $rS->getRoleById($id);

        if (!$role) {
            throw new Exception("Role not found", 404);
        }

        $superUserRole = $this->config->get('application')['superUserRole'];


        if ($superUserRole == $role->getKey()) {
            throw new Exception('role ' . $superUserRole . ' is not writable');
        }

        $rS->setPermissions($role, $this->request->getJsonRawBody());

        $this->story($this->request->getJsonRawBody(), 'roles', 'Updated role');

        return $role->getPermissions();
    }


    /**
     * @OA\Put(
     *     path="/admin/settings",
     *     summary="Set company settings",
     *     tags={"Admin"},
     *     description="Update company settings",
     *     operationId="updateSettings",
     *      security={{"api_key":{}}},
     *       @OA\RequestBody(
     *       required=true,
     *      @OA\JsonContent(ref="#/components/schemas/MainSettings"),
     *
     *   ),
     *     @OA\Response(
     *     response=200,
     *     description="Settings saved",
     *     @OA\JsonContent(ref="#/components/schemas/MainSettings"),
     *     ),
     * )
     * @return array
     * @throws Exception
     * @throws \Phalcon\Mvc\Collection\Exception
     */
    public function postSettings()
    {
        $service = new MainService();

        $this->story($this->request->getJsonRawBody(), Story::CATEGORY_SETTINGS, 'Updated settings');
        return $service->saveSettings($this->request->getJsonRawBody());
    }

    /**
     * @OA\Get(
     *     path="/admin/settings",
     *     summary="Get company settings",
     *     tags={"Admin"},
     *     description="Get company settings",
     *     operationId="getSettings",
     *      security={{"api_key":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/MainSettings"),
     *     ),
     *     @OA\Response(
     *     description="All errors in response boby",
     *         response="400",
     *     )
     * )
     *
     * @return array
     * @throws Exception
     */
    public function getSettings()
    {
        return MainService::settings();
    }


}