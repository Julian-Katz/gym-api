<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ApiTokenTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_can_authenticate_with_valid_credentials()
    {
        $password = $this->faker->password;
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $response = $this->postJson('/api/sanctum/token', [
            'email' => $user->email,
            'password' => $password,
            'device_name' => 'Test Device',
        ]);

        $response->assertSuccessful();
        $response->assertJsonStructure(['token']);
    }

    /** @test */
    public function user_cannot_authenticate_with_invalid_password()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/sanctum/token', [
            'email' => $user->email,
            'password' => 'invalid_password',
            'device_name' => 'Test Device',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function user_can_retrieve_user_with_api_token()
    {
        $user = User::factory()->create();

        $token = $user->createToken('Test Token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user');

        $response->assertSuccessful();
        $response->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
