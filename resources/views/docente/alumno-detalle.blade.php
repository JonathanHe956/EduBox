<x-layouts.app :title="__('Detalle del Alumno')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        {{-- Encabezado --}}
        <div>
            <h1 class="text-2xl font-bold text-blue-900 dark:text-white">
                Detalle del Alumno
                @if($materia)
                    <span class="text-gold-500">- {{ $materia->nombre }}</span>
                @endif
            </h1>
            <p class="mt-1 text-blue-700 dark:text-blue-200">
                @if($materia)
                    Información del estudiante en esta materia.
                @else
                    Información completa del estudiante.
                @endif
            </p>
        </div>

        {{-- Información Personal --}}
        <div class="glass-card">
            <h2 class="text-lg font-semibold text-blue-900 dark:text-white mb-4">Información Personal</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Nombre Completo</p>
                    <p class="text-base font-medium text-blue-900 dark:text-white">{{ $alumno->nombre_completo }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                    <p class="text-base font-medium text-blue-900 dark:text-white">{{ $alumno->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Carrera</p>
                    <p class="text-base font-medium text-blue-900 dark:text-white">{{ $alumno->carrera->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Edad</p>
                    <p class="text-base font-medium text-blue-900 dark:text-white">{{ $alumno->edad }} años</p>
                </div>
            </div>
        </div>

        {{-- Materias en Común --}}
        <div class="glass-card">
            <h2 class="text-lg font-semibold text-blue-900 dark:text-white mb-4">
                @if($materia)
                    Calificación en {{ $materia->nombre }}
                @else
                    Materias en Común
                @endif
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="bg-gradient-to-r from-blue-700 to-blue-800 text-xs font-medium uppercase text-white">
                        <tr>
                            <th scope="col" class="px-6 py-3">Materia</th>
                            <th scope="col" class="px-6 py-3">Carrera</th>
                            <th scope="col" class="px-6 py-3">Créditos</th>
                            <th scope="col" class="px-6 py-3">Calificación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($materiasComunes as $materia)
                            <tr class="border-b border-blue-200/50 dark:border-blue-700/50 hover:bg-blue-50/50 dark:hover:bg-blue-900/30 transition-colors duration-150">
                                <td class="px-6 py-4 font-medium text-blue-900 dark:text-white">
                                    {{ $materia->nombre }}
                                </td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">
                                    {{ $materia->carrera->nombre ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">
                                    {{ $materia->creditos }}
                                </td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">
                                    @if($materia->pivot->calificacion)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            @if($materia->pivot->calificacion >= 70) bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                            @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                            @endif">
                                            {{ $materia->pivot->calificacion }}
                                        </span>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">Sin calificación</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Historial de Exámenes --}}
        <div class="glass-card">
            <h2 class="text-lg font-semibold text-blue-900 dark:text-white mb-4">
                @if($materia)
                    Historial de Exámenes en {{ $materia->nombre }}
                @else
                    Historial de Exámenes
                @endif
            </h2>
            @if($intentos->isEmpty())
                <p class="text-gray-500 dark:text-gray-400">El alumno no ha realizado exámenes aún.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="bg-gradient-to-r from-blue-700 to-blue-800 text-xs font-medium uppercase text-white">
                            <tr>
                                <th scope="col" class="px-6 py-3">Examen</th>
                                <th scope="col" class="px-6 py-3">Materia</th>
                                <th scope="col" class="px-6 py-3">Fecha</th>
                                <th scope="col" class="px-6 py-3">Puntuación</th>
                                <th scope="col" class="px-6 py-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($intentos as $intento)
                                <tr class="border-b border-blue-200/50 dark:border-blue-700/50 hover:bg-blue-50/50 dark:hover:bg-blue-900/30 transition-colors duration-150">
                                    <td class="px-6 py-4 font-medium text-blue-900 dark:text-white">
                                        {{ $intento->examen->titulo }}
                                    </td>
                                    <td class="px-6 py-4 text-blue-900 dark:text-white">
                                        {{ $intento->examen->materia->nombre }}
                                    </td>
                                    <td class="px-6 py-4 text-blue-900 dark:text-white">
                                        {{ $intento->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-blue-900 dark:text-white">
                                        @if($intento->isCalificado())
                                            <span class="font-semibold">{{ $intento->puntuacion == floor($intento->puntuacion) ? number_format($intento->puntuacion, 0) : number_format($intento->puntuacion, 1) }}/{{ $intento->total }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                ({{ $intento->total > 0 ? round(($intento->puntuacion / $intento->total) * 100) : 0 }}%)
                                            </span>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($intento->isEnRevision())
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                En Revisión
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                Calificado
                                            </span>
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
            @if($materia)
                <a href="{{ route('docente.alumnos.materia', $materia) }}" class="inline-flex items-center gap-2 btn-secondary px-4 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Volver a Alumnos de {{ $materia->nombre }}
                </a>
            @else
                <a href="{{ route('mis.materias') }}" class="inline-flex items-center gap-2 btn-secondary px-4 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Volver a Mis Materias
                </a>
            @endif
        </div>
    </div>
</x-layouts.app>
