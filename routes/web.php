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

Route::resource('/carrera', CarreraController::class)
    ->middleware(['auth', 'verified', 'role:admin'])
    ->names('carrera');

Route::post('/carrera/buscar', [CarreraController::class, 'buscar'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('carrera.buscar');

Route::resource('/materia', MateriaController::class)
    ->middleware(['auth', 'verified', 'role:admin'])
    ->names('materia')
    ->parameters(['materia' => 'materia']);

Route::post('/materia/buscar', [MateriaController::class, 'buscar'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('materia.buscar');

Route::post('/materia/{materia}/assign-docente', [DocenteController::class, 'asignarMateriaDesdeMateria'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('materia.assignDocente');

Route::resource('/alumno', AlumnoController::class)
    ->middleware(['auth', 'verified', 'role:admin'])
    ->names('alumno');

Route::post('/alumno/buscar', [AlumnoController::class, 'buscar'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('alumno.buscar');

Route::resource('/docente', DocenteController::class)
    ->middleware(['auth', 'verified', 'role:admin'])
    ->names('docente');

Route::post('/docente/buscar', [DocenteController::class, 'buscar'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('docente.buscar');

Route::post('/docente/{docente}/assign-materia', [DocenteController::class, 'asignarMateria'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('docente.assignMateria');

Route::delete('/docente/{docente}/unassign-materia/{materia}', [DocenteController::class, 'desasignarMateria'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('docente.unassignMateria');

Route::get('/docente/create', [DocenteController::class, 'create'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('docente.create');

Route::post('/inscripcion', [AlumnoMateriaController::class, 'inscribir'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('inscripcion.store');

Route::delete('/inscripcion', [AlumnoMateriaController::class, 'desinscribir'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('inscripcion.destroy');

Route::post('/alumno/{alumno}/enroll', [AlumnoMateriaController::class, 'inscribirDesdeAlumno'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('alumno.enroll');

Route::post('/materia/{materia}/enroll', [AlumnoMateriaController::class, 'inscribirDesdeMateria'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('materia.enroll');

Route::get('/docente/materias', [DocenteController::class, 'materias'])
    ->middleware(['auth', 'verified', 'role:docente'])
    ->name('docente.materias');

// Rutas para que docentes vean alumnos
Route::get('/docente/alumnos', [DocenteController::class, 'alumnos'])
    ->middleware(['auth', 'verified', 'role:docente'])
    ->name('docente.alumnos');

Route::get('/docente/alumno/{alumno}/{materia?}', [DocenteController::class, 'showAlumno'])
    ->middleware(['auth', 'verified', 'role:docente'])
    ->name('docente.alumno.show');

Route::get('/materia/{materia}/alumnos', [DocenteController::class, 'alumnosMateria'])
    ->middleware(['auth', 'verified', 'role:docente'])
    ->name('docente.alumnos.materia');



Route::get('/alumno/materias', [AlumnoController::class, 'materias'])
    ->middleware(['auth', 'role:estudiante'])
    ->name('alumno.materias');

// Rutas para exámenes
Route::get('/materia/{materia}/examen/create', [App\Http\Controllers\ExamenController::class, 'crearParaMateria'])
    ->middleware(['auth', 'verified', 'role:docente'])
    ->name('examenes.create');

Route::post('/materia/{materia}/examen', [App\Http\Controllers\ExamenController::class, 'store'])
    ->middleware(['auth', 'verified', 'role:docente'])
    ->name('examenes.store');

Route::post('/examen/{examen}/pregunta', [App\Http\Controllers\ExamenController::class, 'agregarPregunta'])
    ->middleware(['auth', 'verified', 'role:docente'])
    ->name('examenes.addQuestion');

Route::get('/examen/{examen}', [App\Http\Controllers\ExamenController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('examenes.show');

Route::post('/examen/{examen}/intentar', [App\Http\Controllers\ExamenController::class, 'intentar'])
    ->middleware(['auth', 'verified', 'role:estudiante'])
    ->name('examenes.attempt');

Route::get('/examen/intento/{intento}', [App\Http\Controllers\ExamenController::class, 'result'])
    ->middleware(['auth', 'verified'])
    ->name('examenes.result');

// Lista de exámenes disponibles para el alumno (pendientes)
Route::get('/alumno/examenes', [App\Http\Controllers\ExamenController::class, 'alumnoIndex'])
    ->middleware(['auth', 'verified', 'role:estudiante'])
    ->name('examenes.pending');

// Lista de exámenes por materia (docente)
Route::get('/materia/{materia}/examenes', [App\Http\Controllers\ExamenController::class, 'indiceParaMateria'])
    ->middleware(['auth', 'verified', 'role:docente'])
    ->name('examenes.materia');

// Lista de exámenes por materia (alumno)
Route::get('/alumno/materia/{materia}/examenes', [App\Http\Controllers\ExamenController::class, 'indiceParaMateriaAlumno'])
    ->middleware(['auth', 'verified', 'role:estudiante'])
    ->name('examenes.materia.alumno');

// Lista de exámenes creados por el docente
Route::get('/docente/examenes', [App\Http\Controllers\ExamenController::class, 'docenteIndex'])
    ->middleware(['auth', 'verified', 'role:docente'])
    ->name('examenes.mine');

// Editar / actualizar / eliminar examen (docente)
Route::get('/examen/{examen}/editar', [App\Http\Controllers\ExamenController::class, 'edit'])
    ->middleware(['auth', 'verified', 'role:docente'])
    ->name('examenes.edit');

Route::put('/examen/{examen}', [App\Http\Controllers\ExamenController::class, 'update'])
    ->middleware(['auth', 'verified', 'role:docente'])
    ->name('examenes.update');

Route::delete('/examen/{examen}', [App\Http\Controllers\ExamenController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'role:docente'])
    ->name('examenes.destroy');

// Rutas de calificación
Route::post('/respuesta/{respuesta}/calificar', [App\Http\Controllers\ExamenController::class, 'calificarRespuesta'])
    ->middleware(['auth', 'verified', 'role:docente'])
    ->name('examenes.grade-answer');

Route::post('/intento/{intento}/publicar', [App\Http\Controllers\ExamenController::class, 'publicarCalificacion'])
    ->middleware(['auth', 'verified', 'role:docente'])
    ->name('examenes.publish-grade');


// Ruta unificada para que el usuario autenticado vea sus materias
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
        $alumno = \App\Models\Alumno::where('email', $user->email)->first();
        // Cargar materias con datos pivote incluyendo calificación
        $materias = $alumno ? $alumno->materias()->withPivot('calificacion')->with('carrera')->get() : collect();
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
