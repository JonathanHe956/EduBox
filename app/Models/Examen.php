<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Examen extends Model
{
    protected $table = 'examenes';
    protected $fillable = ['materia_id', 'docente_id', 'titulo', 'descripcion', 'cantidad_preguntas', 'options_per_question', 'correct_answers'];

    public function materia(): BelongsTo
    {
        return $this->belongsTo(materia::class, 'materia_id');
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(docente::class, 'docente_id');
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(Pregunta::class, 'examen_id');
    }

    public function intentos(): HasMany
    {
        return $this->hasMany(IntentoExamen::class, 'examen_id');
    }
}
