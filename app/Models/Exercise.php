<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\UserScope;

#[ScopedBy([UserScope::class])]
class Exercise extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id'];

    public function workouts()
    {
        return $this->belongsToMany(Workout::class)
            ->withPivot(['position', 'repetitions', 'break_afterwards'])
            ->withTimestamps();
    }
}
