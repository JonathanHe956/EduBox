<x-layouts.app :title="$examen->titulo">
    <div class="px-4 py-6 max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-blue-900 dark:text-white">{{ $examen->titulo }}</h1>
            @if($examen->descripcion)
                <p class="mt-1 text-sm text-blue-700 dark:text-blue-200">{{ $examen->descripcion }}</p>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700 dark:bg-green-900/30 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        @if($examen->preguntas->isEmpty())
            <div class="rounded-lg border border-gray-200 bg-white p-8 text-center dark:border-gray-700 dark:bg-zinc-900">
                <p class="text-gray-500 dark:text-gray-400">Este examen aún no tiene preguntas.</p>
            </div>
        @else
            @auth
                @php
                    $isStudent = auth()->user()->hasRole('estudiante');
                    $isTeacher = auth()->user()->hasRole('docente');
                    $hasAttempted = false;
                    
                    if ($isStudent) {
                        $alumno = auth()->user()->alumno ?? \App\Models\Alumno::where('email', auth()->user()->email)->first();
                        $hasAttempted = $alumno && $examen->intentos()
                            ->where('alumno_id', $alumno->id)
                            ->where('version_anterior', false) // Solo verificar intentos de versión actual
                            ->exists();
                    }
                @endphp

                @if($isStudent && !$hasAttempted)
                    {{-- Student exam form --}}
                    <form method="POST" action="{{ route('exams.attempt', $examen) }}" class="space-y-6">
                        @csrf
                        @foreach($examen->preguntas as $index => $pregunta)
                            <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-zinc-900">
                                <h3 class="mb-4 text-lg font-medium text-blue-900 dark:text-white">
                                    {{ $index + 1 }}. {{ $pregunta->pregunta }}
                                </h3>
                                
                                @if($pregunta->isAbierta())
                                    {{-- Open-ended question --}}
                                    <div>
                                        <label for="answer-{{ $pregunta->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Tu respuesta:
                                        </label>
                                        <textarea 
                                            id="answer-{{ $pregunta->id }}"
                                            name="answers[{{ $pregunta->id }}]" 
                                            rows="5" 
                                            required
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white"
                                            placeholder="Escribe tu respuesta aquí..."
                                        ></textarea>
                                    </div>
                                @else
                                    {{-- Multiple choice or True/False --}}
                                    <div class="space-y-2">
                                        @if($pregunta->tipo === 'verdadero_falso')
                                            {{-- True/False: Radio buttons (Single selection) --}}
                                            @foreach($pregunta->opciones as $opcion)
                                                <label class="flex items-center gap-3 rounded-lg border border-gray-200 p-3 cursor-pointer hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-zinc-800">
                                                    <input 
                                                        type="radio" 
                                                        name="answers[{{ $pregunta->id }}]" 
                                                        value="{{ $opcion->id }}" 
                                                        class="w-4 h-4 text-indigo-600 dark:bg-zinc-700 dark:border-gray-600 focus:ring-indigo-500" 
                                                        required
                                                    >
                                                    <span class="text-sm text-blue-900 dark:text-white">{{ $opcion->opcion }}</span>
                                                </label>
                                            @endforeach
                                        @else
                                            {{-- Multiple Choice: Checkboxes (Multiple selection) --}}
                                            @foreach($pregunta->opciones as $opcion)
                                                <label class="flex items-center gap-3 rounded-lg border border-gray-200 p-3 cursor-pointer hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-zinc-800">
                                                    <input 
                                                        type="checkbox" 
                                                        name="answers[{{ $pregunta->id }}][]" 
                                                        value="{{ $opcion->id }}" 
                                                        class="w-4 h-4 text-indigo-600 dark:bg-zinc-700 dark:border-gray-600 focus:ring-indigo-500 answer-checkbox-{{ $pregunta->id }}" 
                                                        onchange="limitCheckboxes({{ $pregunta->id }})"
                                                    >
                                                    <span class="text-sm text-blue-900 dark:text-white">{{ $opcion->opcion }}</span>
                                                </label>
                                            @endforeach
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Puedes seleccionar varias opciones</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('exams.materia.alumno', $examen->materia_id) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-zinc-800">
                                Cancelar
                            </a>
                            <button type="submit" class="rounded-lg bg-green-600 px-6 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" onclick="return validateForm()">
                                Enviar respuestas
                            </button>
                        </div>

                        <script>
                            function limitCheckboxes(questionId) {
                                const checkboxes = document.querySelectorAll('.answer-checkbox-' + questionId);
                                const checked = Array.from(checkboxes).filter(cb => cb.checked);
                                
                                if (checked.length > 4) {
                                    // Desmarcar la última seleccionada
                                    event.target.checked = false;
                                    alert('Solo puedes seleccionar hasta 4 opciones por pregunta.');
                                }
                            }

                            function validateForm() {
                                // Validar que todas las preguntas de opción múltiple y verdadero/falso tengan respuesta
                                const questionGroups = {};
                                
                                // Seleccionar todos los inputs de respuesta (radio y checkbox)
                                const inputs = document.querySelectorAll('input[name^="answers"]');
                                
                                inputs.forEach(input => {
                                    // Extraer el ID de la pregunta del nombre: answers[123] o answers[123][]
                                    const match = input.name.match(/answers\[(\d+)\]/);
                                    if (match) {
                                        const questionId = match[1];
                                        if (!questionGroups[questionId]) {
                                            questionGroups[questionId] = false;
                                        }
                                        if (input.checked) {
                                            questionGroups[questionId] = true;
                                        }
                                    }
                                });

                                // Verificar si alguna pregunta no tiene respuesta
                                for (const questionId in questionGroups) {
                                    if (!questionGroups[questionId]) {
                                        alert('Debes responder todas las preguntas antes de enviar el examen.');
                                        return false;
                                    }
                                }

                                return true;
                            }
                        </script>
                    </form>
                @elseif($isStudent && $hasAttempted)
                    {{-- Student has already attempted --}}
                    <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-6 dark:border-yellow-900/30 dark:bg-yellow-900/20">
                        <p class="text-yellow-800 dark:text-yellow-400">Ya has completado este examen. Puedes ver tu resultado en la lista de exámenes.</p>
                        <a href="{{ route('exams.materia.alumno', $examen->materia_id) }}" class="mt-4 inline-block text-sm font-medium text-yellow-900 hover:text-yellow-700 dark:text-yellow-300">
                            Volver a exámenes →
                        </a>
                    </div>
                @elseif($isTeacher)
                    {{-- Teacher view - show questions and student attempts --}}
                    
                    {{-- Questions Preview --}}
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-blue-900 dark:text-white mb-4">Preguntas del Examen</h2>
                        <div class="space-y-4">
                            @foreach($examen->preguntas as $index => $pregunta)
                                <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-zinc-900">
                                    <h3 class="mb-3 text-lg font-medium text-blue-900 dark:text-white">
                                        {{ $index + 1 }}. {{ $pregunta->pregunta }}
                                        <span class="ml-2 text-sm font-normal text-gray-500">
                                            ({{ ucfirst(str_replace('_', ' ', $pregunta->tipo)) }})
                                        </span>
                                    </h3>
                                    
                                    @if($pregunta->isAbierta())
                                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                            <strong>Respuesta esperada:</strong> {{ $pregunta->respuesta_correcta_abierta ?? 'No especificada' }}
                                        </div>
                                    @else
                                        <ul class="space-y-2">
                                            @foreach($pregunta->opciones as $opcion)
                                                <li class="flex items-center gap-2 text-sm">
                                                    <span class="text-gray-700 dark:text-gray-300">{{ $opcion->opcion }}</span>
                                                    @if($opcion->es_correcta)
                                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                            Correcta
                                                        </span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Student Attempts --}}
                    @php
                        $intentos = $examen->intentos()
                            ->where('version_anterior', false) // Solo mostrar intentos de versión actual
                            ->with([
                                'alumno', 
                                'respuestas' => function($query) {
                                    $query->with(['pregunta', 'opcion']);
                                }
                            ])
                            ->get();
                    @endphp
                    
                    <div>
                        <h2 class="text-xl font-semibold text-blue-900 dark:text-white mb-4">Intentos de Estudiantes</h2>
                        
                        @if($intentos->isEmpty())
                            <div class="rounded-lg border border-gray-200 bg-white p-8 text-center dark:border-gray-700 dark:bg-zinc-900">
                                <p class="text-gray-500 dark:text-gray-400">Aún no hay estudiantes que hayan intentado este examen.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($intentos as $intento)
                                    <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-zinc-900">
                                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <h3 class="font-medium text-blue-900 dark:text-white">
                                                        {{ $intento->alumno->nombre ?? 'Estudiante' }}
                                                    </h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $intento->created_at->format('d/m/Y H:i') }}
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    @if($intento->isEnRevision())
                                                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                            En Revisión
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                            Calificado
                                                        </span>
                                                        <div class="mt-1 text-lg font-semibold text-blue-900 dark:text-white">
                                                            {{ $intento->puntuacion == floor($intento->puntuacion) ? number_format($intento->puntuacion, 0) : number_format($intento->puntuacion, 1) }}/{{ $intento->total }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="p-4">
                                            <button 
                                                type="button" 
                                                onclick="toggleAttempt({{ $intento->id }})" 
                                                class="text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400"
                                                aria-expanded="false" 
                                                aria-controls="attempt-{{ $intento->id }}"
                                            >
                                                Ver / Ocultar respuestas →
                                            </button>
                                            
                                            <div id="attempt-{{ $intento->id }}" class="hidden mt-4 space-y-4">
                                                @foreach($examen->preguntas as $pregunta)
                                                    @php
                                                        $respuestas = $intento->respuestas->where('pregunta_id', $pregunta->id);
                                                        $respuesta = $respuestas->first();
                                                    @endphp
                                                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-600 dark:bg-zinc-800">
                                                        <h4 class="font-medium text-blue-900 dark:text-white mb-2">
                                                            {{ $pregunta->pregunta }}
                                                        </h4>
                                                        
                                                        @if($pregunta->isAbierta())
                                                            {{-- Open-ended question --}}
                                                            <div class="mb-3">
                                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Respuesta del estudiante:</p>
                                                                <div class="bg-white dark:bg-zinc-900 p-3 rounded border border-gray-200 dark:border-gray-700">
                                                                    {{ $respuesta->respuesta_abierta ?? 'No respondida' }}
                                                                </div>
                                                            </div>
                                                            
                                                            @if($respuesta && $intento->isEnRevision())
                                                                <form action="{{ route('exams.grade-answer', $respuesta) }}" method="POST" class="mt-3">
                                                                    @csrf
                                                                    <div class="space-y-3">
                                                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                                            Calificación:
                                                                        </p>
                                                                        <div class="flex gap-4">
                                                                            <label class="flex items-center gap-2 cursor-pointer">
                                                                                <input 
                                                                                    type="radio" 
                                                                                    name="es_correcta" 
                                                                                    value="1" 
                                                                                    class="w-4 h-4 text-green-600 focus:ring-green-500" 
                                                                                    required
                                                                                >
                                                                                <span class="text-sm text-gray-700 dark:text-gray-300">✓ Correcta</span>
                                                                            </label>
                                                                            <label class="flex items-center gap-2 cursor-pointer">
                                                                                <input 
                                                                                    type="radio" 
                                                                                    name="es_correcta" 
                                                                                    value="0" 
                                                                                    class="w-4 h-4 text-red-600 focus:ring-red-500" 
                                                                                    required
                                                                                >
                                                                                <span class="text-sm text-gray-700 dark:text-gray-300">✗ Incorrecta</span>
                                                                            </label>
                                                                        </div>
                                                                        <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                                                            Guardar Calificación
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            @elseif(!$respuesta && $intento->isEnRevision())
                                                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400 italic">
                                                                    El estudiante no respondió esta pregunta. Se calificará automáticamente como incorrecta (0 puntos).
                                                                </div>
                                                            @elseif($respuesta)
                                                                <div class="mt-2">
                                                                    @if($respuesta->es_correcta)
                                                                        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                                            ✓ Correcta
                                                                        </span>
                                                                    @else
                                                                        <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                                            ✗ Incorrecta
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        @else
                                                            {{-- Multiple choice or True/False --}}
                                                            <div class="text-sm">
                                                                <p class="text-gray-600 dark:text-gray-400 font-medium mb-1">Respuesta(s):</p>
                                                                @if($respuestas->isEmpty())
                                                                    <span class="text-gray-500 italic">No respondida</span>
                                                                @else
                                                                    <ul class="list-disc list-inside space-y-1">
                                                                        @foreach($respuestas as $resp)
                                                                            <li class="text-gray-800 dark:text-gray-200">
                                                                                {{ $resp->opcion->opcion ?? 'Opción eliminada' }}
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif

                                                                @php
                                                                    // Determine if the set of answers is correct
                                                                    $selectedOptions = $respuestas->pluck('opcion_id')->sort()->values()->all();
                                                                    $correctOptions = $pregunta->opciones->where('es_correcta', true)->pluck('id')->sort()->values()->all();
                                                                    $isCorrect = !empty($selectedOptions) && $selectedOptions == $correctOptions;
                                                                @endphp

                                                                @if($isCorrect)
                                                                    <span class="inline-flex items-center mt-2 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                                        ✓ Correcta
                                                                    </span>
                                                                @else
                                                                    <span class="inline-flex items-center mt-2 rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                                        ✗ Incorrecta
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                                

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <script>
                        function toggleAttempt(id) {
                            const element = document.getElementById('attempt-' + id);
                            const button = document.querySelector(`button[aria-controls="attempt-${id}"]`);
                            const isHidden = element.classList.toggle('hidden');
                            if (button) {
                                button.setAttribute('aria-expanded', isHidden ? 'false' : 'true');
                            }
                        }
                    </script>
                @endif
            @endauth
        @endif

        <div class="mt-6">
            @auth
                @if(auth()->user()->hasRole('docente'))
                    <a href="{{ route('exams.materia', $examen->materia_id) }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Volver a Exámenes
                    </a>
                @else
                    <a href="{{ route('exams.materia.alumno', $examen->materia_id) }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Volver a Exámenes
                    </a>
                @endif
            @endauth
        </div>
    </div>
</x-layouts.app>
