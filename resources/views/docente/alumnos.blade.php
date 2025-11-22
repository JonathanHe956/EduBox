<x-layouts.app :title="__('Mis Alumnos')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        {{-- Encabezado --}}
        <div>
            <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Mis Alumnos</h1>
            <p class="mt-1 text-blue-700 dark:text-blue-200">Lista de estudiantes inscritos en tus materias.</p>
        </div>

        {{-- Sección de Alumnos --}}
        <div class="glass-card">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="bg-gradient-to-r from-blue-700 to-blue-800 text-xs font-medium uppercase text-white">
                        <tr>
                            <th scope="col" class="px-6 py-3">ID</th>
                            <th scope="col" class="px-6 py-3">Nombre Completo</th>
                            <th scope="col" class="px-6 py-3">Carrera</th>
                            <th scope="col" class="px-6 py-3">Materias en Común</th>
                            <th scope="col" class="px-6 py-3">Promedio General</th>
                            <th scope="col" class="px-6 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alumnos as $alumno)
                            <tr class="border-b border-blue-200/50 dark:border-blue-700/50 hover:bg-blue-50/50 dark:hover:bg-blue-900/30 transition-colors duration-150">
                                <td class="px-6 py-4 font-medium text-blue-900 dark:text-white">
                                    {{ $alumno->id }}
                                </td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">
                                    {{ $alumno->nombre_completo }}
                                </td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">
                                    {{ $alumno->carrera->nombre ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">
                                    {{ $alumno->materias_count }}
                                </td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">
                                    @if($alumno->promedio_general)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            @if($alumno->promedio_general >= 70) bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                            @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                            @endif">
                                            {{ $alumno->promedio_general }}
                                        </span>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">Sin calificación</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('docente.alumno.show', $alumno) }}" class="inline-flex items-center gap-2 btn-blue text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Ver Detalle
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No tienes alumnos inscritos en tus materias aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Botón de regreso --}}
        <div class="mt-4">
            <a href="{{ route('mis.materias') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Volver a Mis Materias
            </a>
        </div>
    </div>
</x-layouts.app>
