<?php

namespace App\Http\Controllers;

use App\Models\examen;
use App\Models\pregunta;
use App\Models\opcion;
use App\Models\intentoExamen;
use App\Models\respuesta;
use App\Models\materia;
use App\Models\docente;
use App\Models\alumno;
use App\Http\Requests\GuardarExamenRequest;
use App\Http\Requests\GuardarPreguntaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ExamenController extends Controller
{
    // Formulario para crear un examen dentro de una materia (docente)
    public function crearParaMateria(materia $materia)
    {
        return view('examenes.create', compact('materia'));
    }

    // Guardar examen básico
    public function store(GuardarExamenRequest $request, materia $materia)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado');
        }
        $docente = $user->docente;
        if (!$docente) {
            $docente = docente::where('email', $user->email)->first();
        }
        $docenteId = $docente?->id;

        DB::transaction(function () use ($request, $materia, $docenteId) {
            $examen = examen::create([
                'materia_id' => $materia->id,
                'docente_id' => $docenteId,
                'titulo' => $request->input('titulo'),
                'descripcion' => $request->input('descripcion'),
                'cantidad_preguntas' => 0,
                'opciones_por_pregunta' => 4,
                'respuestas_correctas' => 1,
            ]);

            if (!$request->has('preguntas') || count($request->input('preguntas')) < 1) {
                throw new \Exception("El examen debe tener al menos una pregunta.");
            }

            if ($request->has('preguntas')) {
                foreach ($request->input('preguntas') as $indiceP => $datosP) {
                    $tipo = $datosP['tipo'] ?? 'multiple';
                    
                    // Validar según el tipo de pregunta
                    if ($tipo === 'multiple') {
                        if (!isset($datosP['opciones']) || count($datosP['opciones']) < 2) {
                            throw new \Exception("La pregunta " . ($indiceP + 1) . " debe tener al menos 2 opciones.");
                        }
                        
                        $tieneRespuestaCorrecta = false;
                        foreach ($datosP['opciones'] as $datosO) {
                            if (isset($datosO['es_correcta'])) {
                                $tieneRespuestaCorrecta = true;
                                break;
                            }
                        }
                        
                        if (!$tieneRespuestaCorrecta) {
                            throw new \Exception("La pregunta " . ($indiceP + 1) . " debe tener al menos una respuesta correcta.");
                        }
                    } elseif ($tipo === 'verdadero_falso') {
                        if (!isset($datosP['vf_correcta'])) {
                            throw new \Exception("La pregunta " . ($indiceP + 1) . " debe tener una respuesta correcta seleccionada.");
                        }
                    }

                    // Crear la pregunta
                    $pregunta = pregunta::create([
                        'examen_id' => $examen->id,
                        'pregunta' => $datosP['texto'],
                        'tipo' => $tipo,
                        'respuesta_correcta_abierta' => $datosP['respuesta_esperada'] ?? null,
                    ]);

                    // Crear opciones según el tipo
                    if ($tipo === 'multiple' && isset($datosP['opciones'])) {
                        foreach ($datosP['opciones'] as $datosO) {
                            opcion::create([
                                'pregunta_id' => $pregunta->id,
                                'opcion' => $datosO['texto'],
                                'es_correcta' => isset($datosO['es_correcta']),
                            ]);
                        }
                    } elseif ($tipo === 'verdadero_falso') {
                        // Crear automáticamente las opciones Verdadero/Falso
                        opcion::create([
                            'pregunta_id' => $pregunta->id,
                            'opcion' => 'Verdadero',
                            'es_correcta' => $datosP['vf_correcta'] === 'verdadero',
                        ]);
                        opcion::create([
                            'pregunta_id' => $pregunta->id,
                            'opcion' => 'Falso',
                            'es_correcta' => $datosP['vf_correcta'] === 'falso',
                        ]);
                    }
                    // Las preguntas abiertas no tienen opciones
                }
                $examen->cantidad_preguntas = count($request->input('preguntas'));
                $examen->save();
            }
        });

        return redirect()->route('examenes.materia', $materia)->with('success', 'Examen creado exitosamente.');
    }

    // Añadir pregunta y sus opciones a un examen
    public function agregarPregunta(GuardarPreguntaRequest $request, examen $examen)
    {
        // Obsoleto por formulario dinámico, mantenido por compatibilidad
        return back();
    }

    // Lista de exámenes pendientes para el alumno (no intentados aún)
    public function alumnoIndex()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado');
        }
        $alumno = $user->alumno;
        if (! $alumno) {
            abort(403);
        }

        $materiaIds = $alumno->materias()->pluck('materias.id');

        $examenes = examen::whereIn('materia_id', $materiaIds)
            ->with(['intentos' => function ($q) use ($alumno) {
                $q->where('alumno_id', $alumno->id);
            }])
            ->get();

        return view('examenes.index_alumno', ['examenes' => $examenes]);
    }

    // Lista de exámenes del docente
    public function docenteIndex()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado');
        }
        $docente = $user->docente;
        if (!$docente) {
            $docente = docente::where('email', $user->email)->first();
        }
        if (! $docente) abort(403);

        $examenes = examen::where('docente_id', $docente->id)->withCount('preguntas')->get();
        return view('examenes.index_docente', compact('examenes'));
    }

    // Lista de exámenes por materia (docente)
    public function indiceParaMateria(materia $materia)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado');
        }
        $docente = $user->docente;
        if (!$docente) {
            $docente = docente::where('email', $user->email)->first();
        }
        if (! $docente) abort(403);

        // Verificar que la materia pertenece al docente

        $examenes = examen::where('materia_id', $materia->id)
            ->where('docente_id', $docente->id)
            ->withCount('preguntas')
            ->get();

        return view('examenes.index_materia', compact('examenes', 'materia'));
    }

    // Lista de exámenes por materia (alumno)
    public function indiceParaMateriaAlumno(materia $materia)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado');
        }
        $alumno = $user->alumno;
        if (!$alumno) {
            $alumno = alumno::where('email', $user->email)->first();
        }
        if (! $alumno) abort(403);

        // Verificar que el alumno está inscrito en la materia
        $estaInscrito = $alumno->materias()->where('materias.id', $materia->id)->exists();
        if (!$estaInscrito) abort(403, 'No estás inscrito en esta materia');

        // Obtener exámenes de la materia con información de intentos del alumno
        $examenes = examen::where('materia_id', $materia->id)
            ->withCount('preguntas')
            ->with(['intentos' => function($q) use ($alumno) {
                $q->where('alumno_id', $alumno->id)
                  ->where('version_anterior', false); // Solo intentos de versión actual
            }])
            ->get();

        return view('examenes.index_materia_alumno', compact('examenes', 'materia'));
    }

    // Editar examen (form)
    public function edit(examen $examen)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado');
        }
        $docente = $user->docente;
        if (!$docente) {
            $docente = docente::where('email', $user->email)->first();
        }
        if (! $docente || $examen->docente_id !== $docente->id) abort(403);

        $examen->load('preguntas.opciones');
        return view('examenes.edit', compact('examen'));
    }

    // Actualizar examen
    public function update(Request $request, examen $examen)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado');
        }
        $docente = $user->docente;
        if (!$docente) {
            $docente = docente::where('email', $user->email)->first();
        }
        if (! $docente || $examen->docente_id !== $docente->id) abort(403);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'preguntas' => 'array',
            'preguntas.*.texto' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($request, $examen) {
                // Actualizar título y descripción
                $examen->update([
                    'titulo' => $request->input('titulo'),
                    'descripcion' => $request->input('descripcion'),
                ]);

                // Validar que haya al menos una pregunta (existente o nueva)
                $totalPreguntas = 0;
                if ($request->has('preguntas')) {
                    $totalPreguntas = count($request->input('preguntas'));
                }
                
                if ($totalPreguntas < 1) {
                    throw new \Exception("El examen debe tener al menos una pregunta.");
                }

                // Sincronizar preguntas
                $idsPreguntasEntrantes = [];
                if ($request->has('preguntas')) {
                    foreach ($request->input('preguntas') as $indice => $datosP) {
                        $tipo = $datosP['tipo'] ?? 'multiple';
                        
                        // Validar según el tipo de pregunta
                        if ($tipo === 'multiple') {
                            if (!isset($datosP['opciones']) || count($datosP['opciones']) < 2) {
                                throw new \Exception("La pregunta " . ($indice + 1) . " debe tener al menos 2 opciones.");
                            }
                            
                            $tieneRespuestaCorrecta = false;
                            foreach ($datosP['opciones'] as $datosO) {
                                if (isset($datosO['es_correcta'])) {
                                    $tieneRespuestaCorrecta = true;
                                    break;
                                }
                            }
                            
                            if (!$tieneRespuestaCorrecta) {
                                throw new \Exception("La pregunta " . ($indice + 1) . " debe tener al menos una respuesta correcta.");
                            }
                        } elseif ($tipo === 'verdadero_falso') {
                            if (!isset($datosP['vf_correcta'])) {
                                throw new \Exception("La pregunta " . ($indice + 1) . " debe tener una respuesta correcta seleccionada.");
                            }
                        }

                        if (isset($datosP['id'])) {
                            // Actualizar pregunta existente
                            $idsPreguntasEntrantes[] = $datosP['id'];
                            $pregunta = pregunta::find($datosP['id']);
                            
                            if ($pregunta && $pregunta->examen_id == $examen->id) {
                                $pregunta->update([
                                    'pregunta' => $datosP['texto'],
                                    'tipo' => $tipo,
                                    'respuesta_correcta_abierta' => $datosP['respuesta_esperada'] ?? null,
                                ]);
                                
                                // Eliminar opciones antiguas y crear nuevas
                                $pregunta->opciones()->delete();
                                
                                if ($tipo === 'multiple' && isset($datosP['opciones'])) {
                                    foreach ($datosP['opciones'] as $datosO) {
                                        opcion::create([
                                            'pregunta_id' => $pregunta->id,
                                            'opcion' => $datosO['texto'],
                                            'es_correcta' => isset($datosO['es_correcta']),
                                        ]);
                                    }
                                } elseif ($tipo === 'verdadero_falso') {
                                    opcion::create([
                                        'pregunta_id' => $pregunta->id,
                                        'opcion' => 'Verdadero',
                                        'es_correcta' => $datosP['vf_correcta'] === 'verdadero',
                                    ]);
                                    opcion::create([
                                        'pregunta_id' => $pregunta->id,
                                        'opcion' => 'Falso',
                                        'es_correcta' => $datosP['vf_correcta'] === 'falso',
                                    ]);
                                }
                            }
                        } else {
                            // Nueva pregunta
                            $pregunta = pregunta::create([
                                'examen_id' => $examen->id,
                                'pregunta' => $datosP['texto'],
                                'tipo' => $tipo,
                                'respuesta_correcta_abierta' => $datosP['respuesta_esperada'] ?? null,
                            ]);
                            $idsPreguntasEntrantes[] = $pregunta->id;
                            
                            if ($tipo === 'multiple' && isset($datosP['opciones'])) {
                                foreach ($datosP['opciones'] as $datosO) {
                                    opcion::create([
                                        'pregunta_id' => $pregunta->id,
                                        'opcion' => $datosO['texto'],
                                        'es_correcta' => isset($datosO['es_correcta']),
                                    ]);
                                }
                            } elseif ($tipo === 'verdadero_falso') {
                                opcion::create([
                                    'pregunta_id' => $pregunta->id,
                                    'opcion' => 'Verdadero',
                                    'es_correcta' => $datosP['vf_correcta'] === 'verdadero',
                                ]);
                                opcion::create([
                                    'pregunta_id' => $pregunta->id,
                                    'opcion' => 'Falso',
                                    'es_correcta' => $datosP['vf_correcta'] === 'falso',
                                ]);
                            }
                        }
                    }
                }

                // Eliminar preguntas removidas
                $examen->preguntas()->whereNotIn('id', $idsPreguntasEntrantes)->delete();

                // Actualizar contador
                $examen->cantidad_preguntas = $examen->preguntas()->count();
                $examen->save();
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar el examen: ' . $e->getMessage()]);
        }

        // Marcar intentos anteriores como versión anterior
        $intentosAnteriores = intentoExamen::where('examen_id', $examen->id)
            ->where('version_anterior', false)
            ->get();

        if ($intentosAnteriores->isNotEmpty()) {
            // Marcar todos los intentos existentes como versión anterior
            intentoExamen::where('examen_id', $examen->id)
                ->where('version_anterior', false)
                ->update(['version_anterior' => true]);
            
            return redirect()->route('examenes.materia', $examen->materia_id)
                ->with('success', 'Examen actualizado. Los alumnos que ya lo realizaron tendrán un nuevo intento disponible.');
        }

        return redirect()->route('examenes.materia', $examen->materia_id)->with('success', 'Examen actualizado.');
    }

    // Eliminar examen
    public function destroy($id)
    {
        $examen = examen::find($id);

        if (!$examen) {
            return redirect()->back()->with('error', 'El examen no existe o ya fue eliminado.');
        }

        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Usuario no autenticado');
        }
        $docente = $user->docente;
        if (!$docente) {
            $docente = docente::where('email', $user->email)->first();
        }
        if (! $docente || $examen->docente_id !== $docente->id) abort(403);

        $materiaId = $examen->materia_id;

        // Obtener TODOS los alumnos inscritos en la materia para asegurar que se limpien calificaciones obsoletas
        $alumnoIds = DB::table('alumno_materias')->where('materia_id', $materiaId)->pluck('alumno_id');

        // borrar en transacción
        DB::transaction(function () use ($examen, $alumnoIds, $materiaId) {
            // Eliminar intentos asociados primero
            $examen->intentos()->delete();
            $examen->delete();

            // Recalcular calificación para cada alumno inscrito
            foreach ($alumnoIds as $alumnoId) {
                $this->actualizarCalificacionMateria($alumnoId, $materiaId);
            }
        });

        return redirect()->route('examenes.materia', $materiaId)->with('success', 'Examen eliminado y calificaciones actualizadas.');
    }

    // Mostrar examen (para docente o alumno)
    public function show(examen $examen)
    {
        $examen->load('preguntas.opciones');
        return view('examenes.show', compact('examen'));
    }

    // Alumno realiza intento
    public function intentar(Request $request, examen $examen)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        /** @var User $user */
        $user = Auth::user();
        if (!$user) abort(403);
        $alumno = $user->alumno;
        if (!$alumno) {
            $alumno = \App\Models\alumno::where('email', Auth::user()->email)->first();
        }
        if (!$alumno) abort(403, 'No se encontró el registro de alumno');

        $alumnoId = $alumno->id;

        $existe = intentoExamen::where('examen_id', $examen->id)
            ->where('alumno_id', $alumnoId)
            ->where('version_anterior', false)
            ->exists();
        if ($existe) {
            return redirect()->route('examenes.show', $examen)->withErrors(['ya_realizado' => 'Ya has realizado este examen. Solo se permite un intento.']);
        }

        $respuestas = $request->input('answers', []);
        if (count($respuestas) !== $examen->preguntas->count()) {
            return redirect()->route('examenes.show', $examen)->withErrors(['incompleto' => 'Debes responder todas las preguntas antes de enviar el examen.']);
        }

        $tieneAbiertas = $examen->preguntas()->where('tipo', pregunta::TIPO_ABIERTA)->exists();
        $estadoInicial = $tieneAbiertas ? intentoExamen::ESTADO_EN_REVISION : intentoExamen::ESTADO_CALIFICADO;

        $intento = intentoExamen::create([
            'examen_id' => $examen->id,
            'alumno_id' => $alumnoId,
            'puntuacion' => 0,
            'total' => 0,
            'estado' => $estadoInicial,
        ]);

        $puntuacion = 0;
        $total = 0;
        
        Log::info('Iniciando cálculo de puntuación', ['respuestas' => $respuestas]);
        
        // Iterar sobre TODAS las preguntas del examen
        foreach ($examen->preguntas as $preg) {
            $total++;
            $respuesta = $respuestas[$preg->id] ?? null;
            
            // Manejar según el tipo de pregunta
            if ($preg->isAbierta()) {
                // Pregunta abierta - guardar texto de respuesta
                respuesta::create([
                    'intento_id' => $intento->id,
                    'pregunta_id' => $preg->id,
                    'opcion_id' => null,
                    'respuesta_abierta' => $respuesta,
                    'es_correcta' => 0, // Se marcará como correcta cuando el docente califique
                    'puntos_obtenidos' => null, // Pendiente de calificación
                ]);
            } else {
                // Pregunta de opción múltiple o verdadero/falso
                // Ahora puede ser un array de opciones
                $opcionesSeleccionadas = is_array($respuesta) ? $respuesta : [$respuesta];
                
                // Convertir a enteros para asegurar comparación correcta
                $opcionesSeleccionadas = array_map('intval', $opcionesSeleccionadas);
                
                // Filtrar valores inválidos (0 o negativos) que pueden resultar de inputs vacíos
                $opcionesSeleccionadas = array_filter($opcionesSeleccionadas, function($val) {
                    return $val > 0;
                });
                
                // Obtener las opciones correctas de esta pregunta
                $opcionesCorrectas = $preg->opciones()->where('es_correcta', true)->pluck('id')->toArray();
                
                // Verificar si la respuesta es correcta
                // Es correcta si seleccionó TODAS las correctas y NINGUNA incorrecta
                sort($opcionesSeleccionadas);
                sort($opcionesCorrectas);
                
                // Comparación flexible para arrays
                $esCorrecta = $opcionesSeleccionadas == $opcionesCorrectas;
                
                Log::info('Procesando respuesta', [
                    'id_pregunta' => $preg->id,
                    'opciones_seleccionadas' => $opcionesSeleccionadas,
                    'opciones_correctas' => $opcionesCorrectas,
                    'es_correcta' => $esCorrecta
                ]);
                
                if ($esCorrecta) $puntuacion++;

                // Guardar cada opción seleccionada como una respuesta
                foreach ($opcionesSeleccionadas as $optionId) {
                    respuesta::create([
                        'intento_id' => $intento->id,
                        'pregunta_id' => $preg->id,
                        'opcion_id' => $optionId,
                        'es_correcta' => $esCorrecta,
                    ]);
                }
            }
        }
        
        Log::info('Puntuación final', ['puntuacion' => $puntuacion, 'total' => $total]);

        $intento->puntuacion = $puntuacion;
        $intento->total = $total;
        $intento->save();

        // Solo actualizar calificación de materia si el examen está calificado (no tiene preguntas abiertas)
        if (!$tieneAbiertas) {
            $this->actualizarCalificacionMateria($alumnoId, $examen->materia_id);
        }

        $mensaje = $tieneAbiertas 
            ? 'Examen completado. Tu examen está en revisión por el docente.' 
            : 'Examen completado. Tu calificación ha sido registrada.';

        return redirect()->route('examenes.result', $intento)->with('success', $mensaje);
    }

    /**
     * Calcular promedio de la materia y actualizar calificación del alumno
     */
    private function actualizarCalificacionMateria($alumnoId, $materiaId)
    {
        Log::info("actualizarCalificacionMateria llamado", ['id_alumno' => $alumnoId, 'id_materia' => $materiaId]);
        
        // Obtener IDs de exámenes existentes en la materia
        $idsExamenes = examen::where('materia_id', $materiaId)->pluck('id');
        
        if ($idsExamenes->isEmpty()) {
            // Si no hay exámenes en la materia, calificación es NULL
            DB::table('alumno_materias')
                ->where('alumno_id', $alumnoId)
                ->where('materia_id', $materiaId)
                ->update(['calificacion' => null]);
            Log::info("No hay exámenes en la materia. La calificación se ha restablecido a NULL.");
            return;
        }

        // Obtener intentos válidos para esos exámenes (solo versión actual)
        $intentos = intentoExamen::whereIn('examen_id', $idsExamenes)
            ->where('alumno_id', $alumnoId)
            ->where('version_anterior', false) // Solo intentos de versión actual
            ->get();

        Log::info("Intentos encontrados", ['conteo' => $intentos->count()]);

        if ($intentos->isEmpty()) {
            // Si hay exámenes pero el alumno no tiene intentos, calificación NULL
            DB::table('alumno_materias')
                ->where('alumno_id', $alumnoId)
                ->where('materia_id', $materiaId)
                ->update(['calificacion' => null]);
                
            Log::info("No se encontraron intentos. Calificación restablecida a NULL");
            return;
        }

        // Calcular porcentaje promedio de todos los exámenes
        $porcentajeTotal = 0;
        $conteoExamenes = 0;

        foreach ($intentos as $intento) {
            if ($intento->total > 0) {
                $porcentaje = ($intento->puntuacion / $intento->total) * 100;
                $porcentajeTotal += $porcentaje;
                $conteoExamenes++;
            }
        }

        if ($conteoExamenes > 0) {
            $promedioCalificacion = round($porcentajeTotal / $conteoExamenes, 2);
            
            Log::info("Actualizando calificación", ['promedio' => $promedioCalificacion]);
            
            // Actualizar calificación en la tabla pivote
            $actualizado = DB::table('alumno_materias')
                ->where('alumno_id', $alumnoId)
                ->where('materia_id', $materiaId)
                ->update(['calificacion' => $promedioCalificacion]);
        } else {
             // Si tiene intentos pero ninguno válido para promedio (ej. total=0), resetear
             DB::table('alumno_materias')
                ->where('alumno_id', $alumnoId)
                ->where('materia_id', $materiaId)
                ->update(['calificacion' => null]);
        }
    }

    // Mostrar resultado de intento
    public function result(intentoExamen $intento)
    {
        $intento->load('respuestas.pregunta', 'respuestas.opcion');
        return view('examenes.result', compact('intento'));
    }

    // Calificar múltiples respuestas de un intento
    public function calificarIntentoMasivo(Request $request, intentoExamen $intento)
    {
        $request->validate([
            'calificaciones' => 'required|array',
            'calificaciones.*' => 'required|boolean'
        ]);

        foreach ($request->calificaciones as $respuestaId => $esCorrecta) {
            $respuesta = respuesta::find($respuestaId);
            if ($respuesta && $respuesta->intento_id == $intento->id) {
                $respuesta->update([
                    'es_correcta' => $esCorrecta,
                    'puntos_obtenidos' => $esCorrecta ? 1 : 0
                ]);
            }
        }

        // Verificar si todas las preguntas abiertas ya están calificadas
        $preguntasAbiertas = $intento->respuestas->filter(function($r) {
            return $r->pregunta->isAbierta();
        });

        $todasCalificadas = $preguntasAbiertas->every(function($r) {
            return $r->puntos_obtenidos !== null;
        });

        // Si todas están calificadas, publicar automáticamente
        if ($todasCalificadas) {
            // Calcular puntuación total - contar por PREGUNTA, no por respuesta
            $puntuacion = 0;
            $total = 0;

            // Recargar respuestas para asegurar datos actualizados
            $intento->load('respuestas.pregunta');

            // Agrupar respuestas por pregunta
            $respuestasPorPregunta = $intento->respuestas->groupBy('pregunta_id');
            
            foreach ($respuestasPorPregunta as $preguntaId => $respuestas) {
                $total++;
                /** @var \App\Models\respuesta $primeraRespuesta */
                $primeraRespuesta = $respuestas->first();
                
                if (!$primeraRespuesta->pregunta) {
                    Log::warning("Respuesta huérfana encontrada: ID {$primeraRespuesta->id}");
                    continue;
                }

                if ($primeraRespuesta->pregunta->isAbierta()) {
                    // Para preguntas abiertas, usar puntos_obtenidos
                    $puntuacion += $primeraRespuesta->puntos_obtenidos ?? 0;
                } else {
                    // Para preguntas de opción múltiple, verificar si es correcta
                    if ($primeraRespuesta->es_correcta) {
                        $puntuacion++;
                    }
                }
            }

            // Actualizar intento
            /** @var User $user */
            $user = Auth::user();
            $intento->update([
                'puntuacion' => $puntuacion,
                'total' => $total,
                'estado' => intentoExamen::ESTADO_CALIFICADO,
                'revisado_por' => $user?->docente?->id ?? null,
                'fecha_revision' => now()
            ]);

            // Actualizar calificación de la materia
            $this->actualizarCalificacionMateria($intento->alumno_id, $intento->examen->materia_id);

            return redirect()->route('examenes.show', $intento->examen_id)->with('success', 'Respuestas calificadas. El examen ha sido calificado.');
        }

        return redirect()->route('examenes.show', $intento->examen_id)->with('success', 'Respuestas guardadas correctamente.');
    }

    // Calificar una respuesta abierta (Obsoleto pero mantenido por compatibilidad)
    public function calificarRespuesta(Request $request, respuesta $respuesta)
    {
        $request->validate([
            'es_correcta' => 'required|boolean'
        ]);

        $respuesta->update([
            'es_correcta' => $request->es_correcta,
            'puntos_obtenidos' => $request->es_correcta ? 1 : 0
        ]);

        // Verificar si todas las preguntas abiertas ya están calificadas
        $intento = $respuesta->intento;
        $preguntasAbiertas = $intento->respuestas->filter(function($r) {
            return $r->pregunta->isAbierta();
        });

        $todasCalificadas = $preguntasAbiertas->every(function($r) {
            return $r->puntos_obtenidos !== null;
        });

        // Si todas están calificadas, publicar automáticamente
        if ($todasCalificadas && $intento->isEnRevision()) {
            // Calcular puntuación total - contar por PREGUNTA, no por respuesta
            $puntuacion = 0;
            $total = 0;

            // Agrupar respuestas por pregunta
            $respuestasPorPregunta = $intento->respuestas->groupBy('pregunta_id');
            
            foreach ($respuestasPorPregunta as $preguntaId => $respuestas) {
                $total++;
                $primeraRespuesta = $respuestas->first();
                
                if ($primeraRespuesta->pregunta->isAbierta()) {
                    // Para preguntas abiertas, usar puntos_obtenidos
                    $puntuacion += $primeraRespuesta->puntos_obtenidos ?? 0;
                } else {
                    // Para preguntas de opción múltiple, verificar si es correcta
                    // Solo necesitamos verificar una respuesta ya que todas tienen el mismo valor es_correcta
                    if ($primeraRespuesta->es_correcta) {
                        $puntuacion++;
                    }
                }
            }

            // Actualizar intento
            /** @var User $user */
            $user = Auth::user();
            $intento->update([
                'puntuacion' => $puntuacion,
                'total' => $total,
                'estado' => intentoExamen::ESTADO_CALIFICADO,
                'revisado_por' => $user?->docente?->id ?? null,
                'fecha_revision' => now()
            ]);

            // Actualizar calificación de la materia
            $this->actualizarCalificacionMateria($intento->alumno_id, $intento->examen->materia_id);

            return redirect()->route('examenes.show', $intento->examen_id)->with('success', 'Respuesta calificada. El examen ha sido calificado automáticamente.');
        }

        return redirect()->route('examenes.show', $respuesta->intento->examen_id)->with('success', 'Respuesta calificada correctamente.');
    }

    // Publicar calificación final
    public function publicarCalificacion(intentoExamen $intento)
    {
        // Calcular puntuación total - contar por PREGUNTA, no por respuesta
        $puntuacion = 0;
        $total = 0;

        // Agrupar respuestas por pregunta
        $respuestasPorPregunta = $intento->respuestas->groupBy('pregunta_id');
        
        foreach ($respuestasPorPregunta as $preguntaId => $respuestas) {
            $total++;
            $primeraRespuesta = $respuestas->first();
            
            if ($primeraRespuesta->pregunta->isAbierta()) {
                // Para preguntas abiertas, usar puntos_obtenidos
                $puntuacion += $primeraRespuesta->puntos_obtenidos ?? 0;
            } else {
                // Para preguntas de opción múltiple, verificar si es correcta
                // Solo necesitamos verificar una respuesta ya que todas tienen el mismo valor es_correcta
                if ($primeraRespuesta->es_correcta) {
                    $puntuacion++;
                }
            }
        }

        // Actualizar intento
        /** @var User $user */
        $user = Auth::user();
        $intento->update([
            'puntuacion' => $puntuacion,
            'total' => $total,
            'estado' => intentoExamen::ESTADO_CALIFICADO,
            'revisado_por' => $user?->docente?->id ?? null,
            'fecha_revision' => now()
        ]);

        // Actualizar calificación de la materia
        $this->actualizarCalificacionMateria($intento->alumno_id, $intento->examen->materia_id);

        return redirect()->back()->with('success', 'Calificación publicada correctamente.');
    }
}
