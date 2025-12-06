<x-layouts.app :title="__('Alumnos de la Materia')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-8">

        {{-- Encabezado --}}
        <div>
            <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Alumnos de {{ $materia->nombre }}</h1>
            <p class="mt-1 text-blue-700 dark:text-blue-200">Lista de estudiantes inscritos en esta materia.</p>
        </div>

        {{-- Información de la Materia --}}
        <div class="glass-card p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Materia</p>
                    <p class="text-base font-medium text-blue-900 dark:text-white">{{ $materia->nombre }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Carrera</p>
                    <p class="text-base font-medium text-blue-900 dark:text-white">{{ $materia->carrera->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Créditos</p>
                    <p class="text-base font-medium text-blue-900 dark:text-white">{{ $materia->creditos }}</p>
                </div>
            </div>
        </div>

        {{-- Lista de Alumnos --}}
        <div class="glass-card p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="bg-gradient-to-r from-blue-700 to-blue-800 text-xs font-medium uppercase text-white">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nombre Completo</th>
                            <th scope="col" class="px-6 py-3">Email</th>
                            <th scope="col" class="px-6 py-3">Carrera</th>
                            <th scope="col" class="px-6 py-3">Calificación</th>
                            <th scope="col" class="px-6 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alumnos as $alumno)
                            <tr class="border-b border-blue-200/50 dark:border-blue-700/50 hover:bg-blue-50/50 dark:hover:bg-blue-900/30 transition-colors duration-150">
                                <td class="px-6 py-4 text-blue-900 dark:text-white">
                                    {{ $alumno->nombre_completo }}
                                </td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">
                                    {{ $alumno->email }}
                                </td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">
                                    {{ $alumno->carrera->nombre ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">
                                    @if($alumno->pivot->calificacion)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            @if($alumno->pivot->calificacion >= 70) bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                            @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                            @endif">
                                            {{ $alumno->pivot->calificacion }}
                                        </span>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">Sin calificación</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('docente.alumno.show', [$alumno, $materia]) }}" class="inline-flex items-center gap-2 btn-blue text-sm px-4 py-2">
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
                                    No hay alumnos inscritos en esta materia aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
