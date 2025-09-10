<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    protected $fillable = [
        'title',
        'year',
        'month',
        'dissemination_medium',
        'start_page',
        'end_page',
        'total_pages',
        'book_series',
        'edition',
        'isbn',
        'publication_place',
        'publisher',
        'publisher_type',
        'discipline',
        'evidence_document',
        'credits_certificate',
        'institution_endorsement_certificate'
    ];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'book_user')
                    ->withPivot('role') // si tienes columna extra en la tabla pivote
                    ->withTimestamps();
    }
}
