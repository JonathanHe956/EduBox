<x-layouts.app :title="__('Editar Examen')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 dark:text-white">Editar examen: {{ $examen->titulo }}</h1>
                <p class="mt-1 text-blue-700 dark:text-blue-200">Actualiza la información del examen</p>
            </div>
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

        <form method="POST" action="{{ route('examenes.update', $examen) }}" id="exam-form">
            @csrf
            @method('PUT')
            
            <div class="glass-card p-6 space-y-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-500 dark:text-gray-400">Título</label>
                        <input id="title" name="title" type="text" required maxlength="255" class="input-modern w-full" value="{{ old('title', $examen->titulo) }}">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-500 dark:text-gray-400">Descripción</label>
                        <textarea id="description" name="description" class="input-modern w-full" rows="3">{{ old('description', $examen->descripcion) }}</textarea>
                    </div>
                </div>

                <hr class="border-gray-300 dark:border-gray-700">

                <div>
                    <div class="mb-4">
                        <h2 class="text-xl font-semibold text-blue-900 dark:text-white">Preguntas</h2>
                    </div>

                    <div id="questions-container" class="space-y-6">
                        {{-- Preguntas existentes --}}
                        @foreach($examen->preguntas as $index => $pregunta)
                            <div class="question-block glass-card p-4 relative" data-index="{{ $index }}">
                                <button type="button" class="remove-question-btn absolute top-2 right-2 text-red-400 hover:text-red-600 p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Eliminar pregunta">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $pregunta->id }}">
                                <div class="mb-3">
                                    <label class="block font-medium text-sm mb-1">Pregunta <span class="question-number">{{ $index + 1 }}</span></label>
                                    <textarea name="questions[{{ $index }}][text]" required class="border rounded w-full px-2 py-1">{{ $pregunta->pregunta }}</textarea>
                                </div>
                                
                                <!-- Selector de tipo de pregunta -->
                                <div class="mb-3">
                                    <label class="block font-medium text-sm mb-2">Tipo de pregunta</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="questions[{{ $index }}][tipo]" value="multiple" class="question-type-radio mr-2" {{ $pregunta->tipo === 'multiple' ? 'checked' : '' }}>
                                            <span class="text-sm">Opción múltiple</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="questions[{{ $index }}][tipo]" value="verdadero_falso" class="question-type-radio mr-2" {{ $pregunta->tipo === 'verdadero_falso' ? 'checked' : '' }}>
                                            <span class="text-sm">Verdadero/Falso</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="questions[{{ $index }}][tipo]" value="abierta" class="question-type-radio mr-2" {{ $pregunta->tipo === 'abierta' ? 'checked' : '' }}>
                                            <span class="text-sm">Pregunta abierta</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Contenedor para opción múltiple -->
                                <div class="options-container tipo-multiple-content" style="display: {{ $pregunta->tipo === 'multiple' ? 'block' : 'none' }};">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Opciones</label>
                                        <button type="button" class="add-option-btn text-sm text-indigo-600 hover:text-indigo-800 font-medium" data-question-index="{{ $index }}">
                                            + Agregar opción
                                        </button>
                                    </div>
                                    <div class="options-list space-y-2 pl-4 border-l-2 border-blue-200/50 dark:border-blue-700/50">
                                        @foreach($pregunta->opciones as $optIndex => $opcion)
                                            <div class="flex items-center gap-2 option-item border border-transparent rounded p-1 transition-colors {{ $opcion->es_correcta ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-700' : '' }}" data-option-index="{{ $optIndex }}">
                                                <input type="hidden" name="questions[{{ $index }}][options][{{ $optIndex }}][id]" value="{{ $opcion->id }}">
                                                <input type="checkbox" name="questions[{{ $index }}][options][{{ $optIndex }}][is_correct]" value="1" {{ $opcion->es_correcta ? 'checked' : '' }} class="w-4 h-4 option-checkbox">
                                                <input type="text" name="questions[{{ $index }}][options][{{ $optIndex }}][text]" value="{{ $opcion->opcion }}" class="border rounded px-2 py-1 flex-1 text-sm">
                                                <button type="button" class="remove-option-btn text-red-400 hover:text-red-600 p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" data-question-index="{{ $index }}" title="Eliminar opción">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Contenedor para verdadero/falso -->
                                <div class="tipo-verdadero-falso-content" style="display: {{ $pregunta->tipo === 'verdadero_falso' ? 'block' : 'none' }};">
                                    <label class="block font-medium text-sm mb-2">Respuesta correcta</label>
                                    <div class="space-y-2">
                                        @php
                                            $vfCorrecta = $pregunta->opciones->firstWhere('opcion', 'Verdadero')?->es_correcta ? 'verdadero' : 'falso';
                                        @endphp
                                        <label class="flex items-center cursor-pointer p-2 border rounded hover:bg-gray-50 dark:hover:bg-zinc-700">
                                            <input type="radio" name="questions[{{ $index }}][vf_correcta]" value="verdadero" class="mr-2" {{ $vfCorrecta === 'verdadero' ? 'checked' : '' }}>
                                            <span>Verdadero</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer p-2 border rounded hover:bg-gray-50 dark:hover:bg-zinc-700">
                                            <input type="radio" name="questions[{{ $index }}][vf_correcta]" value="falso" class="mr-2" {{ $vfCorrecta === 'falso' ? 'checked' : '' }}>
                                            <span>Falso</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Contenedor para pregunta abierta -->
                                <div class="tipo-abierta-content" style="display: {{ $pregunta->tipo === 'abierta' ? 'block' : 'none' }};">
                                    <label class="block font-medium text-sm mb-2">Respuesta esperada / Criterios de evaluación (opcional)</label>
                                    <textarea name="questions[{{ $index }}][respuesta_esperada]" class="border rounded w-full px-2 py-1 text-sm" rows="3" placeholder="Describe la respuesta esperada o los criterios para calificar esta pregunta...">{{ $pregunta->respuesta_correcta_abierta }}</textarea>
                                    <p class="text-xs text-gray-500 mt-1">Esta información te ayudará al momento de revisar las respuestas de los estudiantes.</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <button type="button" id="add-question-btn" class="w-full py-2 border-2 border-dashed border-blue-300/50 text-blue-900 dark:text-white rounded hover:border-gold-500 hover:text-gold-400 font-medium transition-colors">
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

        <template id="question-template">
            <div class="question-block glass-card p-4 relative">
                <button type="button" class="remove-question-btn absolute top-2 right-2 text-red-400 hover:text-red-600 p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Eliminar pregunta">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
                <div class="mb-3">
                    <label class="block font-medium text-sm mb-1">Pregunta <span class="question-number"></span></label>
                    <textarea name="questions[INDEX][text]" required class="border rounded w-full px-2 py-1" placeholder="Escribe la pregunta aquí..."></textarea>
                </div>
                
                <!-- Selector de tipo de pregunta -->
                <div class="mb-3">
                    <label class="block font-medium text-sm mb-2">Tipo de pregunta</label>
                    <div class="flex gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="questions[INDEX][tipo]" value="multiple" class="question-type-radio mr-2" checked>
                            <span class="text-sm">Opción múltiple</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="questions[INDEX][tipo]" value="verdadero_falso" class="question-type-radio mr-2">
                            <span class="text-sm">Verdadero/Falso</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="questions[INDEX][tipo]" value="abierta" class="question-type-radio mr-2">
                            <span class="text-sm">Pregunta abierta</span>
                        </label>
                    </div>
                </div>

                <!-- Contenedor para opción múltiple -->
                <div class="options-container tipo-multiple-content">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Opciones</label>
                        <button type="button" class="add-option-btn text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            + Agregar opción
                        </button>
                    </div>
                    <div class="options-list space-y-2 pl-4 border-l-2 border-blue-200/50 dark:border-blue-700/50">
                        {{-- Opciones inyectadas vía JS --}}
                    </div>
                </div>

                <!-- Contenedor para verdadero/falso -->
                <div class="tipo-verdadero-falso-content" style="display: none;">
                    <label class="block font-medium text-sm mb-2">Respuesta correcta</label>
                    <div class="space-y-2">
                        <label class="flex items-center cursor-pointer p-2 border rounded hover:bg-gray-50 dark:hover:bg-zinc-700">
                            <input type="radio" name="questions[INDEX][vf_correcta]" value="verdadero" class="mr-2">
                            <span>Verdadero</span>
                        </label>
                        <label class="flex items-center cursor-pointer p-2 border rounded hover:bg-gray-50 dark:hover:bg-zinc-700">
                            <input type="radio" name="questions[INDEX][vf_correcta]" value="falso" class="mr-2">
                            <span>Falso</span>
                        </label>
                    </div>
                </div>

                <!-- Contenedor para pregunta abierta -->
                <div class="tipo-abierta-content" style="display: none;">
                    <label class="block font-medium text-sm mb-2">Respuesta esperada / Criterios de evaluación (opcional)</label>
                    <textarea name="questions[INDEX][respuesta_esperada]" class="border rounded w-full px-2 py-1 text-sm" rows="3" placeholder="Describe la respuesta esperada o los criterios para calificar esta pregunta..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Esta información te ayudará al momento de revisar las respuestas de los estudiantes.</p>
                </div>
            </div>
        </template>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('questions-container');
                const addQuestionBtn = document.getElementById('add-question-btn');
                const template = document.getElementById('question-template');
                
                // Inicializa el índice basado en las preguntas existentes
                let indicePregunta = {{ $examen->preguntas->count() }};

                function agregarOpcion(questionBlock, questionIdx, optionIdx, isExisting = false) {
                    const optionsList = questionBlock.querySelector('.options-list');
                    const optDiv = document.createElement('div');
                    optDiv.className = 'flex items-center gap-2 option-item';
                    optDiv.dataset.optionIndex = optionIdx;
                    
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.name = `questions[${questionIdx}][options][${optionIdx}][is_correct]`;
                    checkbox.value = '1';
                    checkbox.className = 'w-4 h-4';
                    
                    const text = document.createElement('input');
                    text.type = 'text';
                    text.name = `questions[${questionIdx}][options][${optionIdx}][text]`;
                    text.className = 'border rounded px-2 py-1 flex-1 text-sm';
                    text.placeholder = `Opción ${optionIdx + 1}`;

                    // Resaltar contenedor si es correcta
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            optDiv.classList.add('bg-green-50', 'border-green-200', 'dark:bg-green-900/20', 'dark:border-green-700');
                            optDiv.classList.remove('border-transparent');
                        } else {
                            optDiv.classList.remove('bg-green-50', 'border-green-200', 'dark:bg-green-900/20', 'dark:border-green-700');
                            optDiv.classList.add('border-transparent');
                        }
                    });

                    // Estado inicial
                    optDiv.classList.add('border', 'border-transparent', 'rounded', 'p-1', 'transition-colors');

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-option-btn text-red-400 hover:text-red-600 p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors';
                    removeBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>';
                    removeBtn.title = "Eliminar opción";
                    removeBtn.dataset.questionIndex = questionIdx;
                    removeBtn.addEventListener('click', function() {
                        const optionItems = questionBlock.querySelectorAll('.option-item');
                        if (optionItems.length <= 2) {
                            alert('Cada pregunta debe tener al menos 2 opciones.');
                            return;
                        }
                        optDiv.remove();
                        actualizarNumerosOpciones(questionBlock, questionIdx);
                    });

                    optDiv.appendChild(checkbox);
                    optDiv.appendChild(text);
                    optDiv.appendChild(removeBtn);
                    optionsList.appendChild(optDiv);
                }

                function actualizarNumerosOpciones(questionBlock, questionIdx) {
                    const options = questionBlock.querySelectorAll('.option-item');
                    options.forEach((opt, idx) => {
                        opt.dataset.optionIndex = idx;
                        const checkbox = opt.querySelector('input[type="checkbox"]');
                        const textInput = opt.querySelector('input[type="text"]');
                        const hiddenId = opt.querySelector('input[type="hidden"]');
                        
                        checkbox.name = `questions[${questionIdx}][options][${idx}][is_correct]`;
                        textInput.name = `questions[${questionIdx}][options][${idx}][text]`;
                        textInput.placeholder = `Opción ${idx + 1}`;
                        
                        if (hiddenId) {
                            hiddenId.name = `questions[${questionIdx}][options][${idx}][id]`;
                        }
                    });
                }

                function manejarCambioTipoPregunta(questionBlock, questionIdx) {
                    const typeRadios = questionBlock.querySelectorAll('.question-type-radio');
                    const multipleContent = questionBlock.querySelector('.tipo-multiple-content');
                    const vfContent = questionBlock.querySelector('.tipo-verdadero-falso-content');
                    const abiertaContent = questionBlock.querySelector('.tipo-abierta-content');
                    
                    typeRadios.forEach(radio => {
                        radio.addEventListener('change', function() {
                            // Oculta todos los tipos de contenido
                            multipleContent.style.display = 'none';
                            vfContent.style.display = 'none';
                            abiertaContent.style.display = 'none';
                            
                            // Muestra el tipo seleccionado
                            if (this.value === 'multiple') {
                                multipleContent.style.display = 'block';
                            } else if (this.value === 'verdadero_falso') {
                                vfContent.style.display = 'block';
                            } else if (this.value === 'abierta') {
                                abiertaContent.style.display = 'block';
                            }
                        });
                    });
                }

                function agregarPregunta() {
                    const clone = template.content.cloneNode(true);
                    const block = clone.querySelector('.question-block');
                    const numberSpan = clone.querySelector('.question-number');
                    const removeQuestionBtn = clone.querySelector('.remove-question-btn');
                    const addOptionBtn = clone.querySelector('.add-option-btn');

                    // Usa un ID único para las nuevas preguntas para evitar colisiones de índices
                    const indiceUnico = 'new_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

                    block.dataset.index = indiceUnico;
                    
                    // Calcula el número visual basado en el conteo actual
                    const conteoActual = container.querySelectorAll('.question-block').length;
                    numberSpan.textContent = conteoActual + 1;

                    // Actualiza los atributos name para todos los inputs
                    const textInput = clone.querySelector('textarea');
                    textInput.name = textInput.name.replace(/INDEX/g, indiceUnico);
                    
                    // Actualiza los nombres de los radio buttons
                    const typeRadios = clone.querySelectorAll('.question-type-radio');
                    typeRadios.forEach(radio => {
                        radio.name = radio.name.replace(/INDEX/g, indiceUnico);
                    });
                    
                    // Actualiza los nombres de los radio buttons
                    const vfRadios = clone.querySelectorAll('input[name*="vf_correcta"]');
                    vfRadios.forEach(radio => {
                        radio.name = radio.name.replace(/INDEX/g, indiceUnico);
                    });
                    
                    // Actualiza el nombre del textarea de respuesta esperada
                    const respuestaEsperada = clone.querySelector('textarea[name*="respuesta_esperada"]');
                    if (respuestaEsperada) {
                        respuestaEsperada.name = respuestaEsperada.name.replace(/INDEX/g, indiceUnico);
                    }

                    // Agrega las opciones iniciales (minimum required) para el tipo multiple choice
                    for (let i = 0; i < 2; i++) {
                        agregarOpcion(block, indiceUnico, i);
                    }

                    // Manejador del botón de agregar opción
                    addOptionBtn.addEventListener('click', function() {
                        const currentOptions = block.querySelectorAll('.option-item');
                        if (currentOptions.length >= 4) {
                            alert('Cada pregunta puede tener un máximo de 4 opciones.');
                            return;
                        }
                        agregarOpcion(block, indiceUnico, currentOptions.length);
                    });

                    removeQuestionBtn.addEventListener('click', function() {
                        block.remove();
                        actualizarNumerosPregunta();
                    });
                    
                    // Manejador del cambio de tipo
                    manejarCambioTipoPregunta(block, indiceUnico);

                    container.appendChild(block);
                    indicePregunta++;
                    actualizarNumerosPregunta();
                }

                function actualizarNumerosPregunta() {
                    const blocks = container.querySelectorAll('.question-block');
                    blocks.forEach((block, idx) => {
                        block.querySelector('.question-number').textContent = idx + 1;
                    });
                }

                addQuestionBtn.addEventListener('click', agregarPregunta);

                // Manejador del evento de eliminar para las preguntas existentes
                document.querySelectorAll('.remove-question-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        this.closest('.question-block').remove();
                        actualizarNumerosPregunta();
                    });
                });

                // Manejador del cambio de tipo para las preguntas existentes
                document.querySelectorAll('.question-block').forEach(block => {
                    const questionIdx = block.dataset.index;
                    manejarCambioTipoPregunta(block, questionIdx);
                });

                // Manejador del evento de agregar opción para las preguntas existentes
                document.querySelectorAll('.add-option-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const questionBlock = this.closest('.question-block');
                        const currentOptions = questionBlock.querySelectorAll('.option-item');
                        
                        if (currentOptions.length >= 4) {
                            alert('Cada pregunta puede tener un máximo de 4 opciones.');
                            return;
                        }
                        
                        const questionIdx = questionBlock.dataset.index;
                        agregarOpcion(questionBlock, questionIdx, currentOptions.length, true);
                    });
                });

                // Manejador del evento de eliminar opción para las preguntas existentes
                document.querySelectorAll('.remove-option-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const questionBlock = this.closest('.question-block');
                        const optionItems = questionBlock.querySelectorAll('.option-item');
                        
                        if (optionItems.length <= 2) {
                            alert('Cada pregunta debe tener al menos 2 opciones.');
                            return;
                        }
                        
                        const optionItem = this.closest('.option-item');
                        const questionIdx = this.dataset.questionIndex;
                        optionItem.remove();
                        actualizarNumerosOpciones(questionBlock, questionIdx);
                    });
                });

                // Validación del formulario al enviar
                document.getElementById('exam-form').addEventListener('submit', function(e) {
                    const questions = container.querySelectorAll('.question-block');
                    let tieneError = false;
                    let mensajeError = '';



                    if (questions.length === 0) {
                        alert('El examen debe tener al menos una pregunta.');
                        e.preventDefault();
                        return false;
                    }

                    questions.forEach((question, idx) => {
                        // Obtiene el tipo de pregunta seleccionado
                        const tipoSeleccionado = question.querySelector('.question-type-radio:checked');
                        if (!tipoSeleccionado) {
                            tieneError = true;
                            mensajeError = `La pregunta ${idx + 1} debe tener un tipo seleccionado.`;
                            return;
                        }

                        const tipoPregunta = tipoSeleccionado.value;

                        // Validación basada en el tipo de pregunta
                        if (tipoPregunta === 'multiple') {
                            // Solo verifica las opciones si se selecciona el tipo multiple choice
                            const multipleContent = question.querySelector('.tipo-multiple-content');
                            if (multipleContent && multipleContent.style.display !== 'none') {
                                const options = question.querySelectorAll('.option-item');
                                if (options.length < 2) {
                                    tieneError = true;
                                    mensajeError = `La pregunta ${idx + 1} debe tener al menos 2 opciones.`;
                                    return;
                                }

                                const checkedOptions = question.querySelectorAll('.tipo-multiple-content input[type="checkbox"]:checked');
                                if (checkedOptions.length === 0) {
                                    tieneError = true;
                                    mensajeError = `La pregunta ${idx + 1} debe tener al menos una respuesta correcta marcada.`;
                                    return;
                                }
                            }
                        } else if (tipoPregunta === 'verdadero_falso') {
                            // Solo verifica V/F si se selecciona el tipo verdadero/falso
                            const vfContent = question.querySelector('.tipo-verdadero-falso-content');
                            if (vfContent && vfContent.style.display !== 'none') {
                                const vfChecked = question.querySelector('.tipo-verdadero-falso-content input[name*="vf_correcta"]:checked');
                                if (!vfChecked) {
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
                document.querySelectorAll('.option-item').forEach(item => {
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
