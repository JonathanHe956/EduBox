<?php

namespace App\Models;
use App\Models\carrera;
use App\Models\alumno;
use App\Models\alumno_materia;

use Illuminate\Database\Eloquent\Model;

class materia extends Model
{
    protected $fillable = ['nombre', 'creditos', 'carrera_id', 'docente_id'];

    public function carrera()
    {
        return $this->belongsTo(carrera::class, 'carrera_id', 'id');
    }

    public function alumnos()
    {
        return $this->belongsToMany(alumno::class, 'alumno_materias', 'materia_id', 'alumno_id')->withPivot('calificacion');
    }

    public function docente()
    {
        return $this->belongsTo(docente::class);
    }
}
