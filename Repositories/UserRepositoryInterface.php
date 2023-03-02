<?php

namespace Modules\UserManagement\Repositories;

interface UserRepositoryInterface
{
    public function paginate($request);

    public function save($data);

    public function updateOrCreate($where, $data);

    public function show($condition);

    public function delete($id);

    public function getUserRole($id);

    public function assignRole($id);

    public function removeUserRole($user_id, $role_id);

    public function responseMessage($responseData, $statusCode);
}
