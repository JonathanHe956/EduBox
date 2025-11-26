<x-layouts.app :title="_('Editar Materia')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Editar Materia</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Actualizar información de la materia</p>
            </div>
            <a href="{{ route('materia.index') }}" class="btn-secondary">
                Volver
            </a>
        </div>

        <div class="glass-card p-6 max-w-4xl mx-auto w-full">
            <form action="{{ route('materia.update', $materia->id) }}" method="post" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-blue-700 dark:text-blue-300">Nombre</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $materia->nombre) }}" class="mt-1 block w-full rounded-md border border-blue-200 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white" required>
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="creditos" class="block text-sm font-medium text-blue-700 dark:text-blue-300">Créditos</label>
                        <input type="number" name="creditos" id="creditos" value="{{ old('creditos', $materia->creditos) }}" min="4" max="5" class="mt-1 block w-full rounded-md border border-blue-200 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white" required oninput="validarCreditosMateria(this)">
                        <p class="mt-1 text-xs text-blue-500 dark:text-blue-400">La materia debe tener entre 4 y 5 créditos</p>
                        @error('creditos')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p id="creditos-error" class="mt-1 text-sm text-red-600 dark:text-red-400 hidden"></p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="carrera_id" class="block text-sm font-medium text-blue-700 dark:text-blue-300">Carrera</label>
                        
                        @if($materia->alumnos()->exists() || $materia->docente_id)
                            <div class="rounded-md bg-yellow-50 p-4 mb-2 dark:bg-yellow-900/20">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                            No se puede cambiar la carrera porque hay alumnos inscritos o un docente asignado en esta materia.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="carrera_id" value="{{ $materia->carrera_id }}">
                        @endif

                        <select name="carrera_id" id="carrera_id" class="mt-1 block w-full rounded-md border border-blue-200 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white disabled:opacity-50 disabled:bg-gray-100 dark:disabled:bg-zinc-900" required {{ ($materia->alumnos()->exists() || $materia->docente_id) ? 'disabled' : '' }}>
                            <option value="">Seleccionar Carrera</option>
                            @foreach($carreras as $carrera)
                                <option value="{{ $carrera->id }}" {{ old('carrera_id', $materia->carrera_id) == $carrera->id ? 'selected' : '' }}>{{ $carrera->nombre }}</option>
                            @endforeach
                        </select>
                        @error('carrera_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">
                        Actualizar Materia
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function validarCreditosMateria(input) {
            const valor = parseInt(input.value);
            const elementoError = document.getElementById('creditos-error');
            
            if (isNaN(valor)) {
                elementoError.textContent = '';
                elementoError.classList.add('hidden');
                return;
            }
            
            if (valor < 4) {
                elementoError.textContent = 'La materia debe tener un mínimo de 4 créditos.';
                elementoError.classList.remove('hidden');
            } else if (valor > 5) {
                elementoError.textContent = 'La materia no puede exceder los 5 créditos.';
                elementoError.classList.remove('hidden');
            } else {
                elementoError.textContent = '';
                elementoError.classList.add('hidden');
            }
        }

        document.querySelector('form').addEventListener('submit', function(e) {
            const creditos = parseInt(document.getElementById('creditos').value);
            if (creditos < 4 || creditos > 5) {
                e.preventDefault();
                alert('Por favor, ingresa un valor de créditos entre 4 y 5.');
                return false;
            }
        });
    </script>
</x-layouts.app>
