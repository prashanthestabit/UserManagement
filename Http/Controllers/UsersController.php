<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\UserManagement\Http\Requests\AuthenticateRequest;
use Modules\UserManagement\Http\Requests\UserCreateRequest;
use Modules\UserManagement\Http\Requests\UserRegisterRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Modules\UserManagement\Repositories\UserRepository;

class UsersController extends Controller
{

    protected $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        try{
            $users = $this->user->paginate($request);

            $responseData = [
                'status'  =>  true,
                'message' => __('UserManagement::messages.user.successfully_list'),
                'data'    => $users
            ];

            return $this->user->responseMessage($responseData, Response::HTTP_OK);

        }catch(Exception $e){
            $responseData = [
                'status' => false,
                'message' => __('UserManagement::messages.user.failed_list')
            ];

            return $this->user->responseMessage($responseData, Response::HTTP_INTERNAL_SERVER_ERROR);
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
            $data = [
                'name'     => $request->input('name'),
                'email'    => $request->input('email'),
                'password' => Hash::make($request->input('password'))
            ];

            $user = $this->user->save($data);

            if($user){
                $responseData = [
                    'status'  => true,
                    'message' => __('UserManagement::messages.user.successfully_registered'),
                    'data'    => $user
                ];

               return $this->user->responseMessage($responseData, Response::HTTP_OK);
            }else{
                $responseData = [
                    'status'  => false,
                    'message' => __('UserManagement::messages.user.failed_register'),
                    'data'    => array()
                ];

               return $this->user->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
            }
        }catch(Exception $e){
            $responseData = [
                'status' => false,
                'message' => __('UserManagement::messages.invalid_access'),
            ];

           return $this->user->responseMessage($responseData, Response::HTTP_INTERNAL_SERVER_ERROR);
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
            $user = $this->user->show(['id'=>$id]);
            if(!$user){
                $responseData = [
                    'status' => false,
                    'message' => __('UserManagement::messages.user.failed')
                ];

               return $this->user->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
            }
            $responseData = [
                'status'  => true,
                'message' => __('UserManagement::messages.user.successfully_fetched'),
                'data'    => $user
            ];

           return $this->user->responseMessage($responseData, Response::HTTP_OK);
        }catch(Exception $e){
            $responseData = [
                'status'  => false,
                'message' => __('UserManagement::messages.user.invalid_access'),
            ];

           return $this->user->responseMessage($responseData, Response::HTTP_INTERNAL_SERVER_ERROR);
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
            $user = $this->user->updateOrCreate(
                ['id'=>$request->input('id')],
                [
                    'name' => $request->input('name'),
                    'email'=> $request->input('email')
                ]
            );
            if($user){
                $responseData = [
                    'status'  => true,
                    'message' => __('UserManagement::messages.user.successfully_updated'),
                ];

               return $this->user->responseMessage($responseData, Response::HTTP_OK);
            }else{
                $responseData = [
                    'status'  => false,
                    'message' => __('UserManagement::messages.try_again'),
                ];

               return $this->user->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
            }
        }catch(Exception $e){
            $responseData = [
                'status'  => false,
                'message' => __('UserManagement::messages.user.invalid_access'),
            ];

           return $this->user->responseMessage($responseData, Response::HTTP_INTERNAL_SERVER_ERROR);
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
            $this->user->delete($id);

            $responseData = [
                'status'  => true,
                'message' => __('UserManagement::messages.user.successfully_deleted'),
            ];

           return $this->user->responseMessage($responseData, Response::HTTP_OK);

        }catch(Exception $e){

            $responseData = [
                'status'  => false,
                'message' => __('UserManagement::messages.user.invalid_access'),
            ];

           return $this->user->responseMessage($responseData, Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }
}
