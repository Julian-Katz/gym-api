<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\UserScope;

#[ScopedBy([UserScope::class])]
class Workout extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
    ];

    public function exercises()
    {
        return $this->belongsToMany(Exercise::class)
            ->withPivot(['position', 'repetitions', 'break_afterwards'])
            ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addExercise(Exercise $exercise) {
        return $this->exercises()->attach($exercise);
    }
}
