<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class respuesta extends Model
{
    protected $table = 'respuestas';
    protected $fillable = ['intento_id', 'pregunta_id', 'opcion_id', 'es_correcta', 'respuesta_abierta', 'puntos_obtenidos'];

    protected $casts = [
        'es_correcta' => 'boolean',
        'puntos_obtenidos' => 'decimal:2',
    ];

    public function intento()
    {
        return $this->belongsTo(intentoExamen::class, 'intento_id');
    }

    public function pregunta()
    {
        return $this->belongsTo(pregunta::class, 'pregunta_id');
    }

    public function opcion()
    {
        return $this->belongsTo(opcion::class, 'opcion_id');
    }
}
