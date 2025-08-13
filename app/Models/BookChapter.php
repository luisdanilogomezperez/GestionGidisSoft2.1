<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookChapter extends Model
{
    protected $fillable = [
        'title',
        'year',
        'month',
        'dissemination_medium',
        'evidence_document',
        'start_page',
        'end_page',
        'total_pages',
        'book_series',
        'edition',
        'publication_place',
        'discipline',
        'knowledge_area',
        'institution_endorsement_certificate',
        'credits_certificate',
        'book_id'
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role') // author, coauthor
            ->withTimestamps();
    }
}
