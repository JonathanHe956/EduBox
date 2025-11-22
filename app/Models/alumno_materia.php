<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\alumno;
use App\Models\materia;

class alumno_materia extends Model
{
    protected $fillable = ['alumno_id', 'materia_id', 'calificacion'];
    public function alumnos()
    {
        return $this->belongsToMany(alumno::class, 'alumno_materia', 'alumno_id', 'id')->withPivot('id', 'calificacion');
    }
    
    public function materias()
    {
        return $this->belongsToMany(materia::class, 'alumno_materia', 'materia_id', 'id')->withPivot('id', 'calificacion');
    }
}
