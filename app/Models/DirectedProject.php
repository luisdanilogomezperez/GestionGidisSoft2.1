<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectedProject extends Model
{
    protected $fillable = [
        'name',
        'product_type',
        'start_year',
        'end_year',
        'start_month',
        'end_month',
        'orientation_type',
        'total_pages',
        'institution_name',
        'academic_program',
        'thesis_evaluation',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role') // por ejemplo: director, estudiante, co-director, etc.
            ->withTimestamps();
    }
}
