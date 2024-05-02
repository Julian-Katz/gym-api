<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'exercise_id' => $this->exercise_id,
            'workout_id' => $this->workout_id,
            'user_id' => $this->user_id,
            'position' => $this->position,
            'repetitions' => $this->repetitions,
            'duration' => $this->duration,
            'break_afterwards' => $this->break_afterwards,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
