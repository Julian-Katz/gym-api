<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\UserScope;

#[ScopedBy([UserScope::class])]
class Exercise extends Model
{
    protected $fillable = ['name', 'user_id'];
    use HasFactory;
}
