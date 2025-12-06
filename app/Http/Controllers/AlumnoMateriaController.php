<?php

namespace App\Http\Controllers;

use App\Models\alumno;
use App\Models\materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlumnoMateriaController extends Controller
{
    /**
     * Inscribir alumno en materia (genérico).
     */
    public function inscribir(Request $request)
    {
        $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'materia_id' => 'required|exists:materias,id',
        ]);

        $alumno = alumno::find($request->alumno_id);
        $materia = materia::find($request->materia_id);

        // Verificar límite de alumnos
        if ($materia->alumnos()->count() >= 40) {
            session()->flash('error', 'La materia ha alcanzado el límite máximo de 40 alumnos.');
            return redirect()->back();
        }

        // Verificar si la carrera del alumno coincide con la de la materia
        if ($alumno->carrera_id !== $materia->carrera_id) {
            session()->flash('error', 'No se puede realizar esta acción.');
            return redirect()->back();
        }

        // Verificar si el alumno alcanzó el máximo de 5 materias
        if ($alumno->materias()->count() >= 5) {
            session()->flash('error', 'El alumno ya tiene inscrito el máximo de 5 materias.');
            return redirect()->back();
        }

        // Verificar si ya está inscrito
        if ($alumno->materias()->where('materia_id', $request->materia_id)->exists()) {
            session()->flash('error', 'El alumno ya está inscrito en esta materia.');
            return redirect()->back();
        }

        // Verificar calificación previa en historial
        $calificacionPrevia = DB::table('alumno_materias_historial')
            ->where('alumno_id', $request->alumno_id)
            ->where('materia_id', $request->materia_id)
            ->orderBy('fecha_baja', 'desc')
            ->first();

        $alumno->materias()->attach($request->materia_id, [
            'calificacion' => $calificacionPrevia ? $calificacionPrevia->calificacion : null
        ]);

        session()->flash('mensaje', 'Alumno inscrito exitosamente en la materia.');
        return redirect()->back();
    }

    /**
     * Dar de baja alumno de materia.
     */
    public function desinscribir(Request $request)
    {
        $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'materia_id' => 'required|exists:materias,id',
        ]);

        $alumno = alumno::find($request->alumno_id);
        
        // Obtener calificación actual antes de eliminar
        $inscripcionActual = $alumno->materias()->where('materia_id', $request->materia_id)->first();
        
        if ($inscripcionActual && $inscripcionActual->pivot->calificacion !== null) {
            // Guardar en historial
            DB::table('alumno_materias_historial')->insert([
                'alumno_id' => $request->alumno_id,
                'materia_id' => $request->materia_id,
                'calificacion' => $inscripcionActual->pivot->calificacion,
                'fecha_baja' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $alumno->materias()->detach($request->materia_id);

        session()->flash('mensaje', 'Inscripción eliminada exitosamente.');
        return redirect()->back();
    }

    /**
     * Inscribir alumno en materia desde vista de alumno.
     */
    public function inscribirDesdeAlumno(Request $request, alumno $alumno)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
        ]);

        $materia = materia::find($request->materia_id);

        // Verificar límite de alumnos
        if ($materia->alumnos()->count() >= 40) {
            session()->flash('error', 'La materia ha alcanzado el límite máximo de 40 alumnos.');
            return redirect()->back();
        }

        // Verificar si la carrera del alumno coincide con la de la materia
        if ($alumno->carrera_id !== $materia->carrera_id) {
            session()->flash('error', 'No se puede realizar esta acción.');
            return redirect()->back();
        }

        // Verificar si el alumno alcanzó el máximo de 5 materias
        if ($alumno->materias()->count() >= 5) {
            session()->flash('error', 'El alumno ya tiene inscrito el máximo de 5 materias.');
            return redirect()->back();
        }

        if ($alumno->materias()->where('materia_id', $request->materia_id)->exists()) {
            session()->flash('error', 'El alumno ya está inscrito en esta materia.');
            return redirect()->back();
        }

        // Verificar calificación previa en historial
        $calificacionPrevia = DB::table('alumno_materias_historial')
            ->where('alumno_id', $alumno->id)
            ->where('materia_id', $request->materia_id)
            ->orderBy('fecha_baja', 'desc')
            ->first();

        $alumno->materias()->attach($request->materia_id, [
            'calificacion' => $calificacionPrevia ? $calificacionPrevia->calificacion : null
        ]);

        session()->flash('mensaje', 'Alumno inscrito exitosamente en la materia.');
        return redirect()->back();
    }

    /**
     * Inscribir alumno en materia desde vista de materia.
     */
    public function inscribirDesdeMateria(Request $request, materia $materia)
    {
        $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
        ]);

        $alumno = alumno::find($request->alumno_id);

        // Verificar límite de alumnos
        if ($materia->alumnos()->count() >= 40) {
            session()->flash('error', 'La materia ha alcanzado el límite máximo de 40 alumnos.');
            return redirect()->back();
        }

        // Verificar si la carrera del alumno coincide con la de la materia
        if ($alumno->carrera_id !== $materia->carrera_id) {
            session()->flash('error', 'El alumno solo puede inscribirse en materias de su propia carrera.');
            return redirect()->back();
        }

        // Verificar si el alumno alcanzó el máximo de 5 materias
        if ($alumno->materias()->count() >= 5) {
            session()->flash('error', 'El alumno ya tiene inscrito el máximo de 5 materias.');
            return redirect()->back();
        }

        if ($materia->alumnos()->where('alumno_id', $request->alumno_id)->exists()) {
            session()->flash('error', 'El alumno ya está inscrito en esta materia.');
            return redirect()->back();
        }

        // Verificar calificación previa en historial
        $calificacionPrevia = DB::table('alumno_materias_historial')
            ->where('alumno_id', $request->alumno_id)
            ->where('materia_id', $materia->id)
            ->orderBy('fecha_baja', 'desc')
            ->first();

        $materia->alumnos()->attach($request->alumno_id, [
            'calificacion' => $calificacionPrevia ? $calificacionPrevia->calificacion : null
        ]);

        session()->flash('mensaje', 'Alumno inscrito exitosamente en la materia.');
        return redirect()->back();
    }
}
