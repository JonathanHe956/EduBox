<x-layouts.app :title="_('Administracion de Materias')">
    {{-- Mensaje de exito --}}
    @if (session()->has('mensaje'))
        <script>
            alert("{{ session()->get('mensaje') }}");
        </script>
    @endif

    {{-- Mensaje de error --}}
    @if (session()->has('error'))
        <script>
            alert("{{ session()->get('error') }}");
        </script>
    @endif
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Materias disponibles</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Administrar asignaturas del curso</p>
            </div>
            <a href="{{ route('materia.create') }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Crear Materia
            </a>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-zinc-900">
            <form action="{{ route('materia.buscar') }}" method="post" class="flex gap-4">
                @csrf
                <input type="text" name="materia" placeholder="Buscar materia..." class="flex-1 rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white"/>
                <button type="submit" class="rounded-md bg-blue-900 px-4 py-2 text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:ring-offset-2">
                    Buscar
                </button>
            </form>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="bg-gradient-to-r from-blue-700 to-blue-800 text-xs font-medium uppercase text-white">
                        <tr>
                            <th scope="col" class="px-6 py-3">ID</th>
                            <th scope="col" class="px-6 py-3">Nombre</th>
                            <th scope="col" class="px-6 py-3">Créditos</th>
                            <th scope="col" class="px-6 py-3">Carrera</th>
                            <th scope="col" class="px-6 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($materias as $materia)
                        <tr class="border-b border-blue-200/50 dark:border-blue-700/50 hover:bg-blue-50/50 dark:hover:bg-blue-900/30 transition-colors duration-150">
                            <td class="px-6 py-4 font-medium text-blue-900 dark:text-white">{{ $materia->id }}</td>
                            <td class="px-6 py-4 text-blue-900 dark:text-white">{{ $materia->nombre }}</td>
                            <td class="px-6 py-4 text-blue-900 dark:text-white">{{ $materia->creditos }}</td>
                            <td class="px-6 py-4 text-blue-900 dark:text-white">{{ $materia->carrera->nombre ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('materia.show', $materia) }}" class="rounded-md bg-blue-500 px-3 py-1 text-sm text-white hover:bg-blue-600">Ver</a>
                                    <a href="{{ route('materia.edit', $materia) }}" class="rounded-md bg-gray-500 px-3 py-1 text-sm text-white hover:bg-gray-600">Editar</a>
                                    <form action="{{ route('materia.destroy', $materia) }}" method="post" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('¿Estás seguro?')" class="rounded-md bg-red-500 px-3 py-1 text-sm text-white hover:bg-red-600">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
