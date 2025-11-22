<x-layouts.app :title="__('Editar Examen')">
    <div class="px-4 py-6">
        <h1 class="text-2xl font-semibold">Editar examen: {{ $examen->titulo }}</h1>

        @if(session('success'))
            <div class="mt-4 text-green-600">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
                <ul class="list-disc list-inside text-red-600">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('exams.update', $examen) }}" class="mt-6" id="exam-form">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="title" class="block font-medium">Título</label>
                <input id="title" name="title" type="text" required maxlength="255" class="border rounded px-2 py-1 w-full" value="{{ old('title', $examen->titulo) }}">
            </div>

            <div class="mb-4">
                <label for="description" class="block font-medium">Descripción</label>
                <textarea id="description" name="description" class="border rounded px-2 py-1 w-full">{{ old('description', $examen->descripcion) }}</textarea>
            </div>

            <hr class="my-6 border-gray-300 dark:border-gray-700">

            <div class="mb-6">
                <div class="mb-4">
                    <h2 class="text-xl font-semibold">Preguntas</h2>
                </div>

                <div id="questions-container" class="space-y-6">
                    {{-- Existing questions --}}
                    @foreach($examen->preguntas as $index => $pregunta)
                        <div class="question-block border rounded p-4 bg-gray-50 dark:bg-zinc-800 relative" data-index="{{ $index }}">
                            <button type="button" class="remove-question-btn absolute top-2 right-2 text-red-500 hover:text-red-700 font-bold text-xl">
                                &times;
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
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Opciones (mínimo 2)</label>
                                    <button type="button" class="add-option-btn text-sm text-indigo-600 hover:text-indigo-800 font-medium" data-question-index="{{ $index }}">
                                        + Agregar opción
                                    </button>
                                </div>
                                <div class="options-list space-y-2 pl-4 border-l-2 border-blue-200/50 dark:border-blue-700/50">
                                    @foreach($pregunta->opciones as $optIndex => $opcion)
                                        <div class="flex items-center gap-2 option-item" data-option-index="{{ $optIndex }}">
                                            <input type="hidden" name="questions[{{ $index }}][options][{{ $optIndex }}][id]" value="{{ $opcion->id }}">
                                            <input type="checkbox" name="questions[{{ $index }}][options][{{ $optIndex }}][is_correct]" value="1" {{ $opcion->es_correcta ? 'checked' : '' }} class="w-4 h-4">
                                            <input type="text" name="questions[{{ $index }}][options][{{ $optIndex }}][text]" value="{{ $opcion->opcion }}" class="border rounded px-2 py-1 flex-1 text-sm">
                                            <button type="button" class="remove-option-btn text-red-500 hover:text-red-700 font-bold text-lg" data-question-index="{{ $index }}">&times;</button>
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
                    <button type="button" id="add-question-btn" class="w-full py-2 border-2 border-dashed border-gray-300 text-gray-600 rounded hover:border-indigo-500 hover:text-indigo-600 font-medium transition-colors">
                        + Agregar Pregunta
                    </button>
                </div>
            </div>

            <div class="flex justify-end">
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Guardar cambios</button>
            </div>
        </form>

        <template id="question-template">
            <div class="question-block border rounded p-4 bg-gray-50 dark:bg-zinc-800 relative">
                <button type="button" class="remove-question-btn absolute top-2 right-2 text-red-500 hover:text-red-700 font-bold text-xl">
                    &times;
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
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Opciones (mínimo 2)</label>
                        <button type="button" class="add-option-btn text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            + Agregar opción
                        </button>
                    </div>
                    <div class="options-list space-y-2 pl-4 border-l-2 border-blue-200/50 dark:border-blue-700/50">
                        {{-- Options injected via JS --}}
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
                
                // Initialize index based on existing questions
                let questionIndex = {{ $examen->preguntas->count() }};

                function addOption(questionBlock, questionIdx, optionIdx, isExisting = false) {
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

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-option-btn text-red-500 hover:text-red-700 font-bold text-lg';
                    removeBtn.innerHTML = '&times;';
                    removeBtn.dataset.questionIndex = questionIdx;
                    removeBtn.addEventListener('click', function() {
                        const optionItems = questionBlock.querySelectorAll('.option-item');
                        if (optionItems.length <= 2) {
                            alert('Cada pregunta debe tener al menos 2 opciones.');
                            return;
                        }
                        optDiv.remove();
                        updateOptionNumbers(questionBlock, questionIdx);
                    });

                    optDiv.appendChild(checkbox);
                    optDiv.appendChild(text);
                    optDiv.appendChild(removeBtn);
                    optionsList.appendChild(optDiv);
                }

                function updateOptionNumbers(questionBlock, questionIdx) {
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

                function handleQuestionTypeChange(questionBlock, questionIdx) {
                    const typeRadios = questionBlock.querySelectorAll('.question-type-radio');
                    const multipleContent = questionBlock.querySelector('.tipo-multiple-content');
                    const vfContent = questionBlock.querySelector('.tipo-verdadero-falso-content');
                    const abiertaContent = questionBlock.querySelector('.tipo-abierta-content');
                    
                    typeRadios.forEach(radio => {
                        radio.addEventListener('change', function() {
                            // Hide all content types
                            multipleContent.style.display = 'none';
                            vfContent.style.display = 'none';
                            abiertaContent.style.display = 'none';
                            
                            // Show selected type
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

                function addQuestion() {
                    const clone = template.content.cloneNode(true);
                    const block = clone.querySelector('.question-block');
                    const numberSpan = clone.querySelector('.question-number');
                    const removeQuestionBtn = clone.querySelector('.remove-question-btn');
                    const addOptionBtn = clone.querySelector('.add-option-btn');

                    // Use a unique ID for new questions to prevent any index collision
                    const uniqueIndex = 'new_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

                    block.dataset.index = uniqueIndex;
                    
                    // Calculate visual number based on current count
                    const currentCount = container.querySelectorAll('.question-block').length;
                    numberSpan.textContent = currentCount + 1;

                    // Update name attributes for all inputs
                    const textInput = clone.querySelector('textarea');
                    textInput.name = textInput.name.replace(/INDEX/g, uniqueIndex);
                    
                    // Update type radio names
                    const typeRadios = clone.querySelectorAll('.question-type-radio');
                    typeRadios.forEach(radio => {
                        radio.name = radio.name.replace(/INDEX/g, uniqueIndex);
                    });
                    
                    // Update verdadero/falso radio names
                    const vfRadios = clone.querySelectorAll('input[name*="vf_correcta"]');
                    vfRadios.forEach(radio => {
                        radio.name = radio.name.replace(/INDEX/g, uniqueIndex);
                    });
                    
                    // Update respuesta esperada textarea name
                    const respuestaEsperada = clone.querySelector('textarea[name*="respuesta_esperada"]');
                    if (respuestaEsperada) {
                        respuestaEsperada.name = respuestaEsperada.name.replace(/INDEX/g, uniqueIndex);
                    }

                    // Add initial 2 options (minimum required) for multiple choice
                    for (let i = 0; i < 2; i++) {
                        addOption(block, uniqueIndex, i);
                    }

                    // Add option button handler
                    addOptionBtn.addEventListener('click', function() {
                        const currentOptions = block.querySelectorAll('.option-item');
                        if (currentOptions.length >= 4) {
                            alert('Cada pregunta puede tener un máximo de 4 opciones.');
                            return;
                        }
                        addOption(block, uniqueIndex, currentOptions.length);
                    });

                    removeQuestionBtn.addEventListener('click', function() {
                        block.remove();
                        updateQuestionNumbers();
                    });
                    
                    // Set up type change handler
                    handleQuestionTypeChange(block, uniqueIndex);

                    container.appendChild(block);
                    questionIndex++;
                }

                function updateQuestionNumbers() {
                    const blocks = container.querySelectorAll('.question-block');
                    blocks.forEach((block, idx) => {
                        block.querySelector('.question-number').textContent = idx + 1;
                    });
                }

                addQuestionBtn.addEventListener('click', addQuestion);

                // Attach remove event to existing question buttons
                document.querySelectorAll('.remove-question-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        this.closest('.question-block').remove();
                        updateQuestionNumbers();
                    });
                });

                // Set up type change handlers for existing questions
                document.querySelectorAll('.question-block').forEach(block => {
                    const questionIdx = block.dataset.index;
                    handleQuestionTypeChange(block, questionIdx);
                });

                // Attach add option event to existing questions
                document.querySelectorAll('.add-option-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const questionBlock = this.closest('.question-block');
                        const currentOptions = questionBlock.querySelectorAll('.option-item');
                        
                        if (currentOptions.length >= 4) {
                            alert('Cada pregunta puede tener un máximo de 4 opciones.');
                            return;
                        }
                        
                        const questionIdx = questionBlock.dataset.index;
                        addOption(questionBlock, questionIdx, currentOptions.length, true);
                    });
                });

                // Attach remove option event to existing options
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
                        updateOptionNumbers(questionBlock, questionIdx);
                    });
                });

                // Form validation on submit
                document.getElementById('exam-form').addEventListener('submit', function(e) {
                    const questions = container.querySelectorAll('.question-block');
                    let hasError = false;
                    let errorMessage = '';

                    questions.forEach((question, idx) => {
                        // Get the selected question type
                        const selectedType = question.querySelector('.question-type-radio:checked');
                        if (!selectedType) {
                            hasError = true;
                            errorMessage = `La pregunta ${idx + 1} debe tener un tipo seleccionado.`;
                            return;
                        }

                        const questionType = selectedType.value;

                        // Validate based on question type
                        if (questionType === 'multiple') {
                            // Only check options if multiple choice is selected
                            const multipleContent = question.querySelector('.tipo-multiple-content');
                            if (multipleContent && multipleContent.style.display !== 'none') {
                                const options = question.querySelectorAll('.option-item');
                                if (options.length < 2) {
                                    hasError = true;
                                    errorMessage = `La pregunta ${idx + 1} debe tener al menos 2 opciones.`;
                                    return;
                                }

                                const checkedOptions = question.querySelectorAll('.tipo-multiple-content input[type="checkbox"]:checked');
                                if (checkedOptions.length === 0) {
                                    hasError = true;
                                    errorMessage = `La pregunta ${idx + 1} debe tener al menos una respuesta correcta marcada.`;
                                    return;
                                }
                            }
                        } else if (questionType === 'verdadero_falso') {
                            // Only check V/F if that type is selected
                            const vfContent = question.querySelector('.tipo-verdadero-falso-content');
                            if (vfContent && vfContent.style.display !== 'none') {
                                const vfChecked = question.querySelector('.tipo-verdadero-falso-content input[name*="vf_correcta"]:checked');
                                if (!vfChecked) {
                                    hasError = true;
                                    errorMessage = `La pregunta ${idx + 1} debe tener una respuesta correcta seleccionada (Verdadero o Falso).`;
                                    return;
                                }
                            }
                        }
                        // Open-ended questions don't need special validation
                    });

                    if (hasError) {
                        e.preventDefault();
                        alert(errorMessage);
                        return false;
                    }
                });
            });
        </script>

        <p class="mt-6"><a href="{{ route('exams.materia', $examen->materia_id) }}">Volver a Exámenes</a></p>
    </div>
</x-layouts.app>
