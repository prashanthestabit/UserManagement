<?php

namespace Modules\UserManagement\Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersControllerTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

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
            'data' => [
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
            ],
        ]);
        $response->assertJson([
            'status' => true,
            'message' => 'Users List Fetched Successfully',
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
            'status' => 'Token is Invalid',
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
            'message' => 'User Registered Successfully',
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
            'status' => 'Token is Invalid',
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
                'message' => 'Details Fetched Successfully',
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
            'status' => 'Token is Invalid',
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
                'message' => 'Failed to fetch the details',
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
                'message' => 'User Updated Successfully',
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
                'status' => 'Token is Invalid',
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
                'message' => 'Sorry!! Try again',
            ]);

        $updatedUser = User::find($user->id);

        $this->assertEquals($user->name, $updatedUser->name);
        $this->assertEquals($user->email, $updatedUser->email);
    }

    private function getUserToken()
    {
        $user = User::factory()->create();
        return JWTAuth::fromUser($user);
    }
}
