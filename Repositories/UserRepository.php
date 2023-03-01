<?php

namespace Modules\UserManagement\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Response;
use Modules\UserManagement\Entities\UserRole;

/* Class UserRepository.
 * This class is responsible for handling database operations related to user with JWT.
 */
class UserRepository
{
    /**
     * Paginate all user data
     */
    public function paginate($request)
    {
        $page = $request->input('page');

        return User::paginate($request->input('per_page'));
    }
    /**
     * Register a new user with the given data.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function register($data)
    {
        $user = User::create($data);
        return $user;
    }

    /**
    * Update or create Registered with the given data.
    *
    * @param array $data
    * @param array $where
    * @return \App\Models\User
    */
   public function updateOrCreate($where,$data)
   {
       $user = User::updateOrCreate($where,$data);
       return $user;
   }


    /**
    * Show user details given by condition.
    *
    * @param array $condition
    * @return \App\Models\User
    */
    public function show($condition)
    {
      return User::where($condition)->first();
    }

    /**
    * delete user given by id.
    *
    * @param integer $id
    * @return \App\Models\User
    */
    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return true;
    }

    /**
    * user Role given by user id.
    *
    * @param integer $id
    * @return \App\Models\UserRole
    */
    public function getUserRole($id)
    {
      return UserRole::with('Roles','userData')->where('user_id',$id)->get();;
    }

     /**
    * Assign role to user by user id.
    *
    * @param integer $id
    * @return \App\Models\UserRole
    */
    public function assignRole($id)
    {
      return UserRole::with('Roles','userData')->where('user_id',$id)->get();;
    }

    /**
    * Remove role from user by role id.
    *
    * @param integer $id
    * @return \App\Models\UserRole
    */
    public function removeUserRole($user_id,$role_id)
    {
        $check = UserRole::where(['user_id'=>$user_id,'role_id'=>$role_id])->first();
        if(!$check){
            return $this->responseMessage([
                'status'  => false,
                'message' => __('UserManagement::messages.roles.roles_not_assigned'),
            ],Response::HTTP_NO_CONTENT);

        }
        $check->delete();
        return true;
    }



    /**
     * Generate a response with the given status, message, data and status code.
     *
     * @param array $responseData
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseMessage($responseData, $statusCode)
    {
        return response()->json($responseData, $statusCode);
    }
}
