<x-layouts.app :title="_('Crear Carrera')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        {{-- Encabezado --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Crear Nueva Carrera</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Agrega una nueva carrera al sistema</p>
            </div>
        </div>

        {{-- Formulario --}}
        <div class="glass-card p-6 max-w-4xl mx-auto w-full">
            <form action="{{ route('carrera.store') }}" method="post" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-blue-700 dark:text-blue-300">Nombre de la Carrera</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                               class="mt-1 block w-full rounded-md border border-blue-200 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white">
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="creditos" class="block text-sm font-medium text-blue-700 dark:text-blue-300">Créditos Totales</label>
                        <input type="number" name="creditos" id="creditos" value="{{ old('creditos') }}" min="250" max="300" required
                               class="mt-1 block w-full rounded-md border border-blue-200 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white"
                               oninput="validarCreditos(this)">
                        <p class="mt-1 text-xs text-blue-500 dark:text-blue-400">La carrera debe tener entre 250 y 300 créditos</p>
                        @error('creditos')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p id="creditos-error" class="mt-1 text-sm text-red-600 dark:text-red-400 hidden"></p>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('carrera.index') }}" class="btn-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        Crear Carrera
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function validarCreditos(input) {
            const valor = parseInt(input.value);
            const elementoError = document.getElementById('creditos-error');
            
            if (isNaN(valor)) {
                elementoError.textContent = '';
                elementoError.classList.add('hidden');
                return;
            }
            
            if (valor < 250) {
                elementoError.textContent = 'La carrera debe tener un mínimo de 250 créditos.';
                elementoError.classList.remove('hidden');
            } else if (valor > 300) {
                elementoError.textContent = 'La carrera no puede exceder los 300 créditos.';
                elementoError.classList.remove('hidden');
            } else {
                elementoError.textContent = '';
                elementoError.classList.add('hidden');
            }
        }

        document.querySelector('form').addEventListener('submit', function(e) {
            const creditos = parseInt(document.getElementById('creditos').value);
            if (creditos < 250 || creditos > 300) {
                e.preventDefault();
                alert('Por favor, ingresa un valor de créditos entre 250 y 300.');
                return false;
            }
        });
    </script>
</x-layouts.app>
