<x-layouts.app :title="__('Panel de Control')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-white drop-shadow-lg">Bienvenido a EduBox</h1>
            @if(auth()->user()->hasRole('admin'))
                <p class="mt-2 text-blue-100">Administra tus recursos académicos de manera muy eficiente.</p>
            @elseif(auth()->user()->hasRole('estudiante'))
                <p class="mt-2 text-blue-100">Esperamos que la plataforma sea de gran utilidad para ti.</p>
            @elseif(auth()->user()->hasRole('docente'))
                <p class="mt-2 text-blue-100">Esperamos que la plataforma sea de gran utilidad para ti y tus asignaciones.</p>
            @endif
        </div>        

        @if(auth()->user()->hasRole('admin'))
        <div class="glass-card p-6">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-white">Estadísticas Rápidas</h3>
                <div class="mt-4 grid gap-4 md:grid-cols-4">
                    <div class="text-center p-4 bg-blue-800/30 rounded-lg border border-blue-700/30">
                        <div class="text-3xl font-bold text-gold-500">{{ \App\Models\carrera::count() }}</div>
                        <div class="text-blue-700 dark:text-blue-200 font-medium">Total Carreras</div>
                    </div>
                    <div class="text-center p-4 bg-blue-800/30 rounded-lg border border-blue-700/30">
                        <div class="text-3xl font-bold text-gold-500">{{ \App\Models\materia::count() }}</div>
                        <div class="text-blue-700 dark:text-blue-200 font-medium">Total Materias</div>
                    </div>
                    <div class="text-center p-4 bg-blue-800/30 rounded-lg border border-blue-700/30">
                        <div class="text-3xl font-bold text-gold-500">{{ \App\Models\docente::count() }}</div>
                        <div class="text-blue-700 dark:text-blue-200 font-medium">Total Docentes</div>
                    </div>
                    <div class="text-center p-4 bg-blue-800/30 rounded-lg border border-blue-700/30">
                        <div class="text-3xl font-bold text-gold-500">{{ \App\Models\alumno::count() }}</div>
                        <div class="text-blue-700 dark:text-blue-200 font-medium">Total Alumnos</div>
                    </div>
                </div>
            </div>
            <div class="grid gap-6 md:grid-cols-4">
                <div class="glass-card p-6 transform transition-all duration-300 hover:scale-105">
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-white">Carreras</h3>
                    <p class="mt-2 text-blue-700 dark:text-blue-200">Administrar programas académicos.</p>
                    <a href="{{ route('carrera.index') }}" class="mt-4 inline-block btn-primary" wire:navigate>Ver Carreras</a>
                </div>

                <div class="glass-card p-6 transform transition-all duration-300 hover:scale-105">
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-white">Materias</h3>
                    <p class="mt-2 text-blue-700 dark:text-blue-200">Manejar asignaturas del curso.</p>
                    <a href="{{ route('materia.index') }}" class="mt-4 inline-block btn-primary" wire:navigate>Ver Materias</a>
                </div>

                <div class="glass-card p-6 transform transition-all duration-300 hover:scale-105">
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-white">Alumnos</h3>
                    <p class="mt-2 text-blue-700 dark:text-blue-200">Supervisar registros de estudiantes.</p>
                    <a href="{{ route('alumno.index') }}" class="mt-4 inline-block btn-primary" wire:navigate>Ver Alumnos</a>
                </div>

                <div class="glass-card p-6 transform transition-all duration-300 hover:scale-105">
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-white">Docentes</h3>
                    <p class="mt-2 text-blue-700 dark:text-blue-200">Gestionar el personal docente.</p>
                    <a href="{{ route('docente.index') }}" class="mt-4 inline-block btn-primary" wire:navigate>Ver Docentes</a>
                </div>
            </div>
        @endif

        @if(auth()->user()->hasRole('estudiante') || auth()->user()->hasRole('docente'))
            <div class="glass-card p-6">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-white">Mis Materias</h3>
                <p class="mt-2 text-blue-700 dark:text-blue-200">Accede a tus asignaturas inscritas.</p>
                <a href="{{ route('mis.materias') }}" class="mt-4 inline-block btn-blue" wire:navigate>Ver Mis Materias</a>
            </div>
            <div class="glass-card p-6">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-white">Actividad Reciente</h3>
                <div class="mt-4 space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-2 h-2 bg-gold-500 rounded-full"></div>
                        <div>
                            <p class="text-sm text-blue-900 dark:text-white font-medium">Bienvenido al sistema de gestión académica</p>
                            <p class="text-xs text-blue-700 dark:text-blue-300">Puedes ver estadísticas e información del sistema aquí</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="w-2 h-2 bg-bronze-500 rounded-full"></div>
                        <div>
                            <p class="text-sm text-blue-900 dark:text-white font-medium">El sistema está funcionando correctamente</p>
                            <p class="text-xs text-blue-700 dark:text-blue-300">Todos los recursos académicos están actualizados</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <div>
                            <p class="text-sm text-blue-900 dark:text-white font-medium">Contacta a tu administrador para acceso</p>
                            <p class="text-xs text-blue-700 dark:text-blue-300">Para funciones de gestión, por favor contacta a un administrador</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
