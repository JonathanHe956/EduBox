<x-layouts.app :title="_('Administracion de Carreras')">
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
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Carreras disponibles</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Administrar programas académicos</p>
            </div>
            <a href="{{ route('carrera.create') }}" class="btn-primary">
                Crear Carrera
            </a>
        </div>

        <div class="glass-card p-4">
            <form action="{{ route('carrera.buscar') }}" method="post" class="flex gap-4">
                @csrf
                <input type="text" name="Buscar_carrera" placeholder="Buscar carrera..." class="flex-1 rounded-md border border-blue-200 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white"/>
                <button type="submit" class="btn-primary">
                    Buscar
                </button>
            </form>
        </div>

        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="bg-gradient-to-r from-blue-700 to-blue-800 text-xs font-medium uppercase text-white">
                        <tr>
                            <th scope="col" class="px-6 py-3">ID</th>
                            <th scope="col" class="px-6 py-3">Nombre</th>
                            <th scope="col" class="px-6 py-3">Créditos</th>
                            <th scope="col" class="px-6 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($carreras as $carrera)
                        <tr class="border-b border-blue-200/50 dark:border-blue-700/50 hover:bg-blue-50/50 dark:hover:bg-blue-900/30 transition-colors duration-150">
                            <td class="px-6 py-4 font-medium text-blue-900 dark:text-white">{{ $carrera->id }}</td>
                            <td class="px-6 py-4 text-blue-900 dark:text-white">{{ $carrera->nombre }}</td>
                            <td class="px-6 py-4 text-blue-900 dark:text-white">{{ $carrera->creditos }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('carrera.show', $carrera) }}" class="btn-blue px-3 py-1 text-sm">Ver</a>
                                    <a href="{{ route('carrera.edit', $carrera) }}" class="btn-secondary px-3 py-1 text-sm">Editar</a>
                                    <form action="{{ route('carrera.destroy', $carrera) }}" method="post" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('¿Estás seguro?')" class="rounded-md bg-red-100 px-3 py-1 text-sm text-red-700 hover:bg-red-200 border border-red-200 transition-colors">Eliminar</button>
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
