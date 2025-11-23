<x-layouts.app :title="_('Detalles de Carrera')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        {{-- Encabezado y Botones de Acción --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">{{ $carrera->nombre }}</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Detalles de la carrera y sus relaciones.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('carrera.edit', $carrera) }}" class="btn-primary">
                    Editar
                </a>
                <a href="{{ route('carrera.index') }}" class="btn-secondary">
                    Volver
                </a>
            </div>
        </div>

        {{-- Tarjeta de Información General --}}
        <div class="glass-card p-6">
            <h2 class="text-lg font-semibold text-blue-900 dark:text-white mb-4">Información General</h2>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-blue-500 dark:text-blue-300">Nombre de la Carrera</label>
                    <p class="mt-1 text-lg text-blue-900 dark:text-white">{{ $carrera->nombre }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-blue-500 dark:text-blue-300">Créditos</label>
                    <p class="mt-1 text-lg text-blue-900 dark:text-white">{{ $carrera->creditos }}</p>
                </div>
            </div>
        </div>

        {{-- Lista de Materias --}}
        <div class="glass-card overflow-hidden">
            <div class="border-b border-blue-100 px-6 py-4 dark:border-blue-800">
                <h2 class="text-lg font-semibold text-blue-900 dark:text-white">Materias de la Carrera</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="bg-gradient-to-r from-blue-700 to-blue-800 text-xs font-medium uppercase text-white">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nombre</th>
                            <th scope="col" class="px-6 py-3">Créditos</th>
                            <th scope="col" class="px-6 py-3">Docente Asignado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($carrera->materias as $materia)
                            <tr class="border-b border-blue-200/50 dark:border-blue-700/50 hover:bg-blue-50/50 dark:hover:bg-blue-900/30 transition-colors duration-150">
                                <td class="px-6 py-4 font-medium text-blue-900 dark:text-white">{{ $materia->nombre }}</td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">{{ $materia->creditos }}</td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">{{ $materia->docente->nombre ?? 'No asignado' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-8 text-center text-blue-500 dark:text-blue-300">No hay materias registradas en esta carrera.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Lista de Alumnos --}}
        <div class="glass-card overflow-hidden">
            <div class="border-b border-blue-100 px-6 py-4 dark:border-blue-800">
                <h2 class="text-lg font-semibold text-blue-900 dark:text-white">Alumnos en la Carrera</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="bg-gradient-to-r from-blue-700 to-blue-800 text-xs font-medium uppercase text-white">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nombre Completo</th>
                            <th scope="col" class="px-6 py-3">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($carrera->alumnos as $alumno)
                            <tr class="border-b border-blue-200/50 dark:border-blue-700/50 hover:bg-blue-50/50 dark:hover:bg-blue-900/30 transition-colors duration-150">
                                <td class="px-6 py-4 font-medium text-blue-900 dark:text-white">{{ $alumno->nombre }} {{ $alumno->apaterno }}</td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">{{ $alumno->email }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-6 py-8 text-center text-blue-500 dark:text-blue-300">No hay alumnos registrados en esta carrera.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Lista de Docentes --}}
        <div class="glass-card overflow-hidden">
            <div class="border-b border-blue-100 px-6 py-4 dark:border-blue-800">
                <h2 class="text-lg font-semibold text-blue-900 dark:text-white">Docentes de la Carrera</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="bg-gradient-to-r from-blue-700 to-blue-800 text-xs font-medium uppercase text-white">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nombre Completo</th>
                            <th scope="col" class="px-6 py-3">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($carrera->docentes as $docente)
                            <tr class="border-b border-blue-200/50 dark:border-blue-700/50 hover:bg-blue-50/50 dark:hover:bg-blue-900/30 transition-colors duration-150">
                                <td class="px-6 py-4 font-medium text-blue-900 dark:text-white">{{ $docente->nombre }} {{ $docente->apaterno }}</td>
                                <td class="px-6 py-4 text-blue-900 dark:text-white">{{ $docente->email }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-6 py-8 text-center text-blue-500 dark:text-blue-300">No hay docentes registrados en esta carrera.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>