<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Article;
use App\Models\Book;
use App\Models\BookChapter;
use App\Models\DirectedProject;
use App\Models\Event;
use App\Models\OtherJobs;
use App\Models\Presentation;
use App\Models\ResearchProject;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'document_type',
        'document_number',
        'is_enable',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


   // 🔁 Relación con Artículos
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class)
            ->withPivot('role') // opcional: autor, coautor, etc.
            ->withTimestamps();
    }

    // 🔁 Relación con Libros
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    // 🔁 Relación con Capítulos de Libros
    public function bookChapters(): BelongsToMany
    {
        return $this->belongsToMany(BookChapter::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    // 🔁 Relación con Proyectos Dirigidos
    public function directedProjects(): BelongsToMany
    {
        return $this->belongsToMany(DirectedProject::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    // 🔁 Relación con Eventos
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
            ->withPivot('role') // opcional: ponente, asistente, etc.
            ->withTimestamps();
    }

    // 🔁 Relación con Otros Trabajos
    public function otherJobs(): BelongsToMany
    {
        return $this->belongsToMany(OtherJobs::class)
            ->withPivot('role') // opcional: rol en el trabajo
            ->withTimestamps();
    }

    // 🔁 Relación con Presentaciones
    public function presentations(): BelongsToMany
    {
        return $this->belongsToMany(Presentation::class)
            ->withTimestamps(); 
    }

    // 🔁 Relación con Proyectos de Investigación
    public function researchProjects(): BelongsToMany
    {
        return $this->belongsToMany(ResearchProject::class)
            ->withTimestamps();
    }
}
