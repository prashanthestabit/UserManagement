<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\UserManagement\Http\Requests\AuthenticateRequest;
use Modules\UserManagement\Http\Requests\UserCreateRequest;
use Modules\UserManagement\Http\Requests\UserRegisterRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        try{
            $page = $request->input('page');

            $users = User::paginate($request->input('per_page'));
            if(!$users){
                return response()->json([
                    'status'  =>  false,
                    'message' => 'Failed to fetch the list'
                ],Response::HTTP_BAD_REQUEST);
            }
            return response()->json([
                'status'  =>  true,
                'message' => __('UserManagement::messages.user.successfully_list'),
                'data'    => $users
            ],Response::HTTP_OK);
        }catch(JWTException $e){
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch the users list'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(UserCreateRequest $request)
    {
        try{
            $user = User::create([
                'name'     => $request->input('name'),
                'email'    => $request->input('email'),
                'password' => Hash::make($request->input('password'))
            ]);
            if($user){
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
        }catch(JWTException $e){
            return response()->json([
                'status' => false,
                'message' => 'Invalid Access'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show(Request $request,$id)
    {
        try{
            $user = User::find($id);
            if(!$user){
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to fetch the details'
                ],Response::HTTP_BAD_REQUEST);
            }
            return response()->json([
                'status'  => true,
                'message' => 'Details Fetched Successfully',
                'data'    => $user
            ],Response::HTTP_OK);
        }catch(JWTException $e){
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Access',
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param UserRegisterRequest $request
     * @param int $id
     * @return Response
     */
    public function update(UserCreateRequest $request)
    {
        try{

            $user = User::whereId($request->input('id'))->update([
                'name' => $request->input('name'),
                'email'=> $request->input('email')
            ]);
            if($user){
                return response()->json([
                    'status'  => true,
                    'message' => 'User Updated Successfully'
                ],Response::HTTP_OK);
            }else{
                return response()->json([
                    'status'  => false,
                    'message' => 'Sorry!! Try again'
                ],Response::HTTP_BAD_REQUEST);
            }
        }catch(JWTException $e){
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Access',
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request,$id)
    {
        try{
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json([
                'status'  => true,
                'message' => 'User Deleted Successfully'
            ],Response::HTTP_OK);
        }catch(JWTException $e){
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Access',
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
