<x-layouts.app :title="__('Detalles del Docente')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Detalles del Docente</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Información completa del profesor</p>
            </div>
            <a href="{{ route('docente.index') }}" class="btn-secondary" wire:navigate>
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
                            <div>
                                <dt class="text-sm font-medium text-blue-500 dark:text-blue-400">Nombre Completo</dt>
                                <dd class="mt-1 text-sm text-blue-900 dark:text-white font-semibold">{{ $docente->nombre }} {{ $docente->apaterno }} {{ $docente->amaterno }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-blue-500 dark:text-blue-400">Matrícula</dt>
                                <dd class="mt-1 text-sm text-blue-900 dark:text-white">{{ $docente->matricula }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-blue-500 dark:text-blue-400">Edad</dt>
                                <dd class="mt-1 text-sm text-blue-900 dark:text-white">{{ $docente->edad }} años</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-blue-500 dark:text-blue-400">Sexo</dt>
                                <dd class="mt-1 text-sm text-blue-900 dark:text-white">{{ $docente->sexo == 'M' ? 'Masculino' : 'Femenino' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-blue-900 dark:text-white border-b border-blue-200 pb-2 mb-4">Información Académica</h3>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-blue-500 dark:text-blue-400">Carrera (Departamento)</dt>
                                <dd class="mt-1 text-sm text-blue-900 dark:text-white">{{ $docente->carrera->nombre ?? 'No asignada' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-blue-500 dark:text-blue-400">Correo Electrónico</dt>
                                <dd class="mt-1 text-sm text-blue-900 dark:text-white">{{ $docente->usuario->email }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Columna Derecha: Acciones y Estadísticas -->
                <div class="w-full md:w-1/3 space-y-6">
                    <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4 border border-blue-100 dark:border-blue-800">
                        <h4 class="font-semibold text-blue-900 dark:text-white mb-4">Acciones Rápidas</h4>
                        <div class="flex flex-col gap-3">
                            <a href="{{ route('docente.edit', $docente) }}" class="btn-primary text-center w-full">
                                Editar Información
                            </a>
                            <a href="{{ route('docente.materias', $docente) }}" class="btn-blue text-center w-full">
                                Ver Materias Asignadas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>