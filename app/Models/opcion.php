<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class opcion extends Model
{
    protected $table = 'opciones';
    protected $fillable = ['pregunta_id', 'opcion', 'es_correcta'];
    
    protected $casts = [
        'es_correcta' => 'boolean',
    ];

    public function pregunta()
    {
        return $this->belongsTo(pregunta::class, 'pregunta_id');
    }
}
