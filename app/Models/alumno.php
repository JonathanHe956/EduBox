<?php

namespace App\Models;
use App\Models\carrera;

use Illuminate\Database\Eloquent\Model;

class alumno extends Model
{
    protected $table = 'alumnos';

    protected $fillable = ['nombre', 'apaterno', 'amaterno', 'sexo', 'fecha_nacimiento', 'edad', 'foto', 'carrera_id', 'email'];

    public function carrera()
    {
        return $this->belongsTo(carrera::class, 'carrera_id', 'id');
    }

    public function materias()
    {
        return $this->belongsToMany(\App\Models\materia::class, 'alumno_materias', 'alumno_id', 'materia_id')->withPivot('calificacion');
    }

    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apaterno} {$this->amaterno}";
    }
}
