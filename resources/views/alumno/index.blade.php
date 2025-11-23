<x-layouts.app :title="_('Administracion de Alumnos')">
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
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Alumnos disponibles</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Administrar registros de estudiantes</p>
            </div>
            <a href="{{ route('alumno.create') }}" class="btn-primary" wire:navigate>
                Crear Alumno
            </a>
        </div>

        {{-- Barra de Busqueda --}}
        <div class="glass-card p-4">
            <form action="{{ route('alumno.buscar') }}" method="post" class="flex gap-4">
                @csrf
                <input type="text" name="Buscar_alumno" placeholder="Buscar alumno..." class="flex-1 rounded-md border border-blue-200 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white"/>
                <button type="submit" class="btn-primary">
                    Buscar
                </button>
            </form>
        </div>

        {{-- Grid de Tarjetas --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($alumnos as $alumno)
            <div class="glass-card p-6 flex flex-col items-center text-center hover:scale-[1.02] transition-transform duration-200">
                {{-- Foto del Alumno --}}
                <div class="mb-4 relative">
                    @if($alumno->foto)
                        <img src="{{ asset('storage/' . $alumno->foto) }}" alt="Foto de {{ $alumno->nombre }}" class="w-24 h-24 rounded-full object-cover border-4 border-white dark:border-blue-800 shadow-md">
                    @else
                        @php
                            $initials = strtoupper(substr($alumno->nombre ?? '', 0, 1) . substr($alumno->apaterno ?? '', 0, 1));
                            $bg = '#E6EEF8';
                            $fg = '#0F172A';
                        @endphp
                        <svg class="w-24 h-24 rounded-full border-4 border-white dark:border-blue-800 shadow-md" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Avatar {{ $alumno->nombre }}">
                            <rect width="100" height="100" rx="50" fill="{{ $bg }}" />
                            <text x="50" y="56" font-size="36" font-family="Arial, Helvetica, sans-serif" fill="{{ $fg }}" text-anchor="middle" dominant-baseline="middle">{{ $initials }}</text>
                        </svg>
                    @endif
                </div>

                {{-- Información del Alumno --}}
                <h3 class="text-lg font-semibold text-blue-900 dark:text-white mb-1">{{ $alumno->nombre }} {{ $alumno->apaterno }}</h3>
                <p class="text-sm text-blue-500 mb-3 break-all">{{ $alumno->email }}</p>

                <div class="w-full border-t border-blue-100 dark:border-blue-800 my-3"></div>

                <div class="grid grid-cols-2 gap-2 w-full text-sm mb-4">
                    <div class="text-right text-blue-400 dark:text-blue-300">Edad:</div>
                    <div class="text-left text-blue-800 dark:text-blue-100 font-medium">{{ $alumno->edad }} años</div>
                    <div class="text-right text-blue-400 dark:text-blue-300">Sexo:</div>
                    <div class="text-left text-blue-800 dark:text-blue-100 font-medium">{{ $alumno->sexo == 'M' ? 'M' : 'F' }}</div>
                    <div class="col-span-2 text-center mt-1 text-blue-600 dark:text-blue-200 font-medium bg-blue-50 dark:bg-blue-900/30 rounded py-1">
                        {{ $alumno->carrera->nombre ?? 'Sin Carrera' }}
                    </div>
                </div>

                {{-- Botones de Acción --}}
                <div class="flex gap-2 mt-auto w-full justify-center">
                    <a href="{{ route('alumno.show', $alumno) }}" class="btn-blue px-3 py-1 text-sm flex-1 text-center">Ver</a>
                    <a href="{{ route('alumno.edit', $alumno) }}" class="btn-secondary px-3 py-1 text-sm flex-1 text-center">Editar</a>
                    <form action="{{ route('alumno.destroy', $alumno) }}" method="post" class="inline flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('¿Estás seguro?')" class="w-full rounded-md bg-red-100 px-3 py-1 text-sm text-red-700 hover:bg-red-200 border border-red-200 transition-colors">Eliminar</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>