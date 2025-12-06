<x-layouts.app :title="__('Editar Examen')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Editar examen: {{ $examen->titulo }}</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Actualiza la información del examen</p>
            </div>
            <a href="{{ route('examenes.materia', $examen->materia) }}" class="btn-secondary" wire:navigate>
                Volver
            </a>
        </div>

        @if(session('success'))
            <div class="glass-card p-4 bg-green-50 border-green-200 dark:bg-green-900/20">
                <p class="text-green-600 dark:text-green-400">{{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="glass-card p-4 bg-red-50 border-red-200 dark:bg-red-900/20">
                <ul class="list-disc list-inside text-red-600 dark:text-red-400">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('examenes.update', $examen) }}" id="formulario-examen">
            @csrf
            @method('PUT')
            
            <div class="glass-card p-6 space-y-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-500 dark:text-gray-400">Título</label>
                        <input id="titulo" name="titulo" type="text" required maxlength="255" class="input-modern w-full" value="{{ old('titulo', $examen->titulo) }}">
                    </div>

                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-500 dark:text-gray-400">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="input-modern w-full" rows="3">{{ old('descripcion', $examen->descripcion) }}</textarea>
                    </div>
                </div>

                <hr class="border-gray-300 dark:border-gray-700">

                <div>
                    <div class="mb-4">
                        <h2 class="text-xl font-semibold text-blue-900 dark:text-white">Preguntas</h2>
                    </div>

                    <div id="contenedor-preguntas" class="space-y-6">
                        {{-- Preguntas existentes --}}
                        @foreach($examen->preguntas as $index => $pregunta)
                            <div class="bloque-pregunta glass-card p-4 relative" data-index="{{ $index }}">
                                <button type="button" class="btn-eliminar-pregunta absolute top-2 right-2 text-red-400 hover:text-red-600 p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Eliminar pregunta">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                <input type="hidden" name="preguntas[{{ $index }}][id]" value="{{ $pregunta->id }}">
                                <div class="mb-3">
                                    <label class="block font-medium text-sm mb-1">Pregunta <span class="numero-pregunta">{{ $index + 1 }}</span></label>
                                    <textarea name="preguntas[{{ $index }}][texto]" required class="border rounded w-full px-2 py-1">{{ $pregunta->pregunta }}</textarea>
                                </div>
                                
                                <!-- Selector de tipo de pregunta -->
                                <div class="mb-3">
                                    <label class="block font-medium text-sm mb-2">Tipo de pregunta</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="preguntas[{{ $index }}][tipo]" value="multiple" class="radio-tipo-pregunta mr-2" {{ $pregunta->tipo === 'multiple' ? 'checked' : '' }}>
                                            <span class="text-sm">Opción múltiple</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="preguntas[{{ $index }}][tipo]" value="verdadero_falso" class="radio-tipo-pregunta mr-2" {{ $pregunta->tipo === 'verdadero_falso' ? 'checked' : '' }}>
                                            <span class="text-sm">Verdadero/Falso</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="preguntas[{{ $index }}][tipo]" value="abierta" class="radio-tipo-pregunta mr-2" {{ $pregunta->tipo === 'abierta' ? 'checked' : '' }}>
                                            <span class="text-sm">Pregunta abierta</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Contenedor para opción múltiple -->
                                <div class="contenedor-opciones contenido-tipo-multiple" style="display: {{ $pregunta->tipo === 'multiple' ? 'block' : 'none' }};">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Opciones</label>
                                        <button type="button" class="btn-agregar-opcion text-sm text-indigo-600 hover:text-indigo-800 font-medium" data-question-index="{{ $index }}">
                                            + Agregar opción
                                        </button>
                                    </div>
                                    <div class="lista-opciones space-y-2 pl-4 border-l-2 border-blue-200/50 dark:border-blue-700/50">
                                        @foreach($pregunta->opciones as $optIndex => $opcion)
                                            <div class="flex items-center gap-2 item-opcion border border-transparent rounded p-1 transition-colors {{ $opcion->es_correcta ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-700' : '' }}" data-indice-opcion="{{ $optIndex }}">
                                                <input type="hidden" name="preguntas[{{ $index }}][opciones][{{ $optIndex }}][id]" value="{{ $opcion->id }}">
                                                <input type="checkbox" name="preguntas[{{ $index }}][opciones][{{ $optIndex }}][es_correcta]" value="1" {{ $opcion->es_correcta ? 'checked' : '' }} class="w-4 h-4 checkbox-opcion">
                                                <input type="text" name="preguntas[{{ $index }}][opciones][{{ $optIndex }}][texto]" value="{{ $opcion->opcion }}" class="border rounded px-2 py-1 flex-1 text-sm">
                                                <button type="button" class="btn-eliminar-opcion text-red-400 hover:text-red-600 p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" data-question-index="{{ $index }}" title="Eliminar opción">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Contenedor para verdadero/falso -->
                                <div class="contenido-tipo-verdadero-falso" style="display: {{ $pregunta->tipo === 'verdadero_falso' ? 'block' : 'none' }};">
                                    <label class="block font-medium text-sm mb-2">Respuesta correcta</label>
                                    <div class="space-y-2">
                                        @php
                                            $vfCorrecta = $pregunta->opciones->firstWhere('opcion', 'Verdadero')?->es_correcta ? 'verdadero' : 'falso';
                                        @endphp
                                        <label class="flex items-center cursor-pointer p-2 border rounded hover:bg-gray-50 dark:hover:bg-zinc-700">
                                            <input type="radio" name="preguntas[{{ $index }}][vf_correcta]" value="verdadero" class="mr-2" {{ $vfCorrecta === 'verdadero' ? 'checked' : '' }}>
                                            <span>Verdadero</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer p-2 border rounded hover:bg-gray-50 dark:hover:bg-zinc-700">
                                            <input type="radio" name="preguntas[{{ $index }}][vf_correcta]" value="falso" class="mr-2" {{ $vfCorrecta === 'falso' ? 'checked' : '' }}>
                                            <span>Falso</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Contenedor para pregunta abierta -->
                                <div class="contenido-tipo-abierta" style="display: {{ $pregunta->tipo === 'abierta' ? 'block' : 'none' }};">
                                    <label class="block font-medium text-sm mb-2">Respuesta esperada / Criterios de evaluación (opcional)</label>
                                    <textarea name="preguntas[{{ $index }}][respuesta_esperada]" class="border rounded w-full px-2 py-1 text-sm" rows="3" placeholder="Describe la respuesta esperada o los criterios para calificar esta pregunta...">{{ $pregunta->respuesta_correcta_abierta }}</textarea>
                                    <p class="text-xs text-gray-500 mt-1">Esta información te ayudará al momento de revisar las respuestas de los estudiantes.</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <button type="button" id="btn-agregar-pregunta" class="w-full py-2 border-2 border-dashed border-blue-300/50 text-blue-900 dark:text-white rounded hover:border-gold-500 hover:text-gold-400 font-medium transition-colors">
                            + Agregar Pregunta
                        </button>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('examenes.materia', $examen->materia_id) }}" class="btn-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </form>

        <template id="plantilla-pregunta">
            <div class="bloque-pregunta glass-card p-4 relative">
                <button type="button" class="btn-eliminar-pregunta absolute top-2 right-2 text-red-400 hover:text-red-600 p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Eliminar pregunta">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
                <div class="mb-3">
                    <label class="block font-medium text-sm mb-1">Pregunta <span class="numero-pregunta"></span></label>
                    <textarea name="preguntas[INDEX][texto]" required class="border rounded w-full px-2 py-1" placeholder="Escribe la pregunta aquí..."></textarea>
                </div>
                
                <!-- Selector de tipo de pregunta -->
                <div class="mb-3">
                    <label class="block font-medium text-sm mb-2">Tipo de pregunta</label>
                    <div class="flex gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="preguntas[INDEX][tipo]" value="multiple" class="radio-tipo-pregunta mr-2" checked>
                            <span class="text-sm">Opción múltiple</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="preguntas[INDEX][tipo]" value="verdadero_falso" class="radio-tipo-pregunta mr-2">
                            <span class="text-sm">Verdadero/Falso</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="preguntas[INDEX][tipo]" value="abierta" class="radio-tipo-pregunta mr-2">
                            <span class="text-sm">Pregunta abierta</span>
                        </label>
                    </div>
                </div>

                <!-- Contenedor para opción múltiple -->
                <div class="contenedor-opciones contenido-tipo-multiple">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Opciones</label>
                        <button type="button" class="btn-agregar-opcion text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            + Agregar opción
                        </button>
                    </div>
                    <div class="lista-opciones space-y-2 pl-4 border-l-2 border-blue-200/50 dark:border-blue-700/50">
                        {{-- Opciones inyectadas vía JS --}}
                    </div>
                </div>

                <!-- Contenedor para verdadero/falso -->
                <div class="contenido-tipo-verdadero-falso" style="display: none;">
                    <label class="block font-medium text-sm mb-2">Respuesta correcta</label>
                    <div class="space-y-2">
                        <label class="flex items-center cursor-pointer p-2 border rounded hover:bg-gray-50 dark:hover:bg-zinc-700">
                            <input type="radio" name="preguntas[INDEX][vf_correcta]" value="verdadero" class="mr-2">
                            <span>Verdadero</span>
                        </label>
                        <label class="flex items-center cursor-pointer p-2 border rounded hover:bg-gray-50 dark:hover:bg-zinc-700">
                            <input type="radio" name="preguntas[INDEX][vf_correcta]" value="falso" class="mr-2">
                            <span>Falso</span>
                        </label>
                    </div>
                </div>

                <!-- Contenedor para pregunta abierta -->
                <div class="contenido-tipo-abierta" style="display: none;">
                    <label class="block font-medium text-sm mb-2">Respuesta esperada / Criterios de evaluación (opcional)</label>
                    <textarea name="preguntas[INDEX][respuesta_esperada]" class="border rounded w-full px-2 py-1 text-sm" rows="3" placeholder="Describe la respuesta esperada o los criterios para calificar esta pregunta..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Esta información te ayudará al momento de revisar las respuestas de los estudiantes.</p>
                </div>
            </div>
        </template>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const contenedor = document.getElementById('contenedor-preguntas');
                const btnAgregarPregunta = document.getElementById('btn-agregar-pregunta');
                const plantilla = document.getElementById('plantilla-pregunta');
                
                // Inicializa el índice basado en las preguntas existentes
                let indicePregunta = {{ $examen->preguntas->count() }};

                function agregarOpcion(bloquePregunta, indicePregunta, indiceOpcion, esExistente = false) {
                    const listaOpciones = bloquePregunta.querySelector('.lista-opciones');
                    const divOpcion = document.createElement('div');
                    divOpcion.className = 'flex items-center gap-2 item-opcion';
                    divOpcion.dataset.indiceOpcion = indiceOpcion;
                    
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.name = `preguntas[${indicePregunta}][opciones][${indiceOpcion}][es_correcta]`;
                    checkbox.value = '1';
                    checkbox.className = 'w-4 h-4 checkbox-opcion';
                    
                    const text = document.createElement('input');
                    text.type = 'text';
                    text.name = `preguntas[${indicePregunta}][opciones][${indiceOpcion}][texto]`;
                    text.className = 'border rounded px-2 py-1 flex-1 text-sm';
                    text.placeholder = `Opción ${indiceOpcion + 1}`;

                    // Resaltar contenedor si es correcta
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            divOpcion.classList.add('bg-green-50', 'border-green-200', 'dark:bg-green-900/20', 'dark:border-green-700');
                            divOpcion.classList.remove('border-transparent');
                        } else {
                            divOpcion.classList.remove('bg-green-50', 'border-green-200', 'dark:bg-green-900/20', 'dark:border-green-700');
                            divOpcion.classList.add('border-transparent');
                        }
                    });

                    // Estado inicial
                    divOpcion.classList.add('border', 'border-transparent', 'rounded', 'p-1', 'transition-colors');

                    const btnEliminar = document.createElement('button');
                    btnEliminar.type = 'button';
                    btnEliminar.className = 'btn-eliminar-opcion text-red-400 hover:text-red-600 p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors';
                    btnEliminar.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>';
                    btnEliminar.title = "Eliminar opción";
                    btnEliminar.dataset.indicePregunta = indicePregunta;
                    btnEliminar.addEventListener('click', function() {
                        const itemsOpcion = bloquePregunta.querySelectorAll('.item-opcion');
                        if (itemsOpcion.length <= 2) {
                            alert('Cada pregunta debe tener al menos 2 opciones.');
                            return;
                        }
                        divOpcion.remove();
                        actualizarNumerosOpciones(bloquePregunta, indicePregunta);
                    });

                    divOpcion.appendChild(checkbox);
                    divOpcion.appendChild(text);
                    divOpcion.appendChild(btnEliminar);
                    listaOpciones.appendChild(divOpcion);
                }

                function actualizarNumerosOpciones(bloquePregunta, indicePregunta) {
                    const opciones = bloquePregunta.querySelectorAll('.item-opcion');
                    opciones.forEach((opcion, idx) => {
                        opcion.dataset.indiceOpcion = idx;
                        const checkbox = opcion.querySelector('input[type="checkbox"]');
                        const entradaTexto = opcion.querySelector('input[type="text"]');
                        const idOculto = opcion.querySelector('input[type="hidden"]');
                        
                        checkbox.name = `preguntas[${indicePregunta}][opciones][${idx}][es_correcta]`;
                        entradaTexto.name = `preguntas[${indicePregunta}][opciones][${idx}][texto]`;
                        entradaTexto.placeholder = `Opción ${idx + 1}`;
                        
                        if (idOculto) {
                            idOculto.name = `preguntas[${indicePregunta}][opciones][${idx}][id]`;
                        }
                    });
                }

                function manejarCambioTipoPregunta(bloquePregunta, indicePregunta) {
                    const radiosTipo = bloquePregunta.querySelectorAll('.radio-tipo-pregunta');
                    const contenidoMultiple = bloquePregunta.querySelector('.contenido-tipo-multiple');
                    const contenidoVF = bloquePregunta.querySelector('.contenido-tipo-verdadero-falso');
                    const contenidoAbierta = bloquePregunta.querySelector('.contenido-tipo-abierta');
                    
                    radiosTipo.forEach(radio => {
                        radio.addEventListener('change', function() {
                            // Oculta todos los tipos de contenido
                            contenidoMultiple.style.display = 'none';
                            contenidoVF.style.display = 'none';
                            contenidoAbierta.style.display = 'none';
                            
                            // Muestra el tipo seleccionado
                            if (this.value === 'multiple') {
                                contenidoMultiple.style.display = 'block';
                            } else if (this.value === 'verdadero_falso') {
                                contenidoVF.style.display = 'block';
                            } else if (this.value === 'abierta') {
                                contenidoAbierta.style.display = 'block';
                            }
                        });
                    });
                }

                function agregarPregunta() {
                    const clon = plantilla.content.cloneNode(true);
                    const bloque = clon.querySelector('.bloque-pregunta');
                    const spanNumero = clon.querySelector('.numero-pregunta');
                    const btnEliminarPregunta = clon.querySelector('.btn-eliminar-pregunta');
                    const btnAgregarOpcion = clon.querySelector('.btn-agregar-opcion');

                    // Usa un ID único para las nuevas preguntas para evitar colisiones de índices
                    const indiceUnico = 'new_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

                    bloque.dataset.index = indiceUnico;
                    
                    // Calcula el número visual basado en el conteo actual
                    const conteoActual = contenedor.querySelectorAll('.bloque-pregunta').length;
                    spanNumero.textContent = conteoActual + 1;

                    // Actualiza los atributos name para todos los inputs
                    const entradaTexto = clon.querySelector('textarea');
                    entradaTexto.name = entradaTexto.name.replace(/INDEX/g, indiceUnico);
                    
                    // Actualiza los nombres de los radio buttons
                    const radiosTipo = clon.querySelectorAll('.radio-tipo-pregunta');
                    radiosTipo.forEach(radio => {
                        radio.name = radio.name.replace(/INDEX/g, indiceUnico);
                    });
                    
                    // Actualiza los nombres de los radio buttons
                    const radiosVF = clon.querySelectorAll('input[name*="vf_correcta"]');
                    radiosVF.forEach(radio => {
                        radio.name = radio.name.replace(/INDEX/g, indiceUnico);
                    });
                    
                    // Actualiza el nombre del textarea de respuesta esperada
                    const respuestaEsperada = clon.querySelector('textarea[name*="respuesta_esperada"]');
                    if (respuestaEsperada) {
                        respuestaEsperada.name = respuestaEsperada.name.replace(/INDEX/g, indiceUnico);
                    }

                    // Agrega las opciones iniciales (minimum required) para el tipo multiple choice
                    for (let i = 0; i < 2; i++) {
                        agregarOpcion(bloque, indiceUnico, i);
                    }

                    // Manejador del botón de agregar opción
                    btnAgregarOpcion.addEventListener('click', function() {
                        const opcionesActuales = bloque.querySelectorAll('.item-opcion');
                        if (opcionesActuales.length >= 4) {
                            alert('Cada pregunta puede tener un máximo de 4 opciones.');
                            return;
                        }
                        agregarOpcion(bloque, indiceUnico, opcionesActuales.length);
                    });

                    btnEliminarPregunta.addEventListener('click', function() {
                        bloque.remove();
                        actualizarNumerosPregunta();
                    });
                    
                    // Manejador del cambio de tipo
                    manejarCambioTipoPregunta(bloque, indiceUnico);

                    contenedor.appendChild(bloque);
                    indicePregunta++;
                    actualizarNumerosPregunta();
                }

                function actualizarNumerosPregunta() {
                    const bloques = contenedor.querySelectorAll('.bloque-pregunta');
                    bloques.forEach((bloque, idx) => {
                        bloque.querySelector('.numero-pregunta').textContent = idx + 1;
                    });
                }

                btnAgregarPregunta.addEventListener('click', agregarPregunta);

                // Manejador del evento de eliminar para las preguntas existentes
                document.querySelectorAll('.btn-eliminar-pregunta').forEach(btn => {
                    btn.addEventListener('click', function() {
                        this.closest('.bloque-pregunta').remove();
                        actualizarNumerosPregunta();
                    });
                });

                // Manejador del cambio de tipo para las preguntas existentes
                document.querySelectorAll('.bloque-pregunta').forEach(bloque => {
                    const indicePregunta = bloque.dataset.index;
                    manejarCambioTipoPregunta(bloque, indicePregunta);
                });

                // Manejador del evento de agregar opción para las preguntas existentes
                document.querySelectorAll('.btn-agregar-opcion').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const bloquePregunta = this.closest('.bloque-pregunta');
                        const opcionesActuales = bloquePregunta.querySelectorAll('.item-opcion');
                        
                        if (opcionesActuales.length >= 4) {
                            alert('Cada pregunta puede tener un máximo de 4 opciones.');
                            return;
                        }
                        
                        const indicePregunta = bloquePregunta.dataset.index;
                        agregarOpcion(bloquePregunta, indicePregunta, opcionesActuales.length, true);
                    });
                });

                // Manejador del evento de eliminar opción para las preguntas existentes
                document.querySelectorAll('.btn-eliminar-opcion').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const bloquePregunta = this.closest('.bloque-pregunta');
                        const itemsOpcion = bloquePregunta.querySelectorAll('.item-opcion');
                        
                        if (itemsOpcion.length <= 2) {
                            alert('Cada pregunta debe tener al menos 2 opciones.');
                            return;
                        }
                        
                        const divOpcion = this.closest('.item-opcion');
                        const indicePregunta = this.dataset.indicePregunta;
                        divOpcion.remove();
                        actualizarNumerosOpciones(bloquePregunta, indicePregunta);
                    });
                });

                // Validación del formulario al enviar
                document.getElementById('formulario-examen').addEventListener('submit', function(e) {
                    const preguntas = contenedor.querySelectorAll('.bloque-pregunta');
                    let tieneError = false;
                    let mensajeError = '';

                    if (preguntas.length === 0) {
                        alert('El examen debe tener al menos una pregunta.');
                        e.preventDefault();
                        return false;
                    }

                    preguntas.forEach((pregunta, idx) => {
                        // Obtiene el tipo de pregunta seleccionado
                        const tipoSeleccionado = pregunta.querySelector('.radio-tipo-pregunta:checked');
                        if (!tipoSeleccionado) {
                            tieneError = true;
                            mensajeError = `La pregunta ${idx + 1} debe tener un tipo seleccionado.`;
                            return;
                        }

                        const tipoPregunta = tipoSeleccionado.value;

                        // Validación basada en el tipo de pregunta
                        if (tipoPregunta === 'multiple') {
                            // Solo verifica las opciones si se selecciona el tipo multiple choice
                            const contenidoMultiple = pregunta.querySelector('.contenido-tipo-multiple');
                            if (contenidoMultiple && contenidoMultiple.style.display !== 'none') {
                                const opciones = pregunta.querySelectorAll('.item-opcion');
                                if (opciones.length < 2) {
                                    tieneError = true;
                                    mensajeError = `La pregunta ${idx + 1} debe tener al menos 2 opciones.`;
                                    return;
                                }

                                const opcionesMarcadas = pregunta.querySelectorAll('.contenido-tipo-multiple input[type="checkbox"]:checked');
                                if (opcionesMarcadas.length === 0) {
                                    tieneError = true;
                                    mensajeError = `La pregunta ${idx + 1} debe tener al menos una respuesta correcta marcada.`;
                                    return;
                                }
                            }
                        } else if (tipoPregunta === 'verdadero_falso') {
                            // Solo verifica V/F si se selecciona el tipo verdadero/falso
                            const contenidoVF = pregunta.querySelector('.contenido-tipo-verdadero-falso');
                            if (contenidoVF && contenidoVF.style.display !== 'none') {
                                const vfMarcado = pregunta.querySelector('.contenido-tipo-verdadero-falso input[name*="vf_correcta"]:checked');
                                if (!vfMarcado) {
                                    tieneError = true;
                                    mensajeError = `La pregunta ${idx + 1} debe tener una respuesta correcta seleccionada (Verdadero o Falso).`;
                                    return;
                                }
                            }
                        }
                        // Las preguntas abiertas no requieren validación especial
                    });

                    if (tieneError) {
                        e.preventDefault();
                        alert(mensajeError);
                        return false;
                    }
                });
                // Inicializar listeners para opciones existentes
                document.querySelectorAll('.item-opcion').forEach(item => {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.addEventListener('change', function() {
                            if (this.checked) {
                                item.classList.add('bg-green-50', 'border-green-200', 'dark:bg-green-900/20', 'dark:border-green-700');
                                item.classList.remove('border-transparent');
                            } else {
                                item.classList.remove('bg-green-50', 'border-green-200', 'dark:bg-green-900/20', 'dark:border-green-700');
                                item.classList.add('border-transparent');
                            }
                        });
                    }
                });
            });
        </script>
    </div>
</x-layouts.app>
