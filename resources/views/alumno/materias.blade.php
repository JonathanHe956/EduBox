<x-layouts.app :title="__('Mis Materias')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        {{-- Encabezado --}}
        <div>
            <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Mis Materias Inscritas</h1>
            <p class="mt-1 text-blue-700 dark:text-blue-200">Lista de asignaturas en las que estás matriculado.</p>
        </div>

        {{-- Sección de Materias Inscritas --}}
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="bg-gradient-to-r from-blue-700 to-blue-800 text-xs font-medium uppercase text-white">
                        <tr>
                            <th scope="col" class="px-6 py-3">ID</th>
                            <th scope="col" class="px-6 py-3">Nombre de la Materia</th>
                            <th scope="col" class="px-6 py-3">Créditos</th>
                            <th scope="col" class="px-6 py-3">Carrera</th>
                            <th scope="col" class="px-6 py-3">Calificación</th>
                            <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($materias as $materia)
                            <tr class="border-b border-blue-200/50 dark:border-blue-700/50 hover:bg-blue-50/50 dark:hover:bg-blue-900/30 transition-colors duration-150">
                                <td class="px-6 py-4 font-medium text-blue-900 dark:text-white">
                                    {{ $materia->id }}
                                </td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">
                                    {{ $materia->nombre }}
                                </td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">
                                    {{ $materia->creditos }}
                                </td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">
                                    {{ $materia->carrera->nombre ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">
                                    {{ $materia->pivot->calificacion ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('examenes.materia.alumno', $materia) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        Ver exámenes
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No tienes materias inscritas aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
