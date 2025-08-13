<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presentation extends Model
{
    protected $fillable = [
        'title',
        'main_author',
        'main_author_id',
        'scope',
        'institution',
        'place',
        'year',
        'month',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
