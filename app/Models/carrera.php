<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\alumno;
use App\Models\materia;
use App\Models\docente;

class carrera extends Model
{
    protected $fillable = ['nombre', 'creditos'];

    public function alumnos()
    {
        return $this->hasMany(alumno::class, 'carrera_id');
    }

    public function materias()
    {
        return $this->hasMany(materia::class, 'carrera_id');
    }

    public function docentes()
    {
        return $this->hasMany(docente::class, 'carrera_id');
    }
}
