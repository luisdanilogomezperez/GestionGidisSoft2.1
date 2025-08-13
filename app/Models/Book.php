<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'isbn',
        'publication_place',
        'publisher',
        'publisher_type',
        'discipline',
        'credits_certificate',
        'institution_endorsement_certificate'
    ];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }
}
