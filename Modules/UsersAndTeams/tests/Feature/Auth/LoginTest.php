<?php

namespace Modules\UsersAndTeams\Tests\Feature\Auth;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\UsersAndTeams\Models\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_login_successfully(): void
{
    $user = User::factory()->create([
        'email' => 'example@gmail.com',
        'password' => bcrypt('ex1ex2com')
    ]);

    $userCredintial = [
        'email' => 'example@gmail.com',
        'password' => 'ex1ex2com'
    ];

    $response = $this->postJson('api/login', $userCredintial);

    $response->assertStatus(200)->assertJson([
        'status' => true,
        'message' =>  'Login Successfully',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
        'code' => 200
    ]);

    $this->assertNotNull($response->json('token'));

    $this->assertAuthenticatedAs($user);
}


    public function test_login_fails_with_incorrect_password()
    {
        User::factory()->create(attributes: [
            'email' => 'example@gmail.com',
            'password' => bcrypt('ex1ex2com')
        ]);

        $userCredintial = [
            'email' => 'example@gmail.com',
            'password' => 'example',
        ];

        $response = $this->postJson('/api/login', $userCredintial);

        $response->assertStatus(400)->assertJson([
            'success' => false,
            'message' => 'Your inputs do not match our credential!',
            'data' => null,
        ]);
    }

    public function test_login_fails_with_non_existent_email()
    {
        $userCredintial = [
            'email' => 'example@gmail.com',
            'password' => 'ex1ex2com'
        ];

        $response = $this->postJson('/api/login', $userCredintial);

        $response->assertStatus(422)->assertJsonValidationErrors('email');
    }

   public function test_login_validation_fails(): void
{
    $user = User::factory()->create([
        'email' => 'example@gmail.com',
        'password' => bcrypt('ex1ex2com')
    ]);

    $userCredintial = [
        'email' => 'example@gmail.com',
    ];

    $response = $this->postJson('api/login', $userCredintial);

    $response->assertStatus(422)->assertJsonValidationErrors('password');
}

}
