<x-layouts.app :title="__('Resultado del Examen')">
    <div class="px-4 py-6 max-w-4xl mx-auto">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-blue-900 dark:text-white">Resultado del Examen</h1>
            
            @if($intento->isEnRevision())
                {{-- Exam is under review --}}
                <div class="mt-6 rounded-lg bg-yellow-50 border border-yellow-200 p-8 dark:bg-yellow-900/20 dark:border-yellow-900/30">
                    <div class="flex items-center justify-center gap-3 text-yellow-800 dark:text-yellow-400">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-lg font-semibold">Examen en Revisión</p>
                            <p class="mt-1 text-sm">Tu examen está siendo revisado por el docente. La calificación final estará disponible pronto.</p>
                        </div>
                    </div>
                </div>
            @else
                {{-- Score Display --}}
                <div class="mt-6 inline-block rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 p-8 shadow-lg">
                    <div class="text-white">
                        <p class="text-sm font-medium uppercase tracking-wide">Tu calificación</p>
                        <p class="mt-2 text-6xl font-bold">{{ $intento->puntuacion == floor($intento->puntuacion) ? number_format($intento->puntuacion, 0) : number_format($intento->puntuacion, 1) }}/{{ $intento->total }}</p>
                        @php
                            $percentage = $intento->total > 0 ? round(($intento->puntuacion / $intento->total) * 100) : 0;
                        @endphp
                        <p class="mt-2 text-2xl">{{ $percentage }}%</p>
                    </div>
                </div>

                @if($percentage >= 70)
                    <p class="mt-4 text-lg font-medium text-green-600 dark:text-green-400">¡Felicidades! Has aprobado el examen.</p>
                @else
                    <p class="mt-4 text-lg font-medium text-yellow-600 dark:text-yellow-400">Necesitas mejorar. Sigue estudiando.</p>
                @endif
            @endif
        </div>

        {{-- Answers Review --}}
        @if(!$intento->isEnRevision())
            <div class="mt-8">
                <h2 class="text-xl font-semibold text-blue-900 dark:text-white mb-4">Revisión de Respuestas</h2>
                <div class="space-y-4">
                    @foreach($intento->examen->preguntas as $index => $pregunta)
                        @php
                            $respuestas = $intento->respuestas->where('pregunta_id', $pregunta->id);
                            $isOpenEnded = $pregunta->isAbierta();
                            $isCorrect = false;
                            
                            if ($isOpenEnded) {
                                $respuesta = $respuestas->first();
                                // Consider correct if it has points (graded)
                                $isCorrect = $respuesta && $respuesta->puntos_obtenidos > 0;
                            } else {
                                // For multiple choice, check if all selected options are correct and no incorrect ones are selected
                                // Logic matches controller: strict comparison of selected vs correct options
                                $selectedOptions = $respuestas->pluck('opcion_id')->sort()->values()->all();
                                $correctOptions = $pregunta->opciones->where('es_correcta', true)->pluck('id')->sort()->values()->all();
                                $isCorrect = $selectedOptions == $correctOptions;
                            }
                        @endphp
                        
                        <div class="rounded-lg border {{ $isCorrect ? 'border-green-200 bg-green-50 dark:border-green-900/30 dark:bg-green-900/10' : ($isOpenEnded && (!$respuestas->first() || $respuestas->first()->puntos_obtenidos === null) ? 'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-zinc-900' : 'border-red-200 bg-red-50 dark:border-red-900/30 dark:bg-red-900/10') }} p-6">
                            <div class="flex items-start gap-3">
                                @if($isOpenEnded)
                                    <svg class="h-6 w-6 flex-shrink-0 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @elseif($isCorrect)
                                    <svg class="h-6 w-6 flex-shrink-0 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @else
                                    <svg class="h-6 w-6 flex-shrink-0 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @endif
                                <div class="flex-1">
                                    <h3 class="font-medium text-blue-900 dark:text-white">
                                        Pregunta {{ $index + 1 }}: {{ $pregunta->pregunta ?? '—' }}
                                    </h3>
                                    
                                    @if($isOpenEnded)
                                        @php $respuesta = $respuestas->first(); @endphp
                                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                            <span class="font-medium">Tu respuesta:</span>
                                        </p>
                                        <div class="mt-1 rounded bg-white dark:bg-zinc-800 p-3 text-sm text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                            {{ $respuesta->respuesta_abierta ?? 'No respondida' }}
                                        </div>
                                        @if($respuesta && $respuesta->puntos_obtenidos !== null)
                                            <p class="mt-2 text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                                Puntos obtenidos: {{ intval($respuesta->puntos_obtenidos) }}/1
                                            </p>
                                        @endif
                                    @else
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-700 dark:text-gray-300 font-medium mb-1">Tu respuesta:</p>
                                            @if($respuestas->isEmpty())
                                                <span class="text-sm text-gray-500 italic">Sin selección</span>
                                            @else
                                                <ul class="list-disc list-inside text-sm text-gray-700 dark:text-gray-300">
                                                    @foreach($respuestas as $resp)
                                                        <li>{{ $resp->opcion->opcion ?? 'Opción eliminada' }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>

                                        @if(!$isCorrect)
                                            <div class="mt-3">
                                                <p class="text-sm text-green-700 dark:text-green-400 font-medium mb-1">Respuesta correcta:</p>
                                                <ul class="list-disc list-inside text-sm text-green-700 dark:text-green-400">
                                                    @foreach($pregunta->opciones->where('es_correcta', true) as $correctOption)
                                                        <li>{{ $correctOption->opcion }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-8 flex justify-center">
            <a href="{{ route('examenes.materia.alumno', $intento->examen->materia_id) }}" class="inline-flex items-center gap-2 btn-secondary px-6 py-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Volver a Exámenes
            </a>
        </div>
    </div>
</x-layouts.app>
