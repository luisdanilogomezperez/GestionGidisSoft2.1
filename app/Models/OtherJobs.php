<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherJobs extends Model
{
    protected $fillable = [
        'product_name',
        'year',
        'month',
        'language',
        'dissemination_medium',
        'publication_place',
        'purpose',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }
}
