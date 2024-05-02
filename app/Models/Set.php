<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

#[ScopedBy([UserScope::class])]
class Set extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_id',
        'workout_id',
        'user_id',
        'position',
        'repetitions',
        'duration',
        'break_afterwards',
    ];

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function updateBefore(Array $setData, int $beforeSetId = null): Set
    {
        DB::transaction(function () use (&$setData, $beforeSetId) {
            $setData['position'] = $this->workout->createFreeSetPosition($beforeSetId);
            $this->update($setData);
        });
        return $this;
    }

    public static function createBefore(Array $setData, int $beforeSetId = null)
    {
        $set = null;
        DB::transaction(function () use (&$set, $setData, $beforeSetId) {
            $setData['position'] = Workout::find($setData['workout_id'])->createFreeSetPosition($beforeSetId);
            $set = Set::create($setData);
        });
        return $set;
    }
}
