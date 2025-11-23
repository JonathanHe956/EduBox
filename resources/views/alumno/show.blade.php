<x-layouts.app :title="__('Detalles del Alumno')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Detalles del Alumno</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Información completa del estudiante</p>
            </div>
            <a href="{{ route('alumno.index') }}" class="btn-secondary" wire:navigate>
                Volver
            </a>
        </div>

        <div class="glass-card p-6 max-w-4xl mx-auto w-full">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Columna Izquierda: Información Principal -->
                <div class="flex-1 space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-blue-900 dark:text-white border-b border-blue-200 pb-2 mb-4">Información Personal</h3>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-blue-500 dark:text-blue-400">Nombre Completo</dt>
                                <dd class="mt-1 text-sm text-blue-900 dark:text-white font-semibold">{{ $alumno->nombre }} {{ $alumno->apaterno }} {{ $alumno->amaterno }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-blue-500 dark:text-blue-400">Edad</dt>
                                <dd class="mt-1 text-sm text-blue-900 dark:text-white">{{ $alumno->edad }} años</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-blue-500 dark:text-blue-400">Sexo</dt>
                                <dd class="mt-1 text-sm text-blue-900 dark:text-white">{{ $alumno->sexo == 'M' ? 'Masculino' : 'Femenino' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-blue-500 dark:text-blue-400">Fecha de Nacimiento</dt>
                                <dd class="mt-1 text-sm text-blue-900 dark:text-white">{{ \Carbon\Carbon::parse($alumno->fecha_nacimiento)->format('d/m/Y') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-blue-900 dark:text-white border-b border-blue-200 pb-2 mb-4">Información Académica</h3>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-blue-500 dark:text-blue-400">Carrera</dt>
                                <dd class="mt-1 text-sm text-blue-900 dark:text-white">{{ $alumno->carrera->nombre ?? 'No asignada' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-blue-500 dark:text-blue-400">Correo Electrónico</dt>
                                <dd class="mt-1 text-sm text-blue-900 dark:text-white">{{ $alumno->email ?? 'No asignado' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Columna Derecha: Foto y Acciones -->
                <div class="w-full md:w-1/3 space-y-6">
                    <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4 border border-blue-100 dark:border-blue-800 flex flex-col items-center">
                        @if($alumno->foto)
                            <img src="{{ asset('storage/' . $alumno->foto) }}" alt="Foto de {{ $alumno->nombre }}" class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-blue-800 shadow-md">
                        @else
                            <div class="w-32 h-32 rounded-full bg-blue-200 dark:bg-blue-700 flex items-center justify-center border-4 border-white dark:border-blue-800 shadow-md">
                                <span class="text-blue-600 dark:text-blue-200 text-3xl font-bold">{{ substr($alumno->nombre, 0, 1) }}{{ substr($alumno->apaterno, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4 border border-blue-100 dark:border-blue-800">
                        <h4 class="font-semibold text-blue-900 dark:text-white mb-4">Acciones Rápidas</h4>
                        <div class="flex flex-col gap-3">
                            <a href="{{ route('alumno.edit', $alumno) }}" class="btn-primary text-center w-full">
                                Editar Información
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Materias Inscritas -->
        <div class="glass-card p-6 max-w-4xl mx-auto w-full">
            <div class="border-b border-blue-200 pb-4 mb-6 dark:border-blue-700 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-blue-900 dark:text-white">
                        Materias Inscritas
                    </h2>
                    <p class="text-sm text-blue-600 dark:text-blue-300">Progreso: {{ $alumno->materias->count() }}/5 materias</p>
                </div>
            </div>

            @if($alumno->materias->count() >= 5)
                <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                    <p class="font-medium">Límite alcanzado</p>
                    <p class="text-sm">El alumno ya tiene inscrito el máximo de 5 materias.</p>
                </div>
            @else
                <div class="mb-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 border border-blue-100 dark:border-blue-800">
                    <h3 class="text-md font-medium text-blue-900 dark:text-white mb-4">Inscribir Nueva Materia</h3>
                    <form action="{{ route('alumno.enroll', $alumno) }}" method="post" class="flex flex-col sm:flex-row gap-4 items-end">
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
                        <div class="flex-1 w-full">
                            <label for="materia_id" class="block text-sm font-medium text-blue-700 dark:text-blue-300 mb-1">Seleccionar Materia</label>
                            <select name="materia_id" id="materia_id" required class="w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-zinc-800 dark:border-blue-700 dark:text-white">
                                <option value="">-- Seleccione una materia --</option>
                                @foreach($materiasDisponibles as $materia)
                                    <option value="{{ $materia->id }}">{{ $materia->nombre }} ({{ $materia->creditos }} créditos)</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn-primary w-full sm:w-auto">
                            Inscribir
                        </button>
                    </form>
                </div>
            @endif

            <div class="overflow-hidden rounded-lg border border-blue-200 dark:border-blue-800">
                <table class="min-w-full divide-y divide-blue-200 dark:divide-blue-800">
                    <thead class="bg-blue-50 dark:bg-blue-900/50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider dark:text-blue-200">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider dark:text-blue-200">Materia</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider dark:text-blue-200">Créditos</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider dark:text-blue-200">Calificación</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-blue-800 uppercase tracking-wider dark:text-blue-200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-blue-100 dark:bg-zinc-900 dark:divide-blue-800">
                        @forelse ($alumno->materias as $materia)
                            <tr class="hover:bg-blue-50/50 dark:hover:bg-blue-900/20 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-900 dark:text-white font-medium">
                                    {{ $materia->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-900 dark:text-white">
                                    {{ $materia->nombre }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700 dark:text-blue-300">
                                    {{ $materia->creditos }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ isset($materia->pivot->calificacion) ? ($materia->pivot->calificacion >= 70 ? 'text-green-600' : 'text-red-600') : 'text-gray-400' }}">
                                    {{ $materia->pivot->calificacion ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form action="{{ route('inscripcion.destroy') }}" method="post" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="alumno_id" value="{{ $alumno->id }}">
                                        <input type="hidden" name="materia_id" value="{{ $materia->id }}">
                                        <button type="submit" onclick="return confirm('¿Estás seguro de desinscribir esta materia?')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                            Desinscribir
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-blue-500 dark:text-blue-400">
                                    <p class="text-lg">No hay materias inscritas</p>
                                    <p class="text-sm mt-1">Utiliza el formulario de arriba para inscribir materias.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
