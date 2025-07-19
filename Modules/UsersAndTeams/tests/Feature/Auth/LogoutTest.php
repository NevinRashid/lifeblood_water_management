<?php

namespace Modules\UsersAndTeams\Tests\Feature\Auth;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Modules\UsersAndTeams\Models\User;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_user_can_logout_successfully(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test_token')->plainTextToken;


        $response = $this->postJson('api/logout', [], ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(204)->assertNoContent();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'test_token',
        ]);
    }

    public function test_guest_cannot_logout()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }
}
