<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Exercise;
use Tests\TestHelper;


class ExerciseTest extends TestCase
{
    use RefreshDatabase , WithFaker;

    /** @test */
    public function user_can_get_all_his_exercises(): void
    {
        $user = User::factory()->create();
        $exercises = Exercise::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
                        ->getJson('/api/exercises');

        $response->assertStatus(200);
        $response->assertJson(['data' => $exercises->toArray()]);
    }

    /** @test */
    public function user_can_not_get_exercises_from_another_user(): void
    {
        Exercise::factory()->count(3)->create([
            'user_id' => User::factory()->create()->id,
        ]);

        $user = User::factory()->create();
        Exercise::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
                     ->getJson('/api/exercises');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['user_id' => $user->id],
                    ['user_id' => $user->id],
                    ['user_id' => $user->id],
                ],
            ]);
    }

    /** @test */
    public function user_can_get_all_his_exercises_with_name_filter(): void
    {
        $user = User::factory()->create();
        $exercises = Exercise::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
                        ->getJson('/api/exercises?name=' . $exercises[0]->name);

        $response->assertStatus(200);
        $response->assertExactJson(['data' => [$exercises[0]->toArray()]]);
    }

    /** @test */
    public function user_can_get_all_his_exercises_that_start_with_A(): void
    {
        $user = User::factory()->create();
        $exercisesA = Exercise::factory()->count(3)->create([
            'name' => 'A' . $this->faker->sentence,
            'user_id' => $user->id,
        ]);
        Exercise::factory()->count(3)->create([
            'name' => 'B' . $this->faker->sentence,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
                        ->getJson('/api/exercises?name=A%');

        $response->assertStatus(200);
        $response->assertExactJson(['data' => $exercisesA->toArray()]);
    }

    /** @test */
    public function user_can_get_all_his_exercises_sorted_by_name_desc(): void
    {
        $user = User::factory()->create();
        $exercises = Exercise::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
                        ->getJson('/api/exercises?sort_by=name&sort_order=desc');

        $sortedExercises = $exercises->sortByDesc('name')->values();

        $response->assertStatus(200);
        $this->assertTrue(TestHelper::arrays_have_same_order($sortedExercises->toArray(), $response->json()['data']));
    }

    /** @test */
    public function user_can_get_an_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
                        ->getJson("/api/exercises/{$exercise->id}");

        $response->assertStatus(200)
                ->assertJson(['data' => [
                    'id' => $exercise->id,
                    'name' => $exercise->name,
                ]]);
    }

    /** @test */
    public function user_can_not_get_an_exercise_from_another_user(): void
    {
        $exercise = Exercise::factory()->create([
            'user_id' => User::factory()->create()->id,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
                        ->getJson("/api/exercises/{$exercise->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_create_an_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->make();

        $response = $this->actingAs($user)
                        ->postJson('/api/exercises', [
                            'name' => $exercise->name,
                        ]);

        $response->assertStatus(201)
                ->assertJson(
                    ['data' => ['name' => $exercise->name,]]
                );
        $this->assertDatabaseHas('exercises', [
            'name' => $exercise->name,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function user_can_not_create_an_exercise_without_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                        ->postJson('/api/exercises', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors('name');
    }

    /** @test */
    public function user_can_update_an_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->create([
            'user_id' => $user->id,
        ]);

        $newName = $this->faker->sentence;

        $response = $this->actingAs($user)
                        ->putJson("/api/exercises/{$exercise->id}", [
                            'name' => $newName,
                        ]);

        $response->assertStatus(200)
                ->assertJson(['data' => [
                    'id' => $exercise->id,
                    'name' => $newName,
                ]]);
        $this->assertDatabaseHas('exercises', [
            'id' => $exercise->id,
            'name' => $newName,
        ]);
    }

    /** @test */
    public function user_can_not_update_an_exercise_from_another_user(): void
    {
        $exercise = Exercise::factory()->create([
            'user_id' => User::factory()->create()->id,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
                        ->putJson("/api/exercises/{$exercise->id}", [
                            'name' => $this->faker->sentence,
                        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_delete_an_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
                        ->deleteJson("/api/exercises/{$exercise->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('exercises', [
            'id' => $exercise->id,
        ]);
    }

    /** @test */
    public function user_can_not_delete_an_exercise_from_another_user(): void
    {
        $exercise = Exercise::factory()->create([
            'user_id' => User::factory()->create()->id,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
                        ->deleteJson("/api/exercises/{$exercise->id}");

        $response->assertStatus(404);
    }
}
