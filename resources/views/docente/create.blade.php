<x-layouts.app :title="_('Crear Docente')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Crear Nuevo Docente</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Agrega un nuevo docente al sistema</p>
            </div>
            <a href="{{ route('docente.index') }}" class="inline-flex items-center rounded-md bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                Volver
            </a>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-zinc-900">
            <script>
                function calcularEdad() {
                    const fechaNacimiento = document.getElementById('fecha_nacimiento').value;
                    if (fechaNacimiento) {
                        const nacimiento = new Date(fechaNacimiento);
                        const hoy = new Date();
                        let edad = hoy.getFullYear() - nacimiento.getFullYear();
                        const mes = hoy.getMonth() - nacimiento.getMonth();
                        if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
                            edad--;
                        }
                        document.getElementById('edad').value = edad;
                    }
                }

                function validarFormulario() {
                    const edad = parseInt(document.getElementById('edad').value);
                    if (edad > 80) {
                        alert('La edad no puede ser mayor a 80 años.');
                        return false;
                    }
                    if (edad < 17) {
                        alert('La edad no puede ser menor a 17 años.');
                        return false;
                    }
                    return true;
                }

                function generarEmail() {
                    let nombre = document.getElementById('nombre').value.trim();
                    const apaterno = document.getElementById('apaterno').value.trim();
                    const fechaNacimiento = document.getElementById('fecha_nacimiento').value;
                    
                    if (nombre) {
                        // Use only the first name
                        nombre = nombre.split(' ')[0];

                        let suffix = apaterno ? apaterno.toLowerCase() : (fechaNacimiento ? new Date(fechaNacimiento).getFullYear() : '');
                        if (suffix) {
                            let email = nombre.toLowerCase() + '.' + suffix + '@example.com';
                            let counter = 1;
                            // Simulate checking for duplicates (in real implementation, this would be done server-side)
                            while (document.getElementById('email').dataset.usedEmails && document.getElementById('email').dataset.usedEmails.includes(email)) {
                                email = nombre.toLowerCase() + '.' + suffix + counter + '@example.com';
                                counter++;
                            }
                            document.getElementById('email').value = email;
                        }
                    }
                }
            </script>
            <form action="{{ route('docente.store') }}" method="post" enctype="multipart/form-data" class="space-y-6" onsubmit="return validarFormulario()">
                @csrf

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white" required oninput="generarEmail()">
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="apaterno" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellido Paterno</label>
                        <input type="text" name="apaterno" id="apaterno" value="{{ old('apaterno') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white" oninput="generarEmail()">
                        @error('apaterno')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="amaterno" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellido Materno</label>
                        <input type="text" name="amaterno" id="amaterno" value="{{ old('amaterno') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white" required>
                        @error('amaterno')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sexo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sexo</label>
                        <select name="sexo" id="sexo" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white" required>
                            <option value="">Seleccionar</option>
                            <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>Femenino</option>
                        </select>
                        @error('sexo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white" required onchange="calcularEdad()">
                        @error('fecha_nacimiento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="edad" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Edad (calculada automáticamente)</label>
                        <input type="number" name="edad" id="edad" value="{{ old('edad') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white" readonly>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">La edad se calcula automáticamente basada en la fecha de nacimiento</p>
                    </div>

                    <div>
                        <label for="foto" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto</label>
                        <input type="file" name="foto" id="foto" accept="image/*" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white">
                        @error('foto')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email (generado automáticamente)</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white" readonly>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">El email se genera automáticamente con nombre.apellido@example.com</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="carrera_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Carrera</label>
                        <select name="carrera_id" id="carrera_id" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white" required>
                            <option value="">Seleccionar Carrera</option>
                            @foreach($carreras as $carrera)
                                <option value="{{ $carrera->id }}" {{ old('carrera_id') == $carrera->id ? 'selected' : '' }}>{{ $carrera->nombre }}</option>
                            @endforeach
                        </select>
                        @error('carrera_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Crear Docente
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
