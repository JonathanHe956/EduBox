<?php

namespace App\Http\Controllers;

use App\Models\Materia; // Importa el modelo Materia
use App\Models\alumno;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AlumnoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $alumnos = alumno::paginate(10);
        return view('alumno.index', compact('alumnos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $carreras = \App\Models\carrera::all();
        return view('alumno.create', compact('carreras'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apaterno' => 'nullable|string|max:255',
            'amaterno' => 'nullable|string|max:255',
            'sexo' => 'required|in:M,F',
            'fecha_nacimiento' => 'required|date|before:-17 years|after:-80 years',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'carrera_id' => 'required|exists:carreras,id',
        ]);

        // Calcular edad basada en fecha de nacimiento
        $fechaNacimiento = Carbon::parse($request->fecha_nacimiento);
        $edadCalculada = $fechaNacimiento->age;

        // Generar email automáticamente
        $suffix = $request->apaterno ? $request->apaterno : Carbon::parse($request->fecha_nacimiento)->year;
        $nombre = explode(' ', trim($request->nombre))[0];
        $email = strtolower($nombre . '.' . $suffix . '@example.com');
        $counter = 1;
        $originalEmail = $email;
        while (\App\Models\alumno::where('email', $email)->exists()) {
            $email = strtolower($request->nombre . '.' . $suffix . $counter . '@example.com');
            $counter++;
        }

        $alumno = new \App\Models\alumno();
        $alumno->nombre = $request->nombre;
        $alumno->apaterno = $request->apaterno;
        $alumno->amaterno = $request->amaterno;
        $alumno->sexo = $request->sexo;
        $alumno->fecha_nacimiento = $request->fecha_nacimiento;
        $alumno->edad = $edadCalculada;
        $alumno->carrera_id = $request->carrera_id;
        $alumno->email = $email;

        // Manejar la subida de foto
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('fotos_alumnos', 'public');
            $alumno->foto = $fotoPath;
        }

        $alumno->save();

        // Asignar rol de estudiante con email único
        $suffix = $alumno->apaterno ? $alumno->apaterno : Carbon::parse($alumno->fecha_nacimiento)->year;
        $nombre = explode(' ', trim($alumno->nombre))[0];
        $email = strtolower($nombre . '.' . $suffix . '@example.com');
        $counter = 1;
        $originalEmail = $email;
        while (\App\Models\User::where('email', $email)->exists()) {
            $email = strtolower($alumno->nombre . '.' . $suffix . $counter . '@example.com');
            $counter++;
        }

        $user = \App\Models\User::create([
            'name' => $alumno->nombre . ' ' . $alumno->apaterno,
            'email' => $email,
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('estudiante');

        session()->flash('mensaje', 'Alumno creado exitosamente.');
        return redirect()->route('alumno.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(alumno $alumno)
    {
        // Obtener los IDs de las materias en las que el alumno ya está inscrito
        $materiasInscritasIds = $alumno->materias()->pluck('materias.id')->toArray();

        // Obtener solo las materias de la misma carrera del alumno que no están inscritas
        $materiasDisponibles = Materia::where('carrera_id', $alumno->carrera_id)
            ->whereNotIn('id', $materiasInscritasIds)
            ->get();

        return view("alumno.show", compact('alumno', 'materiasDisponibles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(alumno $alumno)
    {
        $carreras = \App\Models\carrera::all();
        return view('alumno.edit', compact('alumno', 'carreras'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, alumno $alumno)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apaterno' => 'nullable|string|max:255',
            'amaterno' => 'nullable|string|max:255',
            'sexo' => 'required|in:M,F',
            'fecha_nacimiento' => 'required|date|before:-17 years|after:-80 years',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'carrera_id' => 'required|exists:carreras,id',
        ]);

        // Calcular edad basada en fecha de nacimiento
        $fechaNacimiento = Carbon::parse($request->fecha_nacimiento);
        $edadCalculada = $fechaNacimiento->age;

        $alumno->nombre = $request->nombre;
        $alumno->apaterno = $request->apaterno;
        $alumno->amaterno = $request->amaterno;
        $alumno->sexo = $request->sexo;
        $alumno->fecha_nacimiento = $request->fecha_nacimiento;
        $alumno->edad = $edadCalculada;
        // Validar cambio de carrera
        if ($request->carrera_id != $alumno->carrera_id) {
            if ($alumno->materias()->count() > 0) {
                return back()->withErrors(['carrera_id' => 'No se puede cambiar de carrera porque el alumno tiene materias asignadas.'])->withInput();
            }
        }

        $alumno->carrera_id = $request->carrera_id;

        // Actualizar email si cambia nombre o apellido
        if ($alumno->isDirty(['nombre', 'apaterno'])) {
            $suffix = $request->apaterno ? $request->apaterno : Carbon::parse($request->fecha_nacimiento)->year;
            $nombre = explode(' ', trim($request->nombre))[0];
            $email = strtolower($nombre . '.' . $suffix . '@example.com');
            
            // Asegurar unicidad si cambió el email
            if ($email !== $alumno->email) {
                $counter = 1;
                while (\App\Models\alumno::where('email', $email)->where('id', '!=', $alumno->id)->exists()) {
                    $email = strtolower($nombre . '.' . $suffix . $counter . '@example.com');
                    $counter++;
                }
                
                // Actualizar también el usuario asociado para mantener el acceso y la consistencia de datos
                $user = \App\Models\User::where('email', $alumno->email)->first();
                if ($user) {
                    $nombreCompleto = $request->nombre;
                    if ($request->apaterno) {
                        $nombreCompleto .= ' ' . $request->apaterno;
                    }
                    
                    $user->update([
                        'name' => $nombreCompleto,
                        'email' => $email,
                    ]);
                }

                $alumno->email = $email;
            }
        }

        // Manejar la subida de foto
        if ($request->hasFile('foto')) {
            // Eliminar foto anterior si existe
            if ($alumno->foto && Storage::disk('public')->exists($alumno->foto)) {
                Storage::disk('public')->delete($alumno->foto);
            }
            $fotoPath = $request->file('foto')->store('fotos_alumnos', 'public');
            $alumno->foto = $fotoPath;
        }

        $alumno->save();

        session()->flash('mensaje', 'Alumno actualizado exitosamente.');
        return redirect()->route('alumno.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(alumno $alumno)
    {
        // Verificar si el alumno tiene materias inscritas
        if ($alumno->materias()->count() > 0) {
            session()->flash('error', 'No se puede eliminar el alumno porque tiene materias inscritas.');
            return redirect()->route('alumno.index');
        }

        $alumno->delete();
        session()->flash('mensaje', 'Alumno eliminado exitosamente.');
        return redirect()->route('alumno.index');
    }

    public function buscar(Request $request)
    {
        $query = $request->input('Buscar_alumno');
        $alumnos = alumno::where('nombre', 'like', '%' . $query . '%')
                        ->orWhere('apaterno', 'like', '%' . $query . '%')
                        ->orWhere('amaterno', 'like', '%' . $query . '%')
                        ->paginate(10);
        return view('alumno.index', compact('alumnos'));
    }

    public function materias()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Encontrar el registro de alumno correspondiente basado en el email
        $alumno = \App\Models\alumno::where('email', $user->email)->first();

        if (!$alumno) {
            // Si no se encuentra registro de alumno, retornar colección vacía
            $materias = collect();
        } else {
            // Obtener las materias inscritas con relaciones
            $materias = $alumno->materias()->with(['carrera', 'docente'])->get();
        }

        return view('alumno.materias', compact('materias'));
    }
}
