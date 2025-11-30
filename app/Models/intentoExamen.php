<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class intentoExamen extends Model
{
    const ESTADO_EN_PROGRESO = 'en_progreso';
    const ESTADO_EN_REVISION = 'en_revision';
    const ESTADO_CALIFICADO = 'calificado';

    protected $table = 'intento_examenes';
    protected $fillable = ['examen_id', 'alumno_id', 'puntuacion', 'total', 'estado', 'revisado_por', 'fecha_revision', 'version_anterior'];

    // Relaciones
    public function examen(): BelongsTo
    {
        return $this->belongsTo(examen::class, 'examen_id');
    }

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(alumno::class, 'alumno_id');
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(respuesta::class, 'intento_id');
    }

    // Métodos auxiliares
    public function isEnRevision(): bool
    {
        return $this->estado === self::ESTADO_EN_REVISION;
    }

    public function isCalificado(): bool
    {
        return $this->estado === self::ESTADO_CALIFICADO;
    }

    // Scopes (Alcances)
    public function scopeEnRevision($query)
    {
        return $query->where('estado', self::ESTADO_EN_REVISION);
    }
}
