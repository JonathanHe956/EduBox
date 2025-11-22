<?php

namespace App\Http\Controllers;

use App\Models\alumno;
use App\Models\materia;
use App\Models\carrera;
use App\Models\docente;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materias = materia::all();
        return view("materia.index", ["materias"=>$materias]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $carreras = carrera::all();
        return view("materia.create", ["carreras"=>$carreras]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "nombre" => "required|min:5",
            "creditos" => "required|integer|min:4|max:5",
            "carrera_id" => "required|exists:carreras,id",
        ], [
            'creditos.min' => 'La materia debe tener un mínimo de 4 créditos.',
            'creditos.max' => 'La materia no puede exceder los 5 créditos.',
        ]);

        // Verificar que no se excedan los créditos totales de la carrera
        $carrera = carrera::findOrFail($request->carrera_id);
        $creditosActuales = $carrera->materias()->sum('creditos');
        $creditosTotales = $creditosActuales + $request->creditos;

        if ($creditosTotales > $carrera->creditos) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'creditos' => "No se puede crear la materia. La carrera '{$carrera->nombre}' tiene {$carrera->creditos} créditos totales, actualmente tiene {$creditosActuales} créditos asignados. Agregar {$request->creditos} créditos excedería el límite (total: {$creditosTotales})."
                ]);
        }

        $nuevamateria = new materia();
        $nuevamateria->nombre = $request->nombre;
        $nuevamateria->creditos = $request->creditos;
        $nuevamateria->carrera_id = $request->carrera_id;
        $nuevamateria->save();
        session()->flash("mensaje", "Materia creada exitosamente.");
        return redirect()->route("materia.index");
    }

    /**
     * Display the specified resource.
     */
    public function show(materia $materia)
    {
        $materia->load('carrera', 'alumnos.carrera', 'docente.carrera');

        // Obtener alumnos no inscritos para el formulario de inscripción
        $alumnosNoInscritos = alumno::where('carrera_id', $materia->carrera_id)
            ->whereDoesntHave('materias', function ($query) use ($materia) {
                $query->where('materia_id', $materia->id);
            })
            ->withCount('materias')
            ->get();

        // Obtener docentes disponibles para asignar
        $docentesDisponibles = docente::where('carrera_id', $materia->carrera_id)
            ->withCount('materias')
            ->having('materias_count', '<', 5)
            ->where(fn ($query) => $query->whereNull('id')->orWhere('id', '!=', $materia->docente_id))
            ->get();

        return view('materia.show', compact('materia', 'alumnosNoInscritos', 'docentesDisponibles'));
    }

    public function buscar(Request $request)
    {
        $materias = materia::wherelike('nombre', "%$request->materia%")->get();
        return view("carrera.index", ["materias"=>$materias]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(materia $materia)
    {
        $carreras = carrera::all();
        return view("materia.edit", ["materia"=>$materia, "carreras"=>$carreras]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, materia $materia)
    {
        $request->validate([
            "nombre" => "required|min:5",
            "creditos" => "required|integer|min:4|max:5",
            "carrera_id" => "required|exists:carreras,id",
        ], [
            'creditos.min' => 'La materia debe tener un mínimo de 4 créditos.',
            'creditos.max' => 'La materia no puede exceder los 5 créditos.',
        ]);

        // Verificar que no se excedan los créditos totales de la carrera
        $carrera = carrera::findOrFail($request->carrera_id);
        // Calcular créditos actuales excluyendo esta materia
        $creditosActuales = $carrera->materias()->where('id', '!=', $materia->id)->sum('creditos');
        $creditosTotales = $creditosActuales + $request->creditos;

        if ($creditosTotales > $carrera->creditos) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'creditos' => "No se puede actualizar la materia. La carrera '{$carrera->nombre}' tiene {$carrera->creditos} créditos totales, actualmente tiene {$creditosActuales} créditos asignados (sin contar esta materia). Actualizar a {$request->creditos} créditos excedería el límite (total: {$creditosTotales})."
                ]);
        }

        $materia->nombre = $request->nombre;
        $materia->creditos = $request->creditos;
        $materia->carrera_id = $request->carrera_id;
        $materia->save();
        session()->flash("mensaje", "Materia actualizada exitosamente.");
        return redirect()->route("materia.index");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(materia $materia)
    {
        // Verificar si la materia tiene alumnos inscritos
        if ($materia->alumnos()->exists()) {
            return redirect()->route('materia.index')->with('error', 'No se puede eliminar la materia porque tiene alumnos inscritos.');
        }

        try {
            $materia->delete();
            return redirect()->route('materia.index')->with('mensaje', 'Materia eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('materia.index')->with('error', 'Ocurrió un error al eliminar la materia.');
        }
    }
}
