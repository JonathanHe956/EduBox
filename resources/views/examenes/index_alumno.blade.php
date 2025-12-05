<x-layouts.app :title="__('Mis Exámenes')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-8">
        
        {{-- Encabezado --}}
        <div>
            <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Mis Exámenes</h1>
            <p class="mt-1 text-blue-700 dark:text-blue-200">Lista de exámenes pendientes y realizados.</p>
        </div>

        {{-- Lista de Exámenes --}}
        <div class="glass-card p-6">
            @if($examenes->isEmpty())
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/30 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-blue-600 dark:text-blue-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 019 9v.375M10.125 2.25A3.375 3.375 0 0113.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 013.375 3.375M9 15l2.25 2.25L15 12" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-blue-900 dark:text-white">Todo al día</h3>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">No tienes exámenes pendientes en este momento.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="bg-gradient-to-r from-blue-700 to-blue-800 text-xs font-medium uppercase text-white">
                            <tr>
                                <th scope="col" class="px-6 py-3">Examen</th>
                                <th scope="col" class="px-6 py-3">Materia</th>
                                <th scope="col" class="px-6 py-3">Estado</th>
                                <th scope="col" class="px-6 py-3 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($examenes as $examen)
                                @php
                                    $intento = $examen->intentos->first();
                                    $tieneIntento = $intento !== null;
                                @endphp
                                <tr class="border-b border-blue-200/50 dark:border-blue-700/50 hover:bg-blue-50/50 dark:hover:bg-blue-900/30 transition-colors duration-150">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-blue-900 dark:text-white text-base">{{ $examen->titulo }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Str::limit($examen->descripcion, 60) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-blue-900 dark:text-white">
                                        {{ $examen->materia->nombre ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($tieneIntento)
                                            <div class="flex flex-col gap-1">
                                                <span class="inline-flex items-center w-fit px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                    Finalizado
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    Calificación: {{ $intento->puntuacion == floor($intento->puntuacion) ? number_format($intento->puntuacion, 0) : number_format($intento->puntuacion, 1) }}/{{ $intento->total }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                Pendiente
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($tieneIntento)
                                            <a href="{{ route('examenes.result', $intento) }}" class="btn-secondary text-xs px-3 py-1.5 inline-flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Ver Resultados
                                            </a>
                                        @else
                                            <a href="{{ route('examenes.show', $examen) }}" class="btn-primary text-xs px-3 py-1.5 inline-flex items-center gap-1 shadow-md hover:shadow-lg transition-all">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                                Realizar Examen
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Botón de regreso --}}
        <div class="mt-4">
            <a href="{{ route('mis.materias') }}" class="inline-flex items-center gap-2 btn-secondary px-4 py-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Volver a Mis Materias
            </a>
        </div>
    </div>
</x-layouts.app>