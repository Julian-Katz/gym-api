<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Relations\HasMany;


#[ScopedBy([UserScope::class])]
class Exercise extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id'];

    public function sets(): HasMany
    {
        return $this->hasMany(Set::class);
    }
}
