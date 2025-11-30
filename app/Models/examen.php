<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class examen extends Model
{
    protected $table = 'examenes';
    protected $fillable = ['materia_id', 'docente_id', 'titulo', 'descripcion', 'cantidad_preguntas', 'opciones_por_pregunta', 'respuestas_correctas'];

    public function materia(): BelongsTo
    {
        return $this->belongsTo(materia::class, 'materia_id');
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(docente::class, 'docente_id');
    }

    public function preguntas()
    {
        return $this->hasMany(pregunta::class, 'examen_id');
    }

    public function intentos()
    {
        return $this->hasMany(intentoExamen::class, 'examen_id');
    }
}
