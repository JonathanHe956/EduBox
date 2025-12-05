<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AlumnoMateriaController;
use App\Http\Controllers\DocenteController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// -------------------------------------------------------------------------
// RUTAS EXCLUSIVAS PARA ADMINISTRADORES
// -------------------------------------------------------------------------

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    // Rutas de Carreras (Resource y Buscar)
    Route::resource('/carrera', CarreraController::class)->names('carrera');
    Route::post('/carrera/buscar', [CarreraController::class, 'buscar'])->name('carrera.buscar');

    // Rutas de Materias (Resource, Buscar y Asignar Docente)
    Route::resource('/materia', MateriaController::class)->names('materia')->parameters(['materia' => 'materia']);
    Route::post('/materia/buscar', [MateriaController::class, 'buscar'])->name('materia.buscar');
    Route::post('/materia/{materia}/assign-docente', [DocenteController::class, 'asignarMateriaDesdeMateria'])->name('materia.assignDocente');

    // Rutas de Alumnos (Resource y Buscar)
    Route::resource('/alumno', AlumnoController::class)->names('alumno');
    Route::post('/alumno/buscar', [AlumnoController::class, 'buscar'])->name('alumno.buscar');

    // Rutas de Docentes (Resource, Buscar, Asignación/Desasignación de Materias y Create)
    Route::resource('/docente', DocenteController::class)->names('docente');
    Route::post('/docente/buscar', [DocenteController::class, 'buscar'])->name('docente.buscar');
    Route::post('/docente/{docente}/assign-materia', [DocenteController::class, 'asignarMateria'])->name('docente.assignMateria');
    Route::delete('/docente/{docente}/unassign-materia/{materia}', [DocenteController::class, 'desasignarMateria'])->name('docente.unassignMateria');
    Route::get('/docente/create', [DocenteController::class, 'create'])->name('docente.create');

    // Rutas de Inscripción (Inscribir/Desinscribir desde Alumno/Materia/Controlador)
    Route::post('/inscripcion', [AlumnoMateriaController::class, 'inscribir'])->name('inscripcion.store');
    Route::delete('/inscripcion', [AlumnoMateriaController::class, 'desinscribir'])->name('inscripcion.destroy');
    Route::post('/alumno/{alumno}/enroll', [AlumnoMateriaController::class, 'inscribirDesdeAlumno'])->name('alumno.enroll');
    Route::post('/materia/{materia}/enroll', [AlumnoMateriaController::class, 'inscribirDesdeMateria'])->name('materia.enroll');
});

// -------------------------------------------------------------------------
// RUTAS PARA DOCENTES
// -------------------------------------------------------------------------

Route::middleware(['auth', 'verified', 'role:docente'])->group(function () {
    // Vistas de Docente
    Route::get('/docente/materias', [DocenteController::class, 'materias'])->name('docente.materias');

    Route::get('/docente/alumno/{alumno}/{materia?}', [DocenteController::class, 'showAlumno'])->name('docente.alumno.show');
    Route::get('/materia/{materia}/alumnos', [DocenteController::class, 'alumnosMateria'])->name('docente.alumnos.materia');

    // Gestión de Exámenes (Crear, Almacenar, Agregar Pregunta, Índice, Editar, Actualizar, Eliminar)
    Route::get('/materia/{materia}/examen/create', [App\Http\Controllers\ExamenController::class, 'crearParaMateria'])->name('examenes.create');
    Route::post('/materia/{materia}/examen', [App\Http\Controllers\ExamenController::class, 'store'])->name('examenes.store');
    Route::post('/examen/{examen}/pregunta', [App\Http\Controllers\ExamenController::class, 'agregarPregunta'])->name('examenes.addQuestion');
    Route::get('/materia/{materia}/examenes', [App\Http\Controllers\ExamenController::class, 'indiceParaMateria'])->name('examenes.materia');
    Route::get('/docente/examenes', [App\Http\Controllers\ExamenController::class, 'docenteIndex'])->name('examenes.mine');
    Route::get('/examen/{examen}/editar', [App\Http\Controllers\ExamenController::class, 'edit'])->name('examenes.edit');
    Route::put('/examen/{examen}', [App\Http\Controllers\ExamenController::class, 'update'])->name('examenes.update');
    Route::delete('/examen/{examen}', [App\Http\Controllers\ExamenController::class, 'destroy'])->name('examenes.destroy');
    Route::post('/respuesta/{respuesta}/calificar', [App\Http\Controllers\ExamenController::class, 'calificarRespuesta'])->name('examenes.grade-answer');
    Route::post('/intento/{intento}/calificar-masivo', [App\Http\Controllers\ExamenController::class, 'calificarIntentoMasivo'])->name('examenes.calificar-intento');
    Route::post('/intento/{intento}/publicar', [App\Http\Controllers\ExamenController::class, 'publicarCalificacion'])->name('examenes.publish-grade');
});

// -------------------------------------------------------------------------
// RUTAS PARA ESTUDIANTES
// -------------------------------------------------------------------------

Route::middleware(['auth', 'role:estudiante'])->group(function () {
    // Vistas de Estudiante
    Route::get('/alumno/materias', [AlumnoController::class, 'materias'])->name('alumno.materias');

    // Gestión de Exámenes (Intentar, Índice, Índice por Materia)
    Route::post('/examen/{examen}/intentar', [App\Http\Controllers\ExamenController::class, 'intentar'])->name('examenes.intentar');
    Route::get('/alumno/examenes', [App\Http\Controllers\ExamenController::class, 'alumnoIndex'])->name('examenes.pending');
    Route::get('/alumno/materia/{materia}/examenes', [App\Http\Controllers\ExamenController::class, 'indiceParaMateriaAlumno'])->name('examenes.materia.alumno');
});


// -------------------------------------------------------------------------
// RUTAS COMPARTIDAS O GENERALES
// -------------------------------------------------------------------------

// Ruta compartida para ver el detalle de un examen o el resultado de un intento
Route::get('/examen/{examen}', [App\Http\Controllers\ExamenController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('examenes.show');

Route::get('/examen/intento/{intento}', [App\Http\Controllers\ExamenController::class, 'result'])
    ->middleware(['auth', 'verified'])
    ->name('examenes.result');


Route::get('/mis-materias', function () {
    /** @var User|null $user */
    $user = Auth::user();
    if (! $user instanceof User) {
        abort(403);
    }

    if ($user->hasRole('docente')) {
        $docente = \App\Models\docente::where('email', $user->email)->first();
        $materias = $docente ? $docente->materias()->with('carrera')->get() : collect();
        return view('docente.materias', compact('materias', 'docente'));
    }

    if ($user->hasRole('estudiante') || $user->hasRole('student')) {
        $alumno = \App\Models\alumno::where('email', $user->email)->first();
        $materias = $alumno ? $alumno->materias()->withPivot('calificacion')->with(['carrera', 'docente'])->get() : collect();
        return view('alumno.materias', compact('materias'));
    }

    abort(403);
})->middleware(['auth', 'verified'])->name('mis.materias');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

require __DIR__.'/auth.php';