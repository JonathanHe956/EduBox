<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Opcion extends Model
{
    protected $table = 'opciones';
    protected $fillable = ['pregunta_id', 'opcion', 'es_correcta'];
    
    protected $casts = [
        'es_correcta' => 'boolean',
    ];

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(Pregunta::class, 'pregunta_id');
    }
}
