<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class docente extends Model
{
    protected $table = 'docentes';

    protected $fillable = ['nombre', 'apaterno', 'amaterno', 'sexo', 'fecha_nacimiento', 'edad', 'foto', 'carrera_id', 'email'];

    public function carrera()
    {
        return $this->belongsTo(carrera::class, 'carrera_id', 'id');
    }

    public function materias()
    {
        return $this->hasMany(materia::class, 'docente_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'email', 'email');
    }

    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apaterno} {$this->amaterno}";
    }
}
