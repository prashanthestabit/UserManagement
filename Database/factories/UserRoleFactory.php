<?php

namespace Modules\UserManagement\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserRoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\UserManagement\Entities\UserRole::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => function () {
                return rand(1, 10);
            },
            'role_id' => function () {
                return rand(1, 5);
            },
        ];
    }
}

