<?php

namespace Modules\UserManagement\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\UserManagement\Http\Requests\AssignRoleRequest;
use Modules\UserManagement\Repositories\UserRepository;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiController extends Controller
{
    protected $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }
    /**
     * Fetch the user roles
     *
     * @param Request $request
     * @param int $id
     * @return response
     */
    public function userRole(Request $request, $id)
    {
        try {
            $userRole = $this->user->getUserRole($id);
            if (!$userRole) {
                $responseData = [
                    'status' => false,
                    'message' => __('UserManagement::messages.user.user_not_found'),
                ];

                return $this->user->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
            }
            $responseData = [
                'status' => true,
                'message' => __('UserManagement::messages.roles.success_user_roles_list'),
                'data' => $userRole,
            ];

            return $this->user->responseMessage($responseData, Response::HTTP_OK);
        } catch (JWTException $e) {
            $responseData = [
                'status' => false,
                'message' => __('UserManagement::messages.invalid_access'),
            ];

            return $this->user->responseMessage($responseData, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Assign roles to user
     *
     * @param AssignRoleRequest $request
     * @param int $id
     * @return response
     */
    public function assignRole(AssignRoleRequest $request, $id)
    {
        try {
            $data = [
                'user_id' => $id,
                'role_id' => $request->input('role_id'),
            ];
            $role = $this->user->assignRole($data);
            if (!$role) {
                $responseData = [
                    'status' => false,
                    'message' => __('UserManagement::messages.roles.failed_to_assign_role'),
                ];

                return $this->user->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
            }
            $responseData = [
                'status' => true,
                'message' => __('UserManagement::messages.roles.successfully_assigned'),
            ];

            return $this->user->responseMessage($responseData, Response::HTTP_OK);
        } catch (Exception $e) {
            $responseData = [
                'status' => false,
                'message' => __('UserManagement::messages.invalid_access'),
            ];

            return $this->user->responseMessage($responseData, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the roles assigned to user
     *
     * @param Request $request
     * @param integer $user_id
     * @param integer $role_id
     * @return void
     */
    public function removeUserRole(Request $request, $user_id, $role_id)
    {
        try {
            $check = $this->user->removeUserRole($user_id, $role_id);
            $responseData = [
                'status' => true,
                'message' => __('UserManagement::messages.roles.roles_removed'),
            ];

            return $this->user->responseMessage($responseData, Response::HTTP_OK);
        } catch (Exception $e) {
            $responseData = [
                'status' => false,
                'message' => __('UserManagement::messages.invalid_access'),
            ];

            return $this->user->responseMessage($responseData, Response::HTTP_OK);
        }
    }

}
