<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title',
        'year',
        'month',
        'dissemination_medium',
        'evidence_document',
        'start_page',
        'end_page',
        'journal_name',
        'article_type',
        'volume',
        'journal_issue',
        'journal_series',
        'publication_place',
        'digital_identifier_doi',
        'language',
        'website_url'
    ];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role'])
            ->withTimestamps();
    }
}
