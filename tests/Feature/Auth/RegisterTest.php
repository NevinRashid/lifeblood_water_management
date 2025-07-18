<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_register_successfully(): void
    {
        $userCredintial = [
            'name' => 'name',
            'email' => 'example@gmail.com',
            'password' => 'PaSSworD1@4',
            'password_confirmation' => 'PaSSworD1@4',
        ];

        $response = $this->postJson('api/register', $userCredintial);

        $response->assertStatus(201)
            ->assertJson(
                [
                    'status' => true,
                    'message' => 'register Successfully',
                    'data' => [
                        'name' => 'name',
                    ]
                ]
            );

        $this->assertDatabaseHas(
            'users',
            [
                'email' => 'example@gmail.com',
                'name' => 'name',
            ]
        );

        $this->assertArrayNotHasKey(
            'password',
            $response->json('data')
        );
    }

    public function test_register_validation_fails()
    {
        $userCredintial = [
            'name' => 'name',
            'password' => 'PaSSworD1@4',
            'password_confirmation' => 'PaSSworD1@4',
        ];

        $response = $this->postJson('api/register', $userCredintial);

        $response->assertStatus(422)->assertJsonValidationErrors('email');

        $this->assertDatabaseMissing('users', [
            'name' => 'name',
        ]);
    }

    public function test_registration_fails_if_email_already_exists()
    {
        User::factory()->create([
            'email' => 'example@gmail.com'
        ]);

        $userCredintial = [
            'name' => 'name',
            'email' => 'example@gmail.com',
            'password' => 'PaSSworD1@4',
            'password_confirmation' => 'PaSSworD1@4',
        ];

        $response = $this->postJson('api/register', $userCredintial);

        $response->assertStatus(422)->assertJsonValidationErrors('email');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_registration_fails_if_passwords_do_not_match()
    {
        $userCredintial = [
            'name' => 'name',
            'email' => 'example@gmail.com',
            'password' => 'PaSSworD1@4',
            'password_confirmation' => 'PaSSworD1@542132134',
        ];

        $response = $this->postJson('api/register', $userCredintial);

        $response->assertStatus(422)->assertJsonValidationErrors('password');
    }
}
