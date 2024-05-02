<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy([UserScope::class])]
class Workout extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
    ];

    public function sets(): HasMany {
        return $this->hasMany(Set::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createFreeSetPosition(int $beforeSetId = null): int
    {
        $sets = $this->sets;
        if ($beforeSetId === null) {
            return $sets->max('position') + 1000;
        }
        $basePosition = $sets->find($beforeSetId)->position;
        $positionBeforeBasePosition = $sets->where('position', '<', $basePosition)->max('position') ?? 0;
        if ($basePosition  - $positionBeforeBasePosition > 1) {
            $middlePosition = round($basePosition -  ($basePosition  - $positionBeforeBasePosition) / 2);
            return $middlePosition;
        } else {
            $sets->where('position', '>=', $basePosition)->each(function ($set, $i) {
                if ($i === 0) {
                    $set->position = $set->position + 1000;
                    $set->save();
                } else {
                    $set->position += 2000;
                    $set->save();
                }
            });
            return $basePosition;
        }
    }
}
