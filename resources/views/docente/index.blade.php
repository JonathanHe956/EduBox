<x-layouts.app :title="_('Administracion de Docentes')">
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
        {{-- Encabezado --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Docentes disponibles</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Administrar registros de profesores</p>
            </div>
            <a href="{{ route('docente.create') }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Crear Docente
            </a>
        </div>

        {{-- Barra de Busqueda --}}
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-zinc-900">
            <form action="{{ route('docente.buscar') }}" method="post" class="flex gap-4">
                @csrf
                <input type="text" name="Buscar_docente" placeholder="Buscar docente..." class="flex-1 rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white"/>
                <button type="submit" class="rounded-md bg-blue-900 px-4 py-2 text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:ring-offset-2">
                    Buscar
                </button>
            </form>
        </div>

        {{-- Grid de Tarjetas (Estilo Alumnos aplicado a Docentes) --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($docentes as $docente)
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-zinc-900">
                <div class="flex flex-col items-center text-center">
                    
                    {{-- Logica de Imagen / Avatar --}}
                    @if($docente->foto)
                        <img src="{{ asset('storage/' . $docente->foto) }}" alt="Foto de {{ $docente->nombre }}" class="w-24 h-24 rounded-full object-cover mb-4">
                    @else
                        @php
                            $initials = strtoupper(substr($docente->nombre ?? '', 0, 1) . substr($docente->apaterno ?? '', 0, 1));
                            $bg = '#CFE6FF';
                            $fg = '#1E3A8A';
                        @endphp
                        <svg class="w-24 h-24 rounded-full mb-4" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Avatar {{ $docente->nombre }}">
                            <rect width="100" height="100" rx="50" fill="{{ $bg }}" />
                            <text x="50" y="56" font-size="36" font-family="Arial, Helvetica, sans-serif" fill="{{ $fg }}" text-anchor="middle" dominant-baseline="middle">{{ $initials }}</text>
                        </svg>
                    @endif

                    {{-- Información del Docente --}}
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-white">{{ $docente->nombre }} {{ $docente->apaterno }} {{ $docente->amaterno }}</h3>
                    
                    {{-- Email añadido (no estaba en alumnos, pero es util en docentes) --}}
                    <p class="text-sm text-blue-500 mb-2 break-all">{{ $docente->email }}</p>

                    <p class="text-sm text-blue-700 dark:text-blue-200 mb-1">{{ $docente->sexo == 'M' ? 'Masculino' : 'Femenino' }}</p>
                    <p class="text-sm text-blue-700 dark:text-blue-200 mb-2">{{ $docente->edad }} años</p>
                    <p class="text-sm text-blue-700 dark:text-blue-200 mb-4 font-medium">{{ $docente->carrera->nombre ?? 'N/A' }}</p>

                    {{-- Botones de Acción --}}
                    <div class="flex gap-2">
                        <a href="{{ route('docente.show', $docente) }}" class="rounded-md bg-blue-500 px-3 py-1 text-sm text-white hover:bg-blue-600">Ver</a>
                        <a href="{{ route('docente.edit', $docente) }}" class="rounded-md bg-gray-500 px-3 py-1 text-sm text-white hover:bg-gray-600">Editar</a>
                        <form action="{{ route('docente.destroy', $docente) }}" method="post" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('¿Estás seguro?')" class="rounded-md bg-red-500 px-3 py-1 text-sm text-white hover:bg-red-600">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>