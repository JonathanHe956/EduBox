<x-layouts.app :title="__('Materias Asignadas')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Materias Asignadas</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Docente: {{ $docente->nombre }} {{ $docente->apaterno }}</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn-secondary" wire:navigate>
                Volver
            </a>
        </div>

        <div class="table-modern">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead>
                        <tr>
                            <th scope="col" class="px-6 py-3">ID</th>
                            <th scope="col" class="px-6 py-3">Nombre de la Materia</th>
                            <th scope="col" class="px-6 py-3">Créditos</th>
                            <th scope="col" class="px-6 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($materias as $materia)
                        <tr>
                            <td class="px-6 py-4 font-medium text-blue-900 dark:text-white">{{ $materia->id }}</td>
                            <td class="px-6 py-4 text-blue-900 dark:text-white">{{ $materia->nombre }}</td>
                            <td class="px-6 py-4 text-blue-900 dark:text-white">{{ $materia->creditos }}</td>
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
    </div>
</x-layouts.app>
