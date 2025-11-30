<x-layouts.app :title="'Resultado: ' . $intento->examen->titulo">
    <div class="px-4 py-6 max-w-4xl mx-auto">
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-semibold text-blue-900 dark:text-white">Resultado: {{ $intento->examen->titulo }}</h1>
            <p class="mt-1 text-sm text-blue-700 dark:text-blue-200">
                Realizado el {{ $intento->created_at->format('d/m/Y H:i') }}
            </p>
        </div>

        <div class="mb-8 text-center">
            @if($intento->isEnRevision())
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-6 dark:border-yellow-900/30 dark:bg-yellow-900/20">
                    <div class="flex items-center justify-center gap-4">
                        <div class="rounded-full bg-yellow-100 p-2 text-yellow-600 dark:bg-yellow-900/50 dark:text-yellow-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-semibold">Examen en Revisión</p>
                            <p class="mt-1 text-sm">Tu examen está siendo revisado por el docente. La calificación final estará disponible pronto.</p>
                        </div>
                    </div>
                </div>
            @else
                {{-- Calificación --}}
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

        {{-- Revisión de respuestas --}}
        @if(!$intento->isEnRevision())
            <div class="mt-8">
                <h2 class="text-xl font-semibold text-blue-900 dark:text-white mb-4">Revisión de Respuestas</h2>
                <div class="space-y-4">
                    @foreach($intento->examen->preguntas as $index => $pregunta)
                        @php
                            $respuestas = $intento->respuestas->where('pregunta_id', $pregunta->id);
                            $esAbierta = $pregunta->isAbierta();
                            $esCorrecta = false;
                            
                            if ($esAbierta) {
                                $respuesta = $respuestas->first();
                                // Considera correcta si tiene puntos (calificada)
                                $esCorrecta = $respuesta && $respuesta->puntos_obtenidos > 0;
                            } else {
                                // Para preguntas multiple choice, verifica si todas las opciones seleccionadas son correctas y no hay opciones incorrectas seleccionadas
                                // La lógica coincide con el controlador: comparación estricta de las opciones seleccionadas vs las correctas
                                $opcionesSeleccionadas = $respuestas->pluck('opcion_id')->sort()->values()->all();
                                $opcionesCorrectas = $pregunta->opciones->where('es_correcta', true)->pluck('id')->sort()->values()->all();
                                $esCorrecta = $opcionesSeleccionadas == $opcionesCorrectas;
                            }
                        @endphp
                        
                        <div class="rounded-lg border {{ $esCorrecta ? 'border-green-200 bg-green-50 dark:border-green-900/30 dark:bg-green-900/10' : ($esAbierta && (!$respuestas->first() || $respuestas->first()->puntos_obtenidos === null) ? 'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-zinc-900' : 'border-red-200 bg-red-50 dark:border-red-900/30 dark:bg-red-900/10') }} p-6">
                            <div class="flex items-start gap-3">
                                @if($esAbierta)
                                    <svg class="h-6 w-6 flex-shrink-0 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @elseif($esCorrecta)
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
                                    
                                    @if($esAbierta)
                                        @php $respuesta = $respuestas->first(); @endphp
                                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                            <span class="font-medium">Tu respuesta:</span>
                                        </p>
                                        <div class="mt-1 rounded bg-white dark:bg-zinc-800 p-3 text-sm text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                            {{ optional($respuesta)->respuesta_abierta ?? 'No respondida' }}
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
                                                        <li>{{ optional($resp->opcion)->opcion ?? 'Opción eliminada' }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>

                                        @if(!$esCorrecta)
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
