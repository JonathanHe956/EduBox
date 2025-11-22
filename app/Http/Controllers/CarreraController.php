<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use Illuminate\Http\Request;

class CarreraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carreras = Carrera::all();
        return view('carrera.index', compact('carreras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('carrera.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:carreras,nombre',
            'creditos' => 'required|integer|min:1',
        ]);

        Carrera::create($request->all());

        return redirect()->route('carrera.index')->with('mensaje', 'Carrera creada exitosamente.');
    }

    public function buscar(Request $request)
    {
        $query = $request->input('Buscar_carrera');
        $carreras = Carrera::where('nombre', 'like', "%{$query}%")->get();
        return view("carrera.index", ["carreras" => $carreras]);
    }

    /**
     * Display the specified resource.
     */
    public function show(carrera $carrera)
    {
        $carrera->load('materias', 'alumnos', 'docentes');
        return view('carrera.show', compact('carrera'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(carrera $carrera)
    {
        return view('carrera.edit', compact('carrera'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Carrera $carrera)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:carreras,nombre,' . $carrera->id,
            'creditos' => 'required|integer|min:1',
        ]);

        $carrera->update($request->all());

        return redirect()->route('carrera.index')->with('mensaje', 'Carrera actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carrera $carrera)
    {
        if ($carrera->materias()->exists() || $carrera->alumnos()->exists() || $carrera->docentes()->exists()) {
            $relations = [];
            if ($carrera->materias()->exists()) $relations[] = 'materias';
            if ($carrera->alumnos()->exists()) $relations[] = 'alumnos';
            if ($carrera->docentes()->exists()) $relations[] = 'docentes';

            $errorMessage = 'No se puede eliminar la carrera porque tiene ' . implode(', ', $relations) . ' asociados.';
            return redirect()->route('carrera.index')->with('error', $errorMessage);
        }

        try {
            $carrera->delete();
            return redirect()->route('carrera.index')->with('mensaje', 'Carrera eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('carrera.index')->with('error', 'Ocurrió un error al eliminar la carrera.');
        }
    }
}
