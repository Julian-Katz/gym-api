<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Workout;
use App\Models\Exercise;
use App\Models\Set;

class WorkoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create();
        $exercises = Exercise::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);

        $workout = Workout::factory()->create([
            'user_id' => $user->id,
        ]);

        $position = 1;
        $setGroupSize = 3;
        for ($index=0; $index < $exercises->count(); $index++) {
            for ($i=0; $i < $setGroupSize; $i++) {
                Set::factory()->create([
                    'user_id' => $user->id,
                    'exercise_id' => $exercises[$index]->id,
                    'workout_id' => $workout->id,
                    'position' => $position * 1000,
                    'repetitions' => 8,
                    'break_afterwards' => $setGroupSize === $i + 1 ? 180 : 60,
                ]);
                $position++;
            }
        }
    }
}
