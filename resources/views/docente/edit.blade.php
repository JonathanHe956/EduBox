<x-layouts.app :title="_('Editar Docente')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Editar Docente</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Modifica la información del docente</p>
            </div>
            <a href="{{ route('docente.index') }}" class="btn-secondary">
                Volver
            </a>
        </div>

        <div class="glass-card p-6 max-w-4xl mx-auto w-full">
            <script>
                function validarTexto(input) {
                    input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
                }

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
                            document.getElementById('email').value = email;
                        }
                    }
                }
            </script>
            <form action="{{ route('docente.update', $docente) }}" method="POST" enctype="multipart/form-data" onsubmit="return validarFormulario()">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-blue-700 dark:text-blue-300">Nombre</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $docente->nombre) }}" class="mt-1 block w-full rounded-md border border-blue-200 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white" required oninput="validarTexto(this); generarEmail()">
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="apaterno" class="block text-sm font-medium text-blue-700 dark:text-blue-300">Apellido Paterno</label>
                        <input type="text" name="apaterno" id="apaterno" value="{{ old('apaterno', $docente->apaterno) }}" class="mt-1 block w-full rounded-md border border-blue-200 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white" oninput="validarTexto(this); generarEmail()">
                        @error('apaterno')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="amaterno" class="block text-sm font-medium text-blue-700 dark:text-blue-300">Apellido Materno</label>
                        <input type="text" name="amaterno" id="amaterno" value="{{ old('amaterno', $docente->amaterno) }}" class="mt-1 block w-full rounded-md border border-blue-200 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white" oninput="validarTexto(this)">
                        @error('amaterno')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sexo" class="block text-sm font-medium text-blue-700 dark:text-blue-300">Sexo</label>
                        <select name="sexo" id="sexo" class="mt-1 block w-full rounded-md border border-blue-200 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white" required>
                            <option value="">Seleccione</option>
                            <option value="M" {{ old('sexo', $docente->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sexo', $docente->sexo) == 'F' ? 'selected' : '' }}>Femenino</option>
                        </select>
                        @error('sexo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="fecha_nacimiento" class="block text-sm font-medium text-blue-700 dark:text-blue-300">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="{{ old('fecha_nacimiento', $docente->fecha_nacimiento) }}" class="mt-1 block w-full rounded-md border border-blue-200 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white" required onchange="calcularEdad(); generarEmail()">
                        @error('fecha_nacimiento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="edad" class="block text-sm font-medium text-blue-700 dark:text-blue-300">Edad</label>
                        <input type="number" name="edad" id="edad" value="{{ old('edad', $docente->edad) }}" class="mt-1 block w-full rounded-md border border-blue-200 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white" required>
                        @error('edad')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="foto" class="block text-sm font-medium text-blue-700 dark:text-blue-300">Foto</label>
                        @if($docente->foto)
                            <img src="{{ asset('storage/' . $docente->foto) }}" alt="Foto actual" class="w-20 h-20 object-cover mb-2 rounded-full border-2 border-blue-200 dark:border-blue-800">
                        @endif
                        <input type="file" name="foto" id="foto" class="mt-1 block w-full rounded-md border border-blue-200 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white" accept="image/*">
                        @error('foto')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-blue-700 dark:text-blue-300">Email (generado automáticamente)</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $docente->email) }}" class="mt-1 block w-full rounded-md border border-blue-200 px-3 py-2 shadow-sm bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800 dark:text-white" readonly>
                        <p class="mt-1 text-sm text-blue-500 dark:text-blue-400">El email se genera automáticamente basado en el nombre y apellido paterno, o fecha de nacimiento si no hay apellido, el correo puede sufrir cambios al momento de guardarse depende a la disponibilidad.</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="carrera_id" class="block text-sm font-medium text-blue-700 dark:text-blue-300">Carrera</label>
                        
                        @if($docente->materias()->exists())
                            <div class="rounded-md bg-yellow-50 p-4 mb-2 dark:bg-yellow-900/20">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                            No se puede cambiar la carrera porque el docente tiene materias asignadas.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="carrera_id" value="{{ $docente->carrera_id }}">
                        @endif

                        <select name="carrera_id" id="carrera_id" class="mt-1 block w-full rounded-md border border-blue-200 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-blue-800 dark:bg-zinc-800 dark:text-white disabled:opacity-50 disabled:bg-gray-100 dark:disabled:bg-zinc-900" required {{ $docente->materias()->exists() ? 'disabled' : '' }}>
                            <option value="">Seleccione una carrera</option>
                            @foreach($carreras as $carrera)
                                <option value="{{ $carrera->id }}" {{ old('carrera_id', $docente->carrera_id) == $carrera->id ? 'selected' : '' }}>{{ $carrera->nombre }}</option>
                            @endforeach
                        </select>
                        @error('carrera_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" class="btn-primary">
                        Actualizar Docente
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
