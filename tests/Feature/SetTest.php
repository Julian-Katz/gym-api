<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\WorkoutSeeder;
use App\Models\User;
use App\Models\Workout;
use App\Models\Exercise;

class SetTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    /** @test */
    public function user_can_get_all_sets_from_one_workout(): void
    {
        $this->seed(WorkoutSeeder::class);
        $user = User::first();
        $this->actingAs($user);
        $workout = User::first()->workouts->first();


        $response = $this->actingAs($user)
        ->getJson("/api/workouts/{$workout->id}/sets");

        $response->assertStatus(200)
            ->assertJson(['data' => $workout->sets->toArray()]);
    }

    /** @test */
    public function user_can_only_get_sets_from_his_workout(): void
    {
        $this->seed([WorkoutSeeder::class, WorkoutSeeder::class]);
        $user = User::first();
        $secondUser = User::all()->skip(1)->first();

        $this->actingAs($secondUser);
        $workoutSecondUser = $secondUser->workouts->first();

        $this->actingAs($user);
        $response = $this->getJson("/api/workouts/{$workoutSecondUser->id}/sets");

        $response->assertStatus(404);

    }

    /** @test */
    public function user_can_get_one_set_from_one_workout(): void
    {
        $this->seed(WorkoutSeeder::class);
        $this->seed(WorkoutSeeder::class);
        $user = User::first();
        $this->actingAs($user);
        $workout = $user->workouts->first();
        $set = $workout->sets->sortBy('position')->first();
        $response = $this->getJson("/api/workouts/{$workout->id}/sets/{$set->id}");

        $response->assertStatus(200)
                ->assertJson(['data' => $set->toArray()]);
    }

    /** @test */
    public function user_can_create_initial_set(): void {
        $user = User::factory()->create();
        $this->actingAs($user);
        $workout = Workout::factory()->create([
            'user_id' => $user->id,
        ]);

        $exercise = Exercise::factory()->create([
            'user_id' => $user->id,
        ]);

        $data = [
            'repetitions' => 10,
            'break_afterwards' => 55,
            'exercise_id' => $exercise->id,
        ];

        $response = $this->postJson("/api/workouts/{$workout->id}/sets", $data);

        $response->assertStatus(201)
            ->assertJson(['data' => $data]);

        $this->assertDatabaseHas('sets', [
            'workout_id' => $workout->id,
            'exercise_id' => $exercise->id,
            'position' => 1 * 1000, // initial set
            'repetitions' => 10,
            'break_afterwards' => 55,
        ]);
    }

    /** @test */
    public function user_can_create_set_before_another_set(): void
    {
        $this->seed(WorkoutSeeder::class);
        $user = User::first();
        $this->actingAs($user);

        $workout = $user->workouts->first();
        $data = [
            'repetitions' => 10,
            'break_afterwards' => 55,
            'before_set' => $workout->sets->first()->id,
            'exercise_id' => $workout->sets->first()->exercise_id,
        ];


        $response = $this->postJson("/api/workouts/{$workout->id}/sets", $data);

        unset($data['before_set']);
        $response->assertStatus(201)
            ->assertJson(['data' => $data]);

        $this->assertDatabaseHas('sets', [
            'workout_id' => $workout->id,
            'exercise_id' => $workout->sets->first()->exercise_id,
            'repetitions' => 10,
            'break_afterwards' => 55,
        ]);

        $workout->load('sets');
        $firstSet = $workout->sets->sortBy('position')->first()->toArray();
        $responseJson = $response->json();
        $this->assertEmpty(array_diff($firstSet, $responseJson['data']));
    }

    /** @test */
    public function user_can_update_set_before_another_set(): void
    {
        $this->seed(WorkoutSeeder::class);
        $user = User::first();
        $this->actingAs($user);

        $workout = $user->workouts->first();
        $set = $workout->sets->first();
        $data = [
            'repetitions' => 2,
            'break_afterwards' => 99,
            'before_set' =>  $workout->sets->sortByDesc('position')->first()->id,
            'exercise_id' => $workout->sets->first()->exercise_id,
        ];

        $response = $this->patchJson("/api/workouts/{$workout->id}/sets/{$set->id}", $data);

        unset($data['before_set']);
        $response->assertStatus(200)
            ->assertJson(['data' => $data]);

        $this->assertDatabaseHas('sets', [
            'workout_id' => $workout->id,
            'exercise_id' => $workout->sets->first()->exercise_id,
            'repetitions' => 2,
            'break_afterwards' => 99,
        ]);

        $workout->load('sets');
        $lastButOneSet = $workout->sets->sortByDesc('position')->skip(1)->first()->toArray();
        $responseJson = $response->json();

        $this->assertEmpty(array_diff($lastButOneSet, $responseJson['data']));
    }

    /** @test */
    public function user_can_remove_set(): void
    {
        $this->seed(WorkoutSeeder::class);
        $user = User::first();
        $this->actingAs($user);

        $workout = $user->workouts->first();
        $set = $workout->sets->first();

        $response = $this->deleteJson("/api/workouts/{$workout->id}/sets/{$set->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('sets', [
            'id' => $set->id,
        ]);
    }
}
