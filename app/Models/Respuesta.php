<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Respuesta extends Model
{
    protected $table = 'respuestas';
    protected $fillable = ['intento_id', 'pregunta_id', 'opcion_id', 'es_correcta', 'respuesta_abierta', 'puntos_obtenidos'];

    protected $casts = [
        'es_correcta' => 'boolean',
        'puntos_obtenidos' => 'decimal:2',
    ];

    public function intento(): BelongsTo
    {
        return $this->belongsTo(IntentoExamen::class, 'intento_id');
    }

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(Pregunta::class, 'pregunta_id');
    }

    public function opcion(): BelongsTo
    {
        return $this->belongsTo(Opcion::class, 'opcion_id');
    }
}
