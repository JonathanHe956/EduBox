<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Models\Pregunta;
use App\Models\Opcion;
use App\Models\IntentoExamen;
use App\Models\Respuesta;
use App\Models\materia;
use App\Models\docente;
use App\Models\alumno;
use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\StoreQuestionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExamenController extends Controller
{
    // Formulario para crear un examen dentro de una materia (docente)
    public function createForMateria(materia $materia)
    {
        return view('examenes.create', compact('materia'));
    }

    // Guardar examen básico
    public function store(StoreExamRequest $request, materia $materia)
    {
        $docente = auth()->user()->docente;
        if (!$docente) {
            $docente = docente::where('email', auth()->user()->email)->first();
        }
        $docenteId = $docente?->id;

        DB::transaction(function () use ($request, $materia, $docenteId) {
            $examen = Examen::create([
                'materia_id' => $materia->id,
                'docente_id' => $docenteId,
                'titulo' => $request->input('title'),
                'descripcion' => $request->input('description'),
                'cantidad_preguntas' => 0,
                'options_per_question' => 4,
                'correct_answers' => 1,
            ]);

            if ($request->has('questions')) {
                foreach ($request->input('questions') as $qIndex => $qData) {
                    $tipo = $qData['tipo'] ?? 'multiple';
                    
                    // Validar según el tipo de pregunta
                    if ($tipo === 'multiple') {
                        if (!isset($qData['options']) || count($qData['options']) < 2) {
                            throw new \Exception("La pregunta " . ($qIndex + 1) . " debe tener al menos 2 opciones.");
                        }
                        
                        $hasCorrectAnswer = false;
                        foreach ($qData['options'] as $optData) {
                            if (isset($optData['is_correct'])) {
                                $hasCorrectAnswer = true;
                                break;
                            }
                        }
                        
                        if (!$hasCorrectAnswer) {
                            throw new \Exception("La pregunta " . ($qIndex + 1) . " debe tener al menos una respuesta correcta.");
                        }
                    } elseif ($tipo === 'verdadero_falso') {
                        if (!isset($qData['vf_correcta'])) {
                            throw new \Exception("La pregunta " . ($qIndex + 1) . " debe tener una respuesta correcta seleccionada.");
                        }
                    }

                    // Crear la pregunta
                    $pregunta = Pregunta::create([
                        'examen_id' => $examen->id,
                        'pregunta' => $qData['text'],
                        'tipo' => $tipo,
                        'respuesta_correcta_abierta' => $qData['respuesta_esperada'] ?? null,
                    ]);

                    // Crear opciones según el tipo
                    if ($tipo === 'multiple' && isset($qData['options'])) {
                        foreach ($qData['options'] as $optData) {
                            Opcion::create([
                                'pregunta_id' => $pregunta->id,
                                'opcion' => $optData['text'],
                                'es_correcta' => isset($optData['is_correct']),
                            ]);
                        }
                    } elseif ($tipo === 'verdadero_falso') {
                        // Crear automáticamente las opciones Verdadero/Falso
                        Opcion::create([
                            'pregunta_id' => $pregunta->id,
                            'opcion' => 'Verdadero',
                            'es_correcta' => $qData['vf_correcta'] === 'verdadero',
                        ]);
                        Opcion::create([
                            'pregunta_id' => $pregunta->id,
                            'opcion' => 'Falso',
                            'es_correcta' => $qData['vf_correcta'] === 'falso',
                        ]);
                    }
                    // Las preguntas abiertas no tienen opciones
                }
                $examen->cantidad_preguntas = count($request->input('questions'));
                $examen->save();
            }
        });

        return redirect()->route('examenes.materia', $materia)->with('success', 'Examen creado exitosamente.');
    }

    // Añadir pregunta y sus opciones a un examen
    public function addQuestion(StoreQuestionRequest $request, Examen $examen)
    {
        // Obsoleto por formulario dinámico, mantenido por compatibilidad
        return back();
    }

    // Lista de exámenes pendientes para el alumno (no intentados aún)
    public function alumnoIndex()
    {
        $alumno = auth()->user()->alumno;
        if (! $alumno) {
            abort(403);
        }

        $materiaIds = $alumno->materias()->pluck('materias.id');

        $examenes = Examen::whereIn('materia_id', $materiaIds)
            ->with(['intentos' => function ($q) use ($alumno) {
                $q->where('alumno_id', $alumno->id);
            }])
            ->get();

        return view('examenes.index_alumno', ['examenes' => $examenes]);
    }

    // Lista de exámenes del docente
    public function docenteIndex()
    {
        $docente = auth()->user()->docente;
        if (!$docente) {
            $docente = docente::where('email', auth()->user()->email)->first();
        }
        if (! $docente) abort(403);

        $examenes = Examen::where('docente_id', $docente->id)->withCount('preguntas')->get();
        return view('examenes.index_docente', compact('examenes'));
    }

    // Lista de exámenes por materia (docente)
    public function indexForMateria(materia $materia)
    {
        $docente = auth()->user()->docente;
        if (!$docente) {
            $docente = docente::where('email', auth()->user()->email)->first();
        }
        if (! $docente) abort(403);

        // Verificar que la materia pertenece al docente (opcional pero recomendado)
        // if ($materia->docente_id !== $docente->id) abort(403);

        $examenes = Examen::where('materia_id', $materia->id)
            ->where('docente_id', $docente->id)
            ->withCount('preguntas')
            ->get();

        return view('examenes.index_materia', compact('examenes', 'materia'));
    }

    // Lista de exámenes por materia (alumno)
    public function indexForMateriaAlumno(materia $materia)
    {
        $alumno = auth()->user()->alumno;
        if (!$alumno) {
            $alumno = alumno::where('email', auth()->user()->email)->first();
        }
        if (! $alumno) abort(403);

        // Verificar que el alumno está inscrito en la materia
        $isEnrolled = $alumno->materias()->where('materias.id', $materia->id)->exists();
        if (!$isEnrolled) abort(403, 'No estás inscrito en esta materia');

        // Obtener exámenes de la materia con información de intentos del alumno
        $examenes = Examen::where('materia_id', $materia->id)
            ->withCount('preguntas')
            ->with(['intentos' => function($q) use ($alumno) {
                $q->where('alumno_id', $alumno->id)
                  ->where('version_anterior', false); // Solo intentos de versión actual
            }])
            ->get();

        return view('examenes.index_materia_alumno', compact('examenes', 'materia'));
    }

    // Editar examen (form)
    public function edit(Examen $examen)
    {
        $docente = auth()->user()->docente;
        if (!$docente) {
            $docente = docente::where('email', auth()->user()->email)->first();
        }
        if (! $docente || $examen->docente_id !== $docente->id) abort(403);

        $examen->load('preguntas.opciones');
        return view('examenes.edit', compact('examen'));
    }

    // Actualizar examen
    public function update(Request $request, Examen $examen)
    {
        $docente = auth()->user()->docente;
        if (!$docente) {
            $docente = docente::where('email', auth()->user()->email)->first();
        }
        if (! $docente || $examen->docente_id !== $docente->id) abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'questions' => 'array',
            'questions.*.text' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($request, $examen) {
                // Actualizar título y descripción
                $examen->update([
                    'titulo' => $request->input('title'),
                    'descripcion' => $request->input('description'),
                ]);

                // Sincronizar preguntas
                $incomingQuestionIds = [];
                if ($request->has('questions')) {
                    foreach ($request->input('questions') as $index => $qData) {
                        $tipo = $qData['tipo'] ?? 'multiple';
                        
                        // Validar según el tipo de pregunta
                        if ($tipo === 'multiple') {
                            if (!isset($qData['options']) || count($qData['options']) < 2) {
                                throw new \Exception("La pregunta " . ($index + 1) . " debe tener al menos 2 opciones.");
                            }
                            
                            $hasCorrectAnswer = false;
                            foreach ($qData['options'] as $optData) {
                                if (isset($optData['is_correct'])) {
                                    $hasCorrectAnswer = true;
                                    break;
                                }
                            }
                            
                            if (!$hasCorrectAnswer) {
                                throw new \Exception("La pregunta " . ($index + 1) . " debe tener al menos una respuesta correcta.");
                            }
                        } elseif ($tipo === 'verdadero_falso') {
                            if (!isset($qData['vf_correcta'])) {
                                throw new \Exception("La pregunta " . ($index + 1) . " debe tener una respuesta correcta seleccionada.");
                            }
                        }

                        if (isset($qData['id'])) {
                            // Actualizar pregunta existente
                            $incomingQuestionIds[] = $qData['id'];
                            $pregunta = Pregunta::find($qData['id']);
                            
                            if ($pregunta && $pregunta->examen_id == $examen->id) {
                                $pregunta->update([
                                    'pregunta' => $qData['text'],
                                    'tipo' => $tipo,
                                    'respuesta_correcta_abierta' => $qData['respuesta_esperada'] ?? null,
                                ]);
                                
                                // Eliminar opciones antiguas y crear nuevas
                                $pregunta->opciones()->delete();
                                
                                if ($tipo === 'multiple' && isset($qData['options'])) {
                                    foreach ($qData['options'] as $optData) {
                                        Opcion::create([
                                            'pregunta_id' => $pregunta->id,
                                            'opcion' => $optData['text'],
                                            'es_correcta' => isset($optData['is_correct']),
                                        ]);
                                    }
                                } elseif ($tipo === 'verdadero_falso') {
                                    Opcion::create([
                                        'pregunta_id' => $pregunta->id,
                                        'opcion' => 'Verdadero',
                                        'es_correcta' => $qData['vf_correcta'] === 'verdadero',
                                    ]);
                                    Opcion::create([
                                        'pregunta_id' => $pregunta->id,
                                        'opcion' => 'Falso',
                                        'es_correcta' => $qData['vf_correcta'] === 'falso',
                                    ]);
                                }
                            }
                        } else {
                            // Nueva pregunta
                            $pregunta = Pregunta::create([
                                'examen_id' => $examen->id,
                                'pregunta' => $qData['text'],
                                'tipo' => $tipo,
                                'respuesta_correcta_abierta' => $qData['respuesta_esperada'] ?? null,
                            ]);
                            $incomingQuestionIds[] = $pregunta->id;
                            
                            if ($tipo === 'multiple' && isset($qData['options'])) {
                                foreach ($qData['options'] as $optData) {
                                    Opcion::create([
                                        'pregunta_id' => $pregunta->id,
                                        'opcion' => $optData['text'],
                                        'es_correcta' => isset($optData['is_correct']),
                                    ]);
                                }
                            } elseif ($tipo === 'verdadero_falso') {
                                Opcion::create([
                                    'pregunta_id' => $pregunta->id,
                                    'opcion' => 'Verdadero',
                                    'es_correcta' => $qData['vf_correcta'] === 'verdadero',
                                ]);
                                Opcion::create([
                                    'pregunta_id' => $pregunta->id,
                                    'opcion' => 'Falso',
                                    'es_correcta' => $qData['vf_correcta'] === 'falso',
                                ]);
                            }
                        }
                    }
                }

                // Eliminar preguntas removidas
                $examen->preguntas()->whereNotIn('id', $incomingQuestionIds)->delete();

                // Actualizar contador
                $examen->cantidad_preguntas = $examen->preguntas()->count();
                $examen->save();
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar el examen: ' . $e->getMessage()]);
        }

        // Marcar intentos anteriores como versión anterior
        $intentosAnteriores = IntentoExamen::where('examen_id', $examen->id)
            ->where('version_anterior', false)
            ->get();

        if ($intentosAnteriores->isNotEmpty()) {
            // Marcar todos los intentos existentes como versión anterior
            IntentoExamen::where('examen_id', $examen->id)
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
        $examen = Examen::find($id);

        if (!$examen) {
            return redirect()->back()->with('error', 'El examen no existe o ya fue eliminado.');
        }

        $docente = auth()->user()->docente;
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
                $this->updateSubjectGrade($alumnoId, $materiaId);
            }
        });

        return redirect()->route('examenes.materia', $materiaId)->with('success', 'Examen eliminado y calificaciones actualizadas.');
    }

    // Mostrar examen (para docente o alumno)
    public function show(Examen $examen)
    {
        $examen->load('preguntas.opciones');
        return view('examenes.show', compact('examen'));
    }

    // Alumno realiza intento
    public function attempt(Request $request, Examen $examen)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        $alumno = auth()->user()->alumno;
        if (!$alumno) {
            $alumno = \App\Models\Alumno::where('email', auth()->user()->email)->first();
        }
        if (!$alumno) abort(403, 'No se encontró el registro de alumno');

        $alumnoId = $alumno->id;

        $exists = IntentoExamen::where('examen_id', $examen->id)
            ->where('alumno_id', $alumnoId)
            ->where('version_anterior', false)
            ->exists();
        if ($exists) {
            return redirect()->route('examenes.show', $examen)->withErrors(['already' => 'Ya has realizado este examen. Solo se permite un intento.']);
        }

        $answers = $request->input('answers', []);
        if (count($answers) !== $examen->preguntas->count()) {
            return redirect()->route('examenes.show', $examen)->withErrors(['incomplete' => 'Debes responder todas las preguntas antes de enviar el examen.']);
        }

        $tieneAbiertas = $examen->preguntas()->where('tipo', Pregunta::TIPO_ABIERTA)->exists();
        $estadoInicial = $tieneAbiertas ? IntentoExamen::ESTADO_EN_REVISION : IntentoExamen::ESTADO_CALIFICADO;

        $intento = IntentoExamen::create([
            'examen_id' => $examen->id,
            'alumno_id' => $alumnoId,
            'puntuacion' => 0,
            'total' => 0,
            'estado' => $estadoInicial,
        ]);

        $score = 0;
        $total = 0;
        
        \Log::info('Starting score calculation', ['answers' => $answers]);
        
        // Iterar sobre TODAS las preguntas del examen
        foreach ($examen->preguntas as $preg) {
            $total++;
            $answer = $answers[$preg->id] ?? null;
            
            // Manejar según el tipo de pregunta
            if ($preg->isAbierta()) {
                // Pregunta abierta - guardar texto de respuesta
                Respuesta::create([
                    'intento_id' => $intento->id,
                    'pregunta_id' => $preg->id,
                    'opcion_id' => null,
                    'respuesta_abierta' => $answer,
                    'es_correcta' => 0, // Se marcará como correcta cuando el docente califique
                    'puntos_obtenidos' => null, // Pendiente de calificación
                ]);
            } else {
                // Pregunta de opción múltiple o verdadero/falso
                // Ahora puede ser un array de opciones
                $selectedOptions = is_array($answer) ? $answer : [$answer];
                
                // Convertir a enteros para asegurar comparación correcta
                $selectedOptions = array_map('intval', $selectedOptions);
                
                // Obtener las opciones correctas de esta pregunta
                $correctOptions = $preg->opciones()->where('es_correcta', true)->pluck('id')->toArray();
                
                // Verificar si la respuesta es correcta
                // Es correcta si seleccionó TODAS las correctas y NINGUNA incorrecta
                sort($selectedOptions);
                sort($correctOptions);
                
                // Comparación flexible para arrays
                $isCorrect = $selectedOptions == $correctOptions;
                
                \Log::info('Processing answer', [
                    'question_id' => $preg->id,
                    'selected_options' => $selectedOptions,
                    'correct_options' => $correctOptions,
                    'isCorrect' => $isCorrect
                ]);
                
                if ($isCorrect) $score++;

                // Guardar cada opción seleccionada como una respuesta
                foreach ($selectedOptions as $optionId) {
                    Respuesta::create([
                        'intento_id' => $intento->id,
                        'pregunta_id' => $preg->id,
                        'opcion_id' => $optionId,
                        'es_correcta' => $isCorrect,
                    ]);
                }
            }
        }
        
        \Log::info('Final score', ['score' => $score, 'total' => $total]);

        $intento->puntuacion = $score;
        $intento->total = $total;
        $intento->save();

        // Solo actualizar calificación de materia si el examen está calificado (no tiene preguntas abiertas)
        if (!$tieneAbiertas) {
            $this->updateSubjectGrade($alumnoId, $examen->materia_id);
        }

        $mensaje = $tieneAbiertas 
            ? 'Examen completado. Tu examen está en revisión por el docente.' 
            : 'Examen completado. Tu calificación ha sido registrada.';

        return redirect()->route('examenes.result', $intento)->with('success', $mensaje);
    }

    /**
     * Calcular promedio de la materia y actualizar calificación del alumno
     */
    private function updateSubjectGrade($alumnoId, $materiaId)
    {
        \Log::info("updateSubjectGrade called", ['alumno_id' => $alumnoId, 'materia_id' => $materiaId]);
        
        // Obtener IDs de exámenes existentes en la materia
        $examIds = Examen::where('materia_id', $materiaId)->pluck('id');
        
        if ($examIds->isEmpty()) {
            // Si no hay exámenes en la materia, calificación es NULL
            DB::table('alumno_materias')
                ->where('alumno_id', $alumnoId)
                ->where('materia_id', $materiaId)
                ->update(['calificacion' => null]);
            \Log::info("No exams in subject. Grade reset to NULL.");
            return;
        }

        // Obtener intentos válidos para esos exámenes (solo versión actual)
        $attempts = IntentoExamen::whereIn('examen_id', $examIds)
            ->where('alumno_id', $alumnoId)
            ->where('version_anterior', false) // Solo intentos de versión actual
            ->get();

        \Log::info("Attempts found", ['count' => $attempts->count()]);

        if ($attempts->isEmpty()) {
            // Si hay exámenes pero el alumno no tiene intentos, calificación NULL
            DB::table('alumno_materias')
                ->where('alumno_id', $alumnoId)
                ->where('materia_id', $materiaId)
                ->update(['calificacion' => null]);
                
            \Log::info("No attempts found. Grade reset to NULL");
            return;
        }

        // Calcular porcentaje promedio de todos los exámenes
        $totalPercentage = 0;
        $examCount = 0;

        foreach ($attempts as $attempt) {
            if ($attempt->total > 0) {
                $percentage = ($attempt->puntuacion / $attempt->total) * 100;
                $totalPercentage += $percentage;
                $examCount++;
            }
        }

        if ($examCount > 0) {
            $averageGrade = round($totalPercentage / $examCount, 2);
            
            \Log::info("Updating grade", ['average' => $averageGrade]);
            
            // Actualizar calificación en la tabla pivote
            $updated = DB::table('alumno_materias')
                ->where('alumno_id', $alumnoId)
                ->where('materia_id', $materiaId)
                ->update(['calificacion' => $averageGrade]);
        } else {
             // Si tiene intentos pero ninguno válido para promedio (ej. total=0), resetear
             DB::table('alumno_materias')
                ->where('alumno_id', $alumnoId)
                ->where('materia_id', $materiaId)
                ->update(['calificacion' => null]);
        }
    }

    // Mostrar resultado de intento
    public function result(IntentoExamen $intento)
    {
        $intento->load('respuestas.pregunta', 'respuestas.opcion');
        return view('examenes.result', compact('intento'));
    }

    // Calificar una respuesta abierta
    public function gradeAnswer(Request $request, Respuesta $respuesta)
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
            $score = 0;
            $total = 0;

            // Agrupar respuestas por pregunta
            $respuestasPorPregunta = $intento->respuestas->groupBy('pregunta_id');
            
            foreach ($respuestasPorPregunta as $preguntaId => $respuestas) {
                $total++;
                $primeraRespuesta = $respuestas->first();
                
                if ($primeraRespuesta->pregunta->isAbierta()) {
                    // Para preguntas abiertas, usar puntos_obtenidos
                    $score += $primeraRespuesta->puntos_obtenidos ?? 0;
                } else {
                    // Para preguntas de opción múltiple, verificar si es correcta
                    // Solo necesitamos verificar una respuesta ya que todas tienen el mismo valor es_correcta
                    if ($primeraRespuesta->es_correcta) {
                        $score++;
                    }
                }
            }

            // Actualizar intento
            $intento->update([
                'puntuacion' => $score,
                'total' => $total,
                'estado' => IntentoExamen::ESTADO_CALIFICADO,
                'revisado_por' => auth()->user()->docente->id ?? null,
                'fecha_revision' => now()
            ]);

            // Actualizar calificación de la materia
            $this->updateSubjectGrade($intento->alumno_id, $intento->examen->materia_id);

            return redirect()->route('examenes.show', $intento->examen_id)->with('success', 'Respuesta calificada. El examen ha sido calificado automáticamente.');
        }

        return redirect()->route('examenes.show', $respuesta->intento->examen_id)->with('success', 'Respuesta calificada correctamente.');
    }

    // Publicar calificación final
    public function publishGrade(IntentoExamen $intento)
    {
        // Calcular puntuación total - contar por PREGUNTA, no por respuesta
        $score = 0;
        $total = 0;

        // Agrupar respuestas por pregunta
        $respuestasPorPregunta = $intento->respuestas->groupBy('pregunta_id');
        
        foreach ($respuestasPorPregunta as $preguntaId => $respuestas) {
            $total++;
            $primeraRespuesta = $respuestas->first();
            
            if ($primeraRespuesta->pregunta->isAbierta()) {
                // Para preguntas abiertas, usar puntos_obtenidos
                $score += $primeraRespuesta->puntos_obtenidos ?? 0;
            } else {
                // Para preguntas de opción múltiple, verificar si es correcta
                // Solo necesitamos verificar una respuesta ya que todas tienen el mismo valor es_correcta
                if ($primeraRespuesta->es_correcta) {
                    $score++;
                }
            }
        }

        // Actualizar intento
        $intento->update([
            'puntuacion' => $score,
            'total' => $total,
            'estado' => IntentoExamen::ESTADO_CALIFICADO,
            'revisado_por' => auth()->user()->docente->id ?? null,
            'fecha_revision' => now()
        ]);

        // Actualizar calificación de la materia
        $this->updateMateriaGrade($intento->alumno_id, $intento->examen->materia_id);

        return redirect()->back()->with('success', 'Calificación publicada correctamente.');
    }
}
