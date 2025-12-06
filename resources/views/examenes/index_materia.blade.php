<x-layouts.app :title="__('Exámenes de ' . $materia->nombre)">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        {{-- Encabezado --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Exámenes de {{ $materia->nombre }}</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Gestiona los exámenes para esta materia.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="glass-card p-4 bg-green-50 border-green-200 dark:bg-green-900/20">
                <p class="text-green-600 dark:text-green-400">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Lista de Exámenes --}}
        <div class="glass-card">
            @if($examenes->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-12 w-12 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-blue-900 dark:text-white">No hay exámenes</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza creando un examen para esta materia.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="bg-gradient-to-r from-blue-700 to-blue-800 text-xs font-medium uppercase text-white">
                            <tr>
                                <th scope="col" class="px-6 py-3">Título</th>
                                <th scope="col" class="px-6 py-3">Preguntas</th>
                                <th scope="col" class="px-6 py-3">Fecha de creación</th>
                                <th scope="col" class="px-6 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($examenes as $ex)
                                <tr class="border-b border-blue-200/50 dark:border-blue-700/50 hover:bg-blue-50/50 dark:hover:bg-blue-900/30 transition-colors duration-150">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-blue-900 dark:text-white">{{ $ex->titulo }}</div>
                                        @if($ex->descripcion)
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($ex->descripcion, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-blue-900 dark:text-white">
                                        {{ $ex->preguntas_count }} preguntas
                                    </td>
                                    <td class="px-6 py-4 text-blue-900 dark:text-white">
                                        {{ $ex->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('examenes.show', $ex) }}" class="btn-blue px-3 py-1 text-xs">
                                                Ver
                                            </a>
                                            <a href="{{ route('examenes.edit', $ex) }}" class="inline-flex items-center gap-1 rounded-lg bg-gold-600 px-3 py-1 text-xs font-medium text-white hover:bg-gold-700 focus:outline-none focus:ring-2 focus:ring-gold-500">
                                                Editar
                                            </a>
                                            <form action="{{ route('examenes.destroy', $ex) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar este examen? Esta acción no se puede deshacer.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-red-600 px-3 py-1 text-xs font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Botón de regreso --}}
        <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <a href="{{ route('mis.materias') }}" class="inline-flex items-center gap-2 btn-secondary px-4 py-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Volver a Mis Materias
            </a>

            <a href="{{ route('examenes.create', $materia) }}" class="btn-primary px-4 py-2">
                + Crear Examen
            </a>
        </div>
    </div>
</x-layouts.app>
