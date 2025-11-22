<?php

namespace App\Http\Controllers;

use App\Models\alumno;
use App\Models\materia;
use Illuminate\Http\Request;

class InscripcionController extends Controller
{


    /**
     * Store a new enrollment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'materia_id' => 'required|exists:materias,id',
        ]);

        $alumno = alumno::find($request->alumno_id);
        $materia = materia::find($request->materia_id);

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
        $previousGrade = \DB::table('alumno_materias_history')
            ->where('alumno_id', $request->alumno_id)
            ->where('materia_id', $request->materia_id)
            ->orderBy('unenrolled_at', 'desc')
            ->first();

        $alumno->materias()->attach($request->materia_id, [
            'calificacion' => $previousGrade ? $previousGrade->calificacion : null
        ]);

        session()->flash('mensaje', 'Alumno inscrito exitosamente en la materia.');
        return redirect()->back();
    }

    /**
     * Remove an enrollment.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'materia_id' => 'required|exists:materias,id',
        ]);

        $alumno = alumno::find($request->alumno_id);
        
        // Obtener calificación actual antes de eliminar
        $currentEnrollment = $alumno->materias()->where('materia_id', $request->materia_id)->first();
        
        if ($currentEnrollment && $currentEnrollment->pivot->calificacion !== null) {
            // Guardar en historial
            \DB::table('alumno_materias_history')->insert([
                'alumno_id' => $request->alumno_id,
                'materia_id' => $request->materia_id,
                'calificacion' => $currentEnrollment->pivot->calificacion,
                'unenrolled_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $alumno->materias()->detach($request->materia_id);

        session()->flash('mensaje', 'Inscripción eliminada exitosamente.');
        return redirect()->back();
    }

    /**
     * Enroll alumno in materia from alumno show.
     */
    public function enrollAlumno(Request $request, alumno $alumno)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
        ]);

        $materia = materia::find($request->materia_id);

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
        $previousGrade = \DB::table('alumno_materias_history')
            ->where('alumno_id', $alumno->id)
            ->where('materia_id', $request->materia_id)
            ->orderBy('unenrolled_at', 'desc')
            ->first();

        $alumno->materias()->attach($request->materia_id, [
            'calificacion' => $previousGrade ? $previousGrade->calificacion : null
        ]);

        session()->flash('mensaje', 'Alumno inscrito exitosamente en la materia.');
        return redirect()->back();
    }

    /**
     * Enroll alumno in materia from materia show.
     */
    public function enrollMateria(Request $request, materia $materia)
    {
        $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
        ]);

        $alumno = alumno::find($request->alumno_id);

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
        $previousGrade = \DB::table('alumno_materias_history')
            ->where('alumno_id', $request->alumno_id)
            ->where('materia_id', $materia->id)
            ->orderBy('unenrolled_at', 'desc')
            ->first();

        $materia->alumnos()->attach($request->alumno_id, [
            'calificacion' => $previousGrade ? $previousGrade->calificacion : null
        ]);

        session()->flash('mensaje', 'Alumno inscrito exitosamente en la materia.');
        return redirect()->back();
    }
}
