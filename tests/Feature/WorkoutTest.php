<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Workout;
use App\Models\Exercise;

class WorkoutTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_can_get_his_workouts(): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/workouts');

        $response->assertStatus(200)
            ->assertJson(['data' => [$workout->toArray()]]);
    }

    /** @test */
    public function user_can_not_get_workouts_from_another_user(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        Workout::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->actingAs($user)->getJson('/api/workouts');

        $response->assertStatus(200)
            ->assertJson([]);
    }

    /** @test */
    public function user_can_create_workout(): void
    {
        $user = User::factory()->create();

        $workout = Workout::factory()->make()->toArray();

        $response = $this->actingAs($user)->postJson('/api/workouts', $workout);

        $response->assertStatus(201)
            ->assertJson(['data' => $workout]);
    }
    /** @test */
    public function user_can_not_create_workout_for_another_user(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        $workout = Workout::factory()->make(['user_id' => $anotherUser->id])->toArray();

        $response = $this->actingAs($user)->postJson('/api/workouts', $workout);

        $this->assertDatabaseMissing('workouts', $workout);
        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_get_an_workout(): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/workouts/{$workout->id}");

        $response->assertStatus(200)
            ->assertJson(['data' => $workout->toArray()]);
    }

    /** @test */
    public function user_can_not_get_an_workout_from_another_user(): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create(['user_id' => User::factory()->create()->id]);

        $response = $this->actingAs($user)->getJson("/api/workouts/{$workout->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_update_an_workout(): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create(['user_id' => $user->id]);

        $newWorkout = Workout::factory()->make()->toArray();

        $response = $this->actingAs($user)->putJson("/api/workouts/{$workout->id}", $newWorkout);

        $response->assertStatus(200)
            ->assertJson(['data' => $newWorkout]);
    }

    /** @test */
    public function user_can_not_update_an_workout_from_another_user(): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create(['user_id' => User::factory()->create()->id]);

        $newWorkout = Workout::factory()->make()->toArray();

        $response = $this->actingAs($user)->putJson("/api/workouts/{$workout->id}", $newWorkout);

        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_delete_an_workout(): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/workouts/{$workout->id}");

        $response->assertStatus(204);
    }

    /** @test */
    public function user_can_not_delete_an_workout_from_another_user(): void
    {
        $user = User::factory()->create();
        $workout = Workout::factory()->create(['user_id' => User::factory()->create()->id]);

        $response = $this->actingAs($user)->deleteJson("/api/workouts/{$workout->id}");

        $response->assertStatus(404);
    }
}
