<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'event_name',
        'start_date',
        'end_date',
        'participation',
        'description',
        'location',
        'institution',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role') // opcional si quieres diferenciar coautores, ponentes, etc.
            ->withTimestamps();
    }
}
