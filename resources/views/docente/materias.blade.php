<x-layouts.app :title="__('Materias Asignadas')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Materias Asignadas</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Docente: {{ $docente->nombre }} {{ $docente->apaterno }}</p>
            </div>
        </div>

        <div class="table-modern">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead>
                        <tr>
                            <th scope="col" class="px-6 py-3">Nombre de la Materia</th>
                            <th scope="col" class="px-6 py-3">Créditos</th>
                            <th scope="col" class="px-6 py-3">Carrera</th>
                            <th scope="col" class="px-6 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($materias as $materia)
                        <tr>
                            <td class="px-6 py-4 text-blue-900 dark:text-white">{{ $materia->nombre }}</td>
                            <td class="px-6 py-4 text-blue-900 dark:text-white">{{ $materia->creditos }}</td>
                            <td class="px-6 py-4 text-blue-900 dark:text-white">{{ $materia->carrera->nombre }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('docente.alumnos.materia', $materia) }}" class="btn-blue px-3 py-1 text-xs" wire:navigate>
                                        Ver Alumnos
                                    </a>
                                    <a href="{{ route('examenes.materia', $materia) }}" class="btn-secondary px-3 py-1 text-xs" wire:navigate>
                                        Ver Exámenes
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No hay materias asignadas a este docente.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 btn-secondary px-4 py-2" wire:navigate>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Volver
            </a>
        </div>
    </div>
</x-layouts.app>
