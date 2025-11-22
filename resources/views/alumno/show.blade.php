<x-layouts.app :title="_('Detalles de Alumno')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        {{-- Encabezado y Botones de Acción --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">{{ $alumno->nombre }} {{ $alumno->apaterno }} {{ $alumno->amaterno }}</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Detalles del estudiante</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('alumno.edit', $alumno) }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Editar
                </a>
                <a href="{{ route('alumno.index') }}" class="inline-flex items-center rounded-md bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Volver
                </a>
            </div>
        </div>

        {{-- Tarjeta de Información General --}}
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-zinc-900">
            <h2 class="text-lg font-semibold text-blue-900 dark:text-white mb-4">Información General</h2>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nombre Completo</label>
                    <p class="mt-1 text-lg text-blue-900 dark:text-white">{{ $alumno->nombre }} {{ $alumno->apaterno }} {{ $alumno->amaterno }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Sexo</label>
                    <p class="mt-1 text-lg text-blue-900 dark:text-white">{{ $alumno->sexo == 'M' ? 'Masculino' : 'Femenino' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Edad</label>
                    <p class="mt-1 text-lg text-blue-900 dark:text-white">{{ $alumno->edad }} años</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Nacimiento</label>
                    <p class="mt-1 text-lg text-blue-900 dark:text-white">{{ \Carbon\Carbon::parse($alumno->fecha_nacimiento)->format('d/m/Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Carrera</label>
                    <p class="mt-1 text-lg text-blue-900 dark:text-white">{{ $alumno->carrera->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                    <p class="mt-1 text-lg text-blue-900 dark:text-white">{{ $alumno->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Foto</label>
                    @if($alumno->foto)
                        <img src="{{ asset('storage/' . $alumno->foto) }}" alt="Foto de {{ $alumno->nombre }}" class="w-24 h-24 rounded-full object-cover mt-1">
                    @else
                        <div class="w-24 h-24 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center mt-1">
                            <span class="text-gray-600 dark:text-gray-300 text-2xl">{{ substr($alumno->nombre, 0, 1) }}{{ substr($alumno->apaterno, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sección de Materias Inscritas --}}
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-zinc-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-blue-900 dark:text-white">
                    Materias Inscritas 
                    <span class="text-sm font-normal text-gray-600 dark:text-gray-400">
                        ({{ $alumno->materias->count() }}/5)
                    </span>
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Lista de asignaturas en las que está matriculado el estudiante.</p>
            </div>

            @if($alumno->materias->count() >= 5)
                {{-- Mensaje cuando se alcanza el límite --}}
                <div class="p-6 border-b border-blue-200/50 dark:border-blue-700/50">
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded dark:bg-yellow-900/30 dark:border-yellow-900/50 dark:text-yellow-400">
                        <span class="block sm:inline">El alumno ya tiene inscrito el máximo de 5 materias.</span>
                    </div>
                </div>
            @else
                {{-- Formulario para agregar materia --}}
                <div class="p-6 border-b border-blue-200/50 dark:border-blue-700/50">
                    <form action="{{ route('alumno.enroll', $alumno) }}" method="post" class="flex gap-4">
                        @csrf
                        @if(session('error'))
                            <div class="w-full bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif
                        @if(session('mensaje'))
                            <div class="w-full bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                                <span class="block sm:inline">{{ session('mensaje') }}</span>
                            </div>
                        @endif
                        <div class="flex-1">
                            <label for="materia_id" class="block text-sm font-medium text-gray-500 dark:text-gray-400">Agregar Materia</label>
                            <select name="materia_id" id="materia_id" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white">
                                <option value="">Seleccionar materia...</option>
                            @foreach($materiasDisponibles as $materia)
                                <option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Inscribir
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="bg-gradient-to-r from-blue-700 to-blue-800 text-xs font-medium uppercase text-white">
                        <tr>
                            <th scope="col" class="px-6 py-3">ID</th>
                            <th scope="col" class="px-6 py-3">Nombre de la Materia</th>
                            <th scope="col" class="px-6 py-3">Créditos</th>
                            <th scope="col" class="px-6 py-3">Calificación</th>
                            <th scope="col" class="px-6 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alumno->materias as $materia)
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
                                    {{ $materia->pivot->calificacion ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('inscripcion.destroy') }}" method="post" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="alumno_id" value="{{ $alumno->id }}">
                                        <input type="hidden" name="materia_id" value="{{ $materia->id }}">
                                        <button type="submit" onclick="return confirm('¿Estás seguro de desinscribir esta materia?')" class="rounded-md bg-red-500 px-3 py-1 text-sm text-white hover:bg-red-600">Desinscribir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No hay materias inscritas para este alumno aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
