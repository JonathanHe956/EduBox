<x-layouts.app :title="_('Crear Materia')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        {{-- Encabezado --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Crear Nueva Materia</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Agrega una nueva materia al sistema</p>
            </div>
        </div>

        {{-- Formulario --}}
        <div class="glass-card p-6 max-w-4xl mx-auto w-full">
            @if($carreras->isEmpty())
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                    <p>No hay carreras disponibles. Por favor, crea una carrera antes de agregar una materia.</p>
                </div>
            @else
                <form action="{{ route('materia.store') }}" method="post" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-blue-700 dark:text-blue-300">Nombre de la Materia</label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                                   class="mt-1 block w-full rounded-md border border-blue-200 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white">
                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="creditos" class="block text-sm font-medium text-blue-700 dark:text-blue-300">Créditos</label>
                            <input type="number" name="creditos" id="creditos" value="{{ old('creditos') }}" min="4" max="5" required
                                   class="mt-1 block w-full rounded-md border border-blue-200 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white"
                                   oninput="validarCreditosMateria(this)">
                            <p class="mt-1 text-xs text-blue-500 dark:text-blue-400">La materia debe tener entre 4 y 5 créditos</p>
                            @error('creditos')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p id="creditos-error" class="mt-1 text-sm text-red-600 dark:text-red-400 hidden"></p>
                        </div>
                    </div>

                    <div>
                        <label for="carrera_id" class="block text-sm font-medium text-blue-700 dark:text-blue-300">Carrera</label>
                        <select name="carrera_id" id="carrera_id" required
                                class="mt-1 block w-full rounded-md border border-blue-200 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white">
                            <option value="">Seleccionar carrera...</option>
                            @foreach ($carreras as $carrera)
                                <option value="{{ $carrera->id }}" {{ old('carrera_id') == $carrera->id ? 'selected' : '' }}>
                                    {{ $carrera->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('carrera_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('materia.index') }}" class="btn-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn-primary">
                            Crear Materia
                        </button>
                    </div>
                </form>
            @endif
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
