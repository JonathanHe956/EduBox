<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class pregunta extends Model
{
    const TIPO_MULTIPLE = 'multiple';
    const TIPO_VERDADERO_FALSO = 'verdadero_falso';
    const TIPO_ABIERTA = 'abierta';

    protected $table = 'preguntas';
    protected $fillable = ['examen_id', 'pregunta', 'tipo', 'respuesta_correcta_abierta'];

    protected $casts = [
        'tipo' => 'string',
    ];

    public function examen(): BelongsTo
    {
        return $this->belongsTo(examen::class, 'examen_id');
    }

    public function opciones(): HasMany
    {
        return $this->hasMany(opcion::class, 'pregunta_id');
    }

    // Métodos auxiliares
    public function isMultiple(): bool
    {
        return $this->tipo === self::TIPO_MULTIPLE;
    }

    public function isVerdaderoFalso(): bool
    {
        return $this->tipo === self::TIPO_VERDADERO_FALSO;
    }

    public function esAbierta(): bool
    {
        return $this->tipo === self::TIPO_ABIERTA;
    }
}
