<?php

namespace App\Http\Controllers;

use App\Models\materia;
use App\Models\docente;
use App\Models\carrera;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocenteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $docentes = docente::with('carrera')->paginate(10);
        return view('docente.index', compact('docentes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $carreras = carrera::all();
        return view('docente.create', compact('carreras'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apaterno' => 'required|string|max:255',
            'amaterno' => 'required|string|max:255',
            'sexo' => 'required|in:M,F',
            'fecha_nacimiento' => 'required|date|before:-17 years|after:-80 years',
            'foto' => 'nullable|image|max:2048',
            'carrera_id' => 'required|exists:carreras,id',
        ]);

        $data = $request->except('foto');

        // Calcular edad y generar email
        $data['edad'] = Carbon::parse($request->fecha_nacimiento)->age;

        $nombre = explode(' ', trim($request->nombre))[0];
        $baseEmail = Str::lower($nombre . '.' . $request->apaterno);
        $email = $baseEmail . '@example.com';
        $counter = 1;
        while (User::where('email', $email)->exists() || docente::where('email', $email)->exists()) {
            $email = $baseEmail . $counter . '@example.com';
            $counter++;
        }
        $data['email'] = $email;

        // Manejar la subida de foto
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('fotos_docentes', 'public');
        }

        // Crear el docente
        docente::create($data);

        // Crear el usuario asociado
        User::create([
            'name' => $request->nombre . ' ' . $request->apaterno,
            'email' => $data['email'],
            'password' => Hash::make('password'), // O una contraseña por defecto
        ])->assignRole('docente');

        return redirect()->route('docente.index')->with('mensaje', 'Docente creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(docente $docente)
    {
        $docente->load('carrera', 'materias.carrera');

        // Obtener materias disponibles para asignar
        $materiasDisponibles = materia::where('carrera_id', $docente->carrera_id)
            ->whereNull('docente_id') // Solo materias sin docente asignado
            ->whereNotIn('id', $docente->materias->pluck('id'))
            ->get();
        return view('docente.show', compact('docente', 'materiasDisponibles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(docente $docente)
    {
        $carreras = carrera::all();
        return view('docente.edit', compact('docente', 'carreras'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, docente $docente)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apaterno' => 'required|string|max:255',
            'amaterno' => 'required|string|max:255',
            'sexo' => 'required|in:M,F',
            'fecha_nacimiento' => 'required|date|before:-17 years|after:-80 years',
            'foto' => 'nullable|image|max:2048',
            'carrera_id' => 'required|exists:carreras,id',
            'email' => ['required', 'email', 'unique:docentes,email,' . $docente->id, 'regex:/^\S+@\S+\.\S+$/'],
        ]);

        $data = $request->except('foto');
        $data['edad'] = Carbon::parse($request->fecha_nacimiento)->age;

        // Manejar la subida de foto
        if ($request->hasFile('foto')) {
            // Eliminar foto anterior si existe
            if ($docente->foto && Storage::disk('public')->exists($docente->foto)) {
                Storage::disk('public')->delete($docente->foto);
            }
            $data['foto'] = $request->file('foto')->store('fotos_docentes', 'public');
        }

        $docente->update($data);

        return redirect()->route('docente.index')->with('mensaje', 'Docente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(docente $docente)
    {
        if ($docente->materias()->exists()) {
            return redirect()->route('docente.index')->with('error', 'No se puede eliminar el docente porque tiene materias inscritas.');
        }

        // Eliminar foto si existe
        if ($docente->foto) {
            Storage::disk('public')->delete($docente->foto);
        }

        $docente->delete();

        return redirect()->route('docente.index')->with('mensaje', 'Docente eliminado exitosamente.');
    }

    public function buscar(Request $request)
    {
        $query = $request->input('Buscar_docente');
        $docentes = docente::where('nombre', 'like', "%{$query}%")
                            ->orWhere('apaterno', 'like', "%{$query}%")
                            ->paginate(10);
        return view('docente.index', compact('docentes'));
    }

    public function asignarMateria(Request $request, docente $docente)
    {
        $request->validate(['materia_id' => 'required|exists:materias,id']);
        $materia = materia::find($request->materia_id);

        $result = $this->realizarAsignacion($docente, $materia);

        return back()->with($result['success'] ? 'mensaje' : 'error', $result['message']);
    }

    public function desasignarMateria(docente $docente, materia $materia)
    {
        if ($materia->docente_id === $docente->id) {
            $materia->docente_id = null;
            $materia->save();
        }

        return back()->with('mensaje', 'Materia desasignada correctamente.');
    }

    public function asignarMateriaDesdeMateria(Request $request, materia $materia)
    {
        $request->validate(['docente_id' => 'required|exists:docentes,id']);
        $docente = docente::find($request->docente_id);

        $result = $this->realizarAsignacion($docente, $materia);

        return back()->with($result['success'] ? 'mensaje' : 'error', $result['message']);
    }

    /**
     * Lógica común para asignar una materia a un docente.
     * Devuelve array ['success' => bool, 'message' => string]
     */
    protected function realizarAsignacion(docente $docente, materia $materia): array
    {
        if (! $docente || ! $materia) {
            return ['success' => false, 'message' => 'Docente o materia no encontrados.'];
        }

        // 1. Validar que el docente no tenga más de 5 materias
        if ($docente->materias()->count() >= 5) {
            return ['success' => false, 'message' => 'El docente ya tiene asignado el máximo de 5 materias.'];
        }

        // 2. Validar que la materia sea de la misma carrera que el docente
        if ($materia->carrera_id !== $docente->carrera_id) {
            return ['success' => false, 'message' => 'El docente solo puede ser asignado a materias de su misma carrera.'];
        }

        // 3. Validar que la materia no tenga ya un docente
        if ($materia->docente_id) {
            return ['success' => false, 'message' => 'Esta materia ya tiene un docente asignado.'];
        }

        // Asignar el docente a la materia
        $materia->docente_id = $docente->id;
        $materia->save();

        return ['success' => true, 'message' => 'Materia asignada correctamente.'];
    }

    /**
     * Muestra las materias asignadas al docente autenticado.
     */
    public function materias(): \Illuminate\View\View
    {
        $user = Auth::user();

        $docente = docente::where('email', $user->email)->first();

        if (!$docente) {
            $materias = collect();
        } else {
            $materias = $docente->materias()->with('carrera')->get();
        }

        return view('docente.materias', compact('materias'));
    }

    /**
     * Muestra todos los alumnos inscritos en las materias del docente.
     */
    public function alumnos(): \Illuminate\View\View
    {
        $user = Auth::user();
        $docente = docente::where('email', $user->email)->first();

        if (!$docente) {
            abort(403, 'No se encontró el registro de docente');
        }

        // Obtener IDs de materias del docente
        $materiaIds = $docente->materias()->pluck('id');

        // Obtener alumnos únicos inscritos en esas materias
        $alumnos = \App\Models\alumno::whereHas('materias', function($query) use ($materiaIds) {
            $query->whereIn('materias.id', $materiaIds);
        })
        ->with(['carrera', 'materias' => function($query) use ($materiaIds) {
            $query->whereIn('materias.id', $materiaIds)->withPivot('calificacion');
        }])
        ->get()
        ->map(function($alumno) {
            // Calcular promedio general de las materias en común
            $calificaciones = $alumno->materias->pluck('pivot.calificacion')->filter();
            $alumno->promedio_general = $calificaciones->isNotEmpty() 
                ? round($calificaciones->avg(), 2) 
                : null;
            $alumno->materias_count = $alumno->materias->count();
            return $alumno;
        });

        return view('docente.alumnos', compact('alumnos'));
    }

    /**
     * Muestra el detalle de un alumno específico.
     */
    public function showAlumno(\App\Models\alumno $alumno, ?\App\Models\materia $materia = null): \Illuminate\View\View
    {
        $user = Auth::user();
        $docente = docente::where('email', $user->email)->first();

        if (!$docente) {
            abort(403, 'No se encontró el registro de docente');
        }

        // Obtener IDs de materias del docente
        $materiaIds = $docente->materias()->pluck('id');

        // Si se especificó una materia, verificar que sea del docente y filtrar por ella
        if ($materia) {
            if ($materia->docente_id !== $docente->id) {
                abort(403, 'No tienes permiso para ver esta materia');
            }
            
            // Filtrar solo por la materia específica
            $materiasComunes = $alumno->materias()
                ->where('materias.id', $materia->id)
                ->withPivot('calificacion')
                ->with('carrera')
                ->get();
                
            // Filtrar intentos solo de esta materia
            $intentos = \App\Models\IntentoExamen::where('alumno_id', $alumno->id)
                ->whereHas('examen', function($query) use ($materia) {
                    $query->where('materia_id', $materia->id);
                })
                ->with(['examen.materia'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Mostrar todas las materias en común
            $materiasComunes = $alumno->materias()
                ->whereIn('materias.id', $materiaIds)
                ->withPivot('calificacion')
                ->with('carrera')
                ->get();
                
            // Mostrar todos los intentos de las materias del docente
            $intentos = \App\Models\IntentoExamen::where('alumno_id', $alumno->id)
                ->whereHas('examen', function($query) use ($materiaIds) {
                    $query->whereIn('materia_id', $materiaIds);
                })
                ->with(['examen.materia'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if ($materiasComunes->isEmpty()) {
            abort(403, 'Este alumno no está inscrito en ninguna de tus materias');
        }

        // Cargar la carrera del alumno
        $alumno->load('carrera');

        return view('docente.alumno-detalle', compact('alumno', 'materiasComunes', 'intentos', 'materia'));
    }

    /**
     * Muestra los alumnos inscritos en una materia específica.
     */
    public function alumnosMateria(materia $materia): \Illuminate\View\View
    {
        $user = Auth::user();
        $docente = docente::where('email', $user->email)->first();

        if (!$docente) {
            abort(403, 'No se encontró el registro de docente');
        }

        // Verificar que la materia pertenece al docente
        if ($materia->docente_id !== $docente->id) {
            abort(403, 'No tienes permiso para ver los alumnos de esta materia');
        }

        // Obtener alumnos inscritos en la materia
        $alumnos = $materia->alumnos()
            ->withPivot('calificacion')
            ->with('carrera')
            ->get();

        return view('docente.alumnos-materia', compact('alumnos', 'materia'));
    }
}
