<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Exercise;

use function PHPUnit\Framework\assertJson;

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
                        ->getJson('/api/exercise');

        $response->assertStatus(200);
        $response->assertJson($exercises->map(function ($exercise) {
            return [
                'id' => $exercise->id,
                'name' => $exercise->name,
            ];
        })->all());
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
                     ->getJson('/api/exercise');

        $response->assertStatus(200)
            ->assertJson([
                ['user_id' => $user->id],
                ['user_id' => $user->id],
                ['user_id' => $user->id],
            ]);
    }

    /** @test */
    public function user_can_get_an_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
                        ->getJson("/api/exercise/{$exercise->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $exercise->id,
                    'name' => $exercise->name,
                ]);
    }

    /** @test */
    public function user_can_not_get_an_exercise_from_another_user(): void
    {
        $exercise = Exercise::factory()->create([
            'user_id' => User::factory()->create()->id,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
                        ->getJson("/api/exercise/{$exercise->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_create_an_exercise(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->make();

        $response = $this->actingAs($user)
                        ->postJson('/api/exercise', [
                            'name' => $exercise->name,
                        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'name' => $exercise->name,
                ]);
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
                        ->postJson('/api/exercise', []);

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
                        ->putJson("/api/exercise/{$exercise->id}", [
                            'name' => $newName,
                        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $exercise->id,
                    'name' => $newName,
                ]);
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
                        ->putJson("/api/exercise/{$exercise->id}", [
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
                        ->deleteJson("/api/exercise/{$exercise->id}");

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
                        ->deleteJson("/api/exercise/{$exercise->id}");

        $response->assertStatus(404);
    }
}
