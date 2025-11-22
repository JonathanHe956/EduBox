<x-layouts.app :title="_('Editar Materia')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Editar Materia</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Actualizar información de la materia</p>
            </div>
            <a href="{{ route('materia.index') }}" class="inline-flex items-center rounded-md bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                Volver
            </a>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-zinc-900">
            <form action="{{ route('materia.update', $materia->id) }}" method="post" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $materia->nombre) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white" required>
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="creditos" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Créditos</label>
                        <input type="number" name="creditos" id="creditos" value="{{ old('creditos', $materia->creditos) }}" min="4" max="5" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white" required oninput="validarCreditosMateria(this)">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">La materia debe tener entre 4 y 5 créditos</p>
                        @error('creditos')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p id="creditos-error" class="mt-1 text-sm text-red-600 dark:text-red-400 hidden"></p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="carrera_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Carrera</label>
                        <select name="carrera_id" id="carrera_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white" required>
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
                    <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Actualizar Materia
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function validarCreditosMateria(input) {
            const valor = parseInt(input.value);
            const errorElement = document.getElementById('creditos-error');
            
            if (isNaN(valor)) {
                errorElement.textContent = '';
                errorElement.classList.add('hidden');
                return;
            }
            
            if (valor < 4) {
                errorElement.textContent = 'La materia debe tener un mínimo de 4 créditos.';
                errorElement.classList.remove('hidden');
            } else if (valor > 5) {
                errorElement.textContent = 'La materia no puede exceder los 5 créditos.';
                errorElement.classList.remove('hidden');
            } else {
                errorElement.textContent = '';
                errorElement.classList.add('hidden');
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
