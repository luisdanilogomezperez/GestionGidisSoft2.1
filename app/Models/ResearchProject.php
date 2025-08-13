<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchProject extends Model
{
    protected $fillable = [
        'title',
        'scope',
        'project_type',
        'start_year',
        'end_year',
        'start_month',
        'end_month',
        'funding_source',
        'summary',
        'is_funded',
        'participation_type',
        'institution_role',
        'is_solidary',
        'funding_type',
        'institution_name',
        'administrative_act_code_number',
        'institution_project_code',
        'counterpart_value',
        'project_value_without_counterpart',
        'administrative_act_date',
        'number_pages',
        'linked_productions_json',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
