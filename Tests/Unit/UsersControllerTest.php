<?php

namespace Modules\UserManagement\Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\UserManagement\Entities\Roles;
use Modules\UserManagement\Entities\UserRole;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersControllerTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    protected $listDataStructure = [
            'data',
            'current_page',
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ];

    /**
     * 1. Test fetch users list API endpoint with valid token.
     *
     * @return void
     */
    public function testFetchUsersListApiWithValidToken()
    {
        // Create a user and generate a valid JWT token
        $token = $this->getUserToken();

        // Make a GET request to the user list endpoint with the token
        $response = $this->get(route('users.list', [
            'token' => $token,
        ]));

        // Assert that the response has a HTTP status code of 200 (OK)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => $this->listDataStructure,
        ]);
        $response->assertJson([
            'status' => true,
            'message' => __('UserManagement::messages.user.successfully_list')
        ]);
    }

    /**
     * 2. Test fetch users list API endpoint with Invalid token.
     *
     * @return void
     */
    public function testFetchUserListWithInvalidToken()
    {
        // Make a GET request to the user list endpoint with an invalid token
        $response = $this->get(route('users.list', ['token' => 'invalid-token']));

        // Assert that the response has a HTTP status code of 500 (Internal Server Error)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
        ]);
        $response->assertJson([
            'status' => __('UserManagement::messages.invalid_token'),
        ]);
    }

    /**
     * 3. Test save users details API endpoint with valid token.
     *
     * @return void
     */
    public function testSaveUserDetailsApiWithValidToken()
    {
        // Create a user and generate a valid JWT token
        $token = $this->getUserToken();

        $name = $this->faker->name;
        $password = $this->faker->password;
        $email = $this->faker->unique()->safeEmail;

        // Make a Post request to the user store endpoint with the data
        $response = $this->post(route('users.store', [
            'token' => $token,
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]));

        // Assert that the response has a HTTP status code of 200 (OK)
        $response->assertStatus(Response::HTTP_OK);

        //Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'name',
                'email',
            ],
        ]);
        $response->assertJson([
            'status' => true,
            'message' => __('UserManagement::messages.user.successfully_registered'),
            'data' => [
                'name' => $name,
                'email' => $email,
            ],
        ]);

        //Check log table data in database
        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
        ]);
    }

    /**
     * 4. Test can not store with invalid data with valid token.
     *
     * @return void
     */
    public function testCanNotSaveUserDetailsWithInvalidData()
    {
        // Create a user and generate a valid JWT token
        $token = $this->getUserToken();

        $password = $this->faker->password;

        // Make a Post request to the user store endpoint with the Invalid data
        $response = $this->post(route('users.store', [
            'token' => $token,
            'name' => '',
            'email' => 'invalid-email',
            'password' => $password,
            'password_confirmation' => $password,
        ]));

        // Assert that the response has a HTTP status code of 200 (OK)
        $response->assertStatus(Response::HTTP_OK);

        //Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
            'message',
        ]);
        $response->assertJson([
            'status' => false,
        ]);
    }

    /**
     * 5. Test save user details with InValid token.
     *
     * @return void
     */
    public function testSaveUserDetailsWithInvalidToken()
    {
        // Make a GET request to the user list endpoint with an invalid token
        $response = $this->post(route('users.store',
            [
                'token' => 'invalid-token',
                'name' => $this->faker->name,
                'email' => $this->faker->unique()->safeEmail(),
                'password' => 'secret',
                'password_confirmation' => 'secret',
            ]));

        // Assert that the response has a HTTP status code of 500 (Internal Server Error)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
        ]);
        $response->assertJson([
            'status' => __('UserManagement::messages.invalid_token'),
        ]);
    }

    /**
     * 6. Test show user with valid token and id.
     *
     * @return void
     */
    public function testShowUserWithValidTokenAndId()
    {
        // Create a user and generate a valid JWT token
        $token = $this->getUserToken();

        $user = User::factory()->create();

        $response = $this->get(route("users.show", ['id' => $user->id, 'token' => $token]));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'status' => true,
                'message' => __('UserManagement::messages.user.successfully_fetched'),
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);

    }

    /**
     * 7. Test show user with invalid token
     *
     * @return void
     */
    public function testShowUserWithInvalidToken()
    {
        $user = User::factory()->create();

        $response = $this->get(route("users.show", ['id' => $user->id, 'token' => 'invalid-token']));

        // Assert that the response has a HTTP status code of 500 (Internal Server Error)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected JSON structure and data
        $response->assertJsonStructure([
            'status',
        ]);
        $response->assertJson([
            'status' => __('UserManagement::messages.invalid_token'),
        ]);
    }

    /**
     * 8. Test show user with non-existent id
     *
     * @return void
     */
    public function testShowUserWithNonExistentId()
    {
       // Create a user and generate a valid JWT token
       $token = $this->getUserToken();

       $response = $this->get(route("users.show", ['id' => 999, 'token' => $token]));
        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'status' => false,
                'message' => __('UserManagement::messages.user.failed'),
            ]);
    }

    /**
     *9. Test update user with valid token and id
     *
     * @return void
     */
    public function testUpdateUserWithValidTokenAndId()
    {

       // Create a user and generate a valid JWT token
        $token = $this->getUserToken();

        $user = User::factory()->create();

        $name =  $this->faker->name;
        $email =  $this->faker->unique()->safeEmail();

        $response = $this->put(route('users.update'), [
            'token' => $token,
            'id' => $user->id,
            'name' => $name,
            'email' => $email,
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'status' => true,
                'message' => __('UserManagement::messages.user.successfully_updated'),
            ]);

        $updatedUser = User::find($user->id);

        $this->assertEquals($name, $updatedUser->name);
        $this->assertEquals($email, $updatedUser->email);
    }

    /**
     * 10. Test update user with invalid token
     *
     * @return void
     */
    public function testUpdateUserWithInvalidToken()
    {
        //Create new User
        $user = User::factory()->create();

        $name =  $this->faker->name;
        $email =  $this->faker->unique()->safeEmail();

        $response = $this->put(route('users.update'), [
            'token' => $this->faker->text(30),
            'id' => $user->id,
            'name' => $name,
            'email' => $email,
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'status' => __('UserManagement::messages.invalid_token'),
            ]);

        $updatedUser = User::find($user->id);

        $this->assertEquals($user->name, $updatedUser->name);
        $this->assertEquals($user->email, $updatedUser->email);
    }

    /**
     * 11. Test update user with non-existent id
     *
     * @return void
     */
    public function testUpdateUserWithNonExistentId()
    {
        // Create a user and generate a valid JWT token
        $token = $this->getUserToken();

        $user = User::factory()->create();

        $name =  $this->faker->name;
        $email =  $this->faker->unique()->safeEmail();

        $response = $this->put(route('users.update'), [
            'token' => $token,
            'id' => 999,
            'name' => $name,
            'email' => $email,
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'status' => false,
                'message' => __('UserManagement::messages.try_again'),
            ]);

        $updatedUser = User::find($user->id);

        $this->assertEquals($user->name, $updatedUser->name);
        $this->assertEquals($user->email, $updatedUser->email);
    }

    /**
     * 12. Test delete user with id and Valid Token
     *
     * @return void
     */
    public function testDeleteUserWithValidToken()
    {
        // Create a user and generate a valid JWT token
        $token = $this->getUserToken();

        // Create a user
        $user = User::factory()->create();

        // Make a delete request to the API
        $response = $this->delete(route('users.delete',['id'=>$user->id,'token' => $token]));

        // Assert that the response status code is 200 (OK)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the user has been deleted from the database
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /**
     * 13. Test delete user with id and Invalid Token
     *
     * @return void
     */
    public function testDeleteUserWithInvalidToken()
    {
        // Create a user
        $user = User::factory()->create();

        // Make a delete request to the API
        $response = $this->delete(route('users.delete',['id'=>$user->id,'token' => $this->faker->text(30)]));

        // Assert that the response status code is 200 (OK)
        $response->assertStatus(Response::HTTP_OK)
                ->assertJson([
                    'status' => __('UserManagement::messages.invalid_token'),
                ]);

        // Assert that the user has not been deleted from the database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    /**
     * 14. Test delete user with non-existent id and valid Token
     *
     * @return void
     */
    public function testDeleteUserWithNonExistentIdAndValidToken()
    {
        // Create a user and generate a valid JWT token
        $token = $this->getUserToken();

        // Create a user
        $user = User::factory()->create();

        // Make a delete request to the API
        $response = $this->delete(route('users.delete',['id'=>999,'token' => $token]));

        // Assert that the response status code is 404 (Not Found)
        $response->assertStatus(Response::HTTP_NOT_FOUND);

        // Assert that the user has not been deleted from the database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

     /**
     * 15. Test fetch user roles with valid Token
     *
     * @return void
     */
    public function testFetchUserRolesWithValidToken()
    {
        // Create a user and generate a valid JWT token
        $token = $this->getUserToken();

        $user = User::factory()->create();
        $role = Roles::factory()->count(2)->create();

        // Create a user and some roles
        $userRole1 = UserRole::factory()->create(['user_id' => $user->id,'role_id' => $role[0]->id]);
        $userRole2 = UserRole::factory()->create(['user_id' => $user->id,'role_id' => $role[1]->id]);

         // Assert that the check user exist in the database
         $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

          // Assert that the roles exist in the database
        $this->assertDatabaseHas('roles', [
            'id' => $role->pluck('id')->toArray()
        ]);


        // Make a get request to the API
        $response = $this->get(route('get_user_role',['id'=>$user->id,'token' => $token]));

        // Assert that the response status code is 200 (OK)
        $response->assertStatus(Response::HTTP_OK);

       // Assert that the response contains the expected data
        $response->assertJson([
            'status'  => true,
            'message' => __('UserManagement::messages.user.role.fetch'),
        ]);
    }


    /**
     * 16. Test fetch user roles with Invalid Token
     *
     * @return void
     */
    public function testFetchUserRolesWithInvalidToken()
    {

        $user = User::factory()->create();
        $role = Roles::factory()->count(2)->create();

        // Create a user and some roles
        $userRole1 = UserRole::factory()->create(['user_id' => $user->id,'role_id' => $role[0]->id]);
        $userRole2 = UserRole::factory()->create(['user_id' => $user->id,'role_id' => $role[1]->id]);

         // Assert that the check user exist in the database
         $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

          // Assert that the roles exist in the database
            $this->assertDatabaseHas('roles', [
                'id' => $role->pluck('id')->toArray()
            ]);


        // Make a get request to the API
        $response = $this->get(route('get_user_role',['id'=>$user->id,'token' => $this->faker->text(30)]));

        // Assert that the response status code is 200 (OK)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected data
        $response->assertJson([
            'status'  => __('UserManagement::messages.invalid_token'),
        ]);
    }

    /**
     * 17. Test fetch user with non-existent id and valid Token
     *
     * @return void
     */
    public function testFetchUserWithNonExistenetIdAndValidToken()
    {

        // Create a user and generate a valid JWT token
        $token = $this->getUserToken();

        $userId = 999;

         // Assert that the check user not exist in the database
         $this->assertDatabaseMissing('users', [
            'id' => $userId,
        ]);

        // Make a get request to the API
        $response = $this->get(route('get_user_role',['id'=>$userId,'token' => $token]));

        // Assert that the response status code is 200 (OK)
        $response->assertStatus(Response::HTTP_OK);

        // Assert that the response contains the expected data
        $response->assertJson([
            'status'  => true,
            'message' => __('UserManagement::messages.user.role.fetch'),
            'data'    => []
        ]);
    }

    /**
     * 18. Test assign role to user with valid token
     */
    public function testAssignRoleToUserWithValidToken()
    {
        $token = $this->getUserToken();

        $user = User::factory()->create();
        $role = Roles::factory()->create();

        // Make a post request to the API
        $response = $this->post(route('assign.role', ['id' => $user->id]), [
            'token' => $token,
            'role_id' => $role->id,
        ]);
        // Assert that the response status code is 200 (OK)
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'status' => true,
                'message' => __('UserManagement::messages.user.role.assigned'),
            ]);
    }

    /**
     * 19. Test delete role from user with valid token
     */
    public function testDeleteRoleFromUserWithValidToken()
    {
        $token = $this->getUserToken();

        $user = User::factory()->create();
        $role = Roles::factory()->create();

        // Assign role to user
        UserRole::factory()->create(['user_id' => $user->id,'role_id' => $role->id]);

        // Make a post request to the API
        $response = $this->delete(route('remove.user_role', ['id' => $user->id,'role_id' =>$role->id,'token' =>$token]));

        // Assert that the response status code is 200 (OK)
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['status' => true, 'message' => 'Role Removed Successfully']);
        $this->assertDatabaseMissing('user_roles', ['user_id' => $user->id, 'role_id' => $role->id]);
    }


    /**
     * 20. Test delete role from user with Invalid token
     */
    public function testDeleteRoleFromUserWithInvalidToken()
    {
        $user = User::factory()->create();
        $role = Roles::factory()->create();

        // Assign role to user
        UserRole::factory()->create(['user_id' => $user->id,'role_id' => $role->id]);

        // Make a post request to the API
        $response = $this->delete(route('remove.user_role', ['id' => $user->id,'role_id' =>$role->id,'token' =>$this->faker->text(30)]));

        // Assert that the response status code is 200 (OK)
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['status' => __('UserManagement::messages.invalid_token')]);
        $this->assertDatabaseHas('user_roles', ['user_id' => $user->id, 'role_id' => $role->id]);
    }

    private function getUserToken()
    {
        $user = User::factory()->create();
        return JWTAuth::fromUser($user);
    }
}
