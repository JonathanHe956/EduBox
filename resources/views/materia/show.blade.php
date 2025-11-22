<x-layouts.app :title="_('Detalles de la Materia')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        {{-- Encabezado y Botones de Acción --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">{{ $materia->nombre }}</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Detalles de la materia</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('materia.edit', $materia) }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Editar
                </a>
                <a href="{{ route('materia.index') }}" class="inline-flex items-center rounded-md bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Volver
                </a>
            </div>
        </div>

        {{-- Tarjeta de Información General --}}
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-zinc-900">
            <h2 class="text-lg font-semibold text-blue-900 dark:text-white mb-4">Información General</h2>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nombre</label>
                    <p class="mt-1 text-lg text-blue-900 dark:text-white">{{ $materia->nombre }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Créditos</label>
                    <p class="mt-1 text-lg text-blue-900 dark:text-white">{{ $materia->creditos }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Carrera</label>
                    <p class="mt-1 text-lg text-blue-900 dark:text-white">{{ $materia->carrera->nombre ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Sección de Docente Asignado --}}
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-zinc-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-blue-900 dark:text-white">Docente Asignado</h2>
            </div>

            @if ($materia->docente)
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-lg font-medium text-blue-900 dark:text-white">{{ $materia->docente->nombre_completo }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $materia->docente->email }}</p>
                        </div>
                        <form action="{{ route('docente.unassignMateria', ['docente' => $materia->docente, 'materia' => $materia]) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('¿Estás seguro de desasignar a este docente?')" class="rounded-md bg-red-500 px-4 py-2 text-sm font-medium text-white hover:bg-red-600">
                                Desasignar
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="p-6 border-b border-blue-200/50 dark:border-blue-700/50">
                    <p class="text-center text-gray-500 dark:text-gray-400 mb-4">No hay docente asignado a esta materia.</p>
                    <form action="{{ route('materia.assignDocente', $materia) }}" method="post" class="flex items-end gap-4">
                        @csrf
                        <div class="flex-1">
                            <label for="docente_id" class="block text-sm font-medium text-gray-500 dark:text-gray-400">Asignar Docente</label>
                            <select name="docente_id" id="docente_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white">
                                <option value="">Seleccionar docente...</option>
                                @foreach($docentesDisponibles as $docente)
                                    <option value="{{ $docente->id }}">{{ $docente->nombre_completo }} ({{ $docente->materias_count }}/5 materias)</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                            Asignar
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Sección de Alumnos Inscritos --}}
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-zinc-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-blue-900 dark:text-white">Alumnos Inscritos</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Lista de alumnos que cursan esta materia.</p>
            </div>

            {{-- Formulario para inscribir alumno --}}
            <div class="p-6 border-b border-blue-200/50 dark:border-blue-700/50">
                <form action="{{ route('materia.enroll', $materia) }}" method="post" class="flex items-end gap-4">
                    @csrf
                    <div class="flex-1">
                        <label for="alumno_id" class="block text-sm font-medium text-gray-500 dark:text-gray-400">Inscribir Alumno</label>
                        <select name="alumno_id" id="alumno_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white">
                            <option value="">Seleccionar alumno...</option>
                            @foreach($alumnosNoInscritos as $alumno)
                                <option value="{{ $alumno->id }}">{{ $alumno->nombre_completo }} ({{ $alumno->materias_count }}/5)</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                        Inscribir
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="bg-gradient-to-r from-blue-700 to-blue-800 text-xs font-medium uppercase text-white">
                        <tr>
                            <th scope="col" class="px-6 py-3">ID</th>
                            <th scope="col" class="px-6 py-3">Nombre del Alumno</th>
                            <th scope="col" class="px-6 py-3">Carrera</th>
                            <th scope="col" class="px-6 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($materia->alumnos as $alumno)
                            <tr class="border-b border-blue-200/50 dark:border-blue-700/50 hover:bg-blue-50/50 dark:hover:bg-blue-900/30 transition-colors duration-150">
                                <td class="px-6 py-4 font-medium text-blue-900 dark:text-white">{{ $alumno->id }}</td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">{{ $alumno->nombre_completo }}</td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">{{ $alumno->carrera->nombre }}</td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('inscripcion.destroy') }}" method="post" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="alumno_id" value="{{ $alumno->id }}">
                                        <input type="hidden" name="materia_id" value="{{ $materia->id }}">
                                        <button type="submit" onclick="return confirm('¿Estás seguro de dar de baja a este alumno?')" class="rounded-md bg-red-500 px-3 py-1 text-sm text-white hover:bg-red-600">
                                            Dar de Baja
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No hay alumnos inscritos en esta materia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>

@if(session('mensaje'))
    <script>
        alert("{{ session('mensaje') }}");
    </script>
@endif

@if(session('error'))
    <script>
        alert("{{ session('error') }}");
    </script>
@endif
