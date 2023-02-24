<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Modules\UserManagement\Emails\ForgetPasswordMail;
use Modules\UserManagement\Entities\UserRole;
use Modules\UserManagement\Http\Requests\AssignRoleRequest;
use Modules\UserManagement\Http\Requests\AuthLoginRequest;
use Modules\UserManagement\Http\Requests\ChangePasswordRequest;
use Modules\UserManagement\Http\Requests\ForgetPasswordRequest;
use Modules\UserManagement\Http\Requests\UserRegisterRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiController extends Controller
{
    /**
     *
     *  Store a newly registered user information.
     * @param UserRegisterRequest $request
     * @return response
     *
     */
    public function register(UserRegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password'))
        ]);
        if($user){
            $user['token'] = JWTAuth::attempt($request->only(['email','password']));
            return response()->json([
                'status'  => true,
                'message' => 'User Registered Successfully',
                'data'    => $user
            ],Response::HTTP_OK);
        }else{
            return response()->json([
                'status'  => false,
                'message' => 'Failed To Register User',
                'data'    => array()
            ],Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     *
     * Verify the user credentials
     * @param AuthLoginRequest $request
     * @return response
     *
     */
    public function authenticate(AuthLoginRequest $request)
    {
        try{
            if(!$token = JWTAuth::attempt($request->only(['email','password']))){
                return response()->json([
                    'status'  => false,
                    'message' => 'Login credentials are invalid'
                ],Response::HTTP_BAD_REQUEST);
            }
        }catch(JWTException $e){
            return response()->json([
                'status' => false,
                'message' => 'Could not create token'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'status' => 'true',
            'message' => 'Login Successfully',
            'token' => $token
        ],Response::HTTP_OK);
    }

    /**
     *
     * Update the password temporary & send the email to user
     * @param ForgetPasswordRequest $request
     * @return response
     *
     */
    public function forgotPassword(ForgetPasswordRequest $request)
    {

        try{
            $user = User::where('email',$request->input('email'))->first();
            if(!$user){
                return response()->json([
                    'status' => false ,
                    'message' => 'User Not Found'
                ],Response::HTTP_NOT_FOUND);
            }
            $pass = Str::random(6);
            $user->password = Hash::make($pass);
            $user->password_status = 1;
            $user->update();
            $details = [
                'password' =>$pass
            ];
            Mail::to($request->input('email'))->send(new ForgetPasswordMail($details));
            if(Mail::failures()){
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to send the mail'
                ],Response::HTTP_UNAUTHORIZED);
            }else{
                return response()->json([
                    'status' => true,
                    'message' => 'Mail Sent Successfully'
                ],Response::HTTP_OK);
            }
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Failed'
            ],Response::HTTP_BAD_REQUEST);

        }
    }

    /**
     *
     * Verify the token and make User Logged out
     * @param Request $request
     * @return response
     *
     */
    public function logout(Request $request)
    {
        try{
            JWTAuth::invalidate($request->input('token'));
            return response()->json([
                'status' => true,
                'message' => 'User has been logged out'
            ],Response::HTTP_OK);
        }catch(JWTException $e){
            return response()->json([
                'status' => false,
                'message' => 'Sorry user cannot be logged out',
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     *
     *  Verify the token and fetched the user information
     * @param Request $request
     * @return response
     *
     */
    public function getUser(Request $request)
    {
        try{
            $user = JWTAuth::authenticate($request->input('token'));
            return response()->json([
                'status' => true,
                'message' => 'Details Fetch Successfully',
                'data' => $user
            ],Response::HTTP_OK);
        }catch(JWTException $e){
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch the user details'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified information of user.
     * @param UserRegisterRequest $request
     * @param int $id
     * @return Response
     */
    public function updateUser(UserRegisterRequest $request)
    {
        try{
            $authenticate = JWTAuth::authenticate($request->input('token'));
            $userUpdate = User::find($authenticate->id)->update([
                'name' => $request->input('name'),
                'email'=> $request->input('email')
            ]);
            $user = User::find($authenticate->id);
            return response()->json([
                'status' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ], Response::HTTP_OK);
        }catch(JWTException $e){
            return response()->json([
                'status' => false,
                'message' => 'Failed to update the user details'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     /**
     *
     * Change the user's password
     * @param ChangePasswordRequest $request
     * @return Response
     *
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        try{
            $user = JWTAuth::authenticate($request->input('token'));
            $old_password = Hash::make($request->input('old_password'));
            $userUpdate = User::where('id',$user->id)->where('password',$old_password)->first();
            if(!$userUpdate){
                return response()->json([
                    'status' => false,
                    'message' => 'User Not Found',
                ], Response::HTTP_NOT_FOUND);
            }
            $userUpdate->password = Hash::make($request->input('password'));
            $userUpdate->update();
            return response()->json([
                'status' => true,
                'message' => 'Password Updated Successfully',
                'data' => $user
            ], Response::HTTP_OK);
        }catch(JWTException $e){
            return response()->json([
                'status' => false,
                'message' => 'Failed to update the password',
                'data' => array()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Fetch the user roles
     *
     * @param Request $request
     * @param int $id
     * @return response
     */
    public function userRole(Request $request,$id)
    {
        try{
            $userRole = User::with('userRoles.Roles')->where('id',$id)->first();
            if(!$userRole){
                return response()->json([
                    'status'  => false,
                    'message' => 'User Not Found',
                ],Response::HTTP_BAD_REQUEST);
            }
            return response()->json([
                'status'  => true,
                'message' => 'User Role Fetch Successfully',
                'data'    => $userRole
            ],Response::HTTP_OK);
        }catch(JWTException $e){
            return response()->json([
                'status' => false,
                'message' => 'Invalid Access',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Assign roles to user
     *
     * @param AssignRoleRequest $request
     * @param int $id
     * @return response
     */
    public function assignRole(AssignRoleRequest $request,$id)
    {
        try{
            $role = UserRole::create([
                'user_id' => $id,
                'role_id' => $request->input('role_id')
            ]);
            if(!$role){
                return response()->json([
                    'status'  => false,
                    'message' => 'Failed To Assign Role To User',
                ],Response::HTTP_BAD_REQUEST);
            }
            return response()->json([
                'status'  => true,
                'message' => 'Role Assigned Successfully',
            ],Response::HTTP_OK);
        }catch(JWTException $e){
            return response()->json([
                'status' => false,
                'message' => 'Invalid Access',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
    public function removeUserRole(Request $request,$user_id,$role_id)
    {
        try{
            $check = UserRole::where('user_id',$user_id)->where('role_id',$role_id)->first();
            if(!$check){
                return response()->json([
                    'status'  => false,
                    'message' => 'Role Not Assigned To User',
                ],Response::HTTP_NO_CONTENT);
            }
            $check->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Role Removed Successfully',
            ],Response::HTTP_OK);
        }catch(JWTException $e){
            return response()->json([
                'status' => false,
                'message' => 'Invalid Access',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
