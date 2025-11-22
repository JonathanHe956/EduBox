<div>
    <form method="POST" action="{{ $action }}" id="question-form">
        @csrf

        <div class="mb-3">
            <label class="block font-medium">Texto de la pregunta</label>
            <textarea name="text" required class="border rounded w-full px-2 py-1"></textarea>
        </div>

        <div class="mb-3">
            <p class="font-medium">Opciones</p>
            <div id="options-container" class="space-y-2">
                {{-- JS generará los inputs aquí --}}
            </div>
            <small class="text-sm text-gray-500">Se generarán <span id="options-count">{{ $optionsPerQuestion }}</span> opciones. Marca hasta <span id="correct-count">{{ $correctAnswers }}</span> como correctas.</small>
        </div>

        <input type="hidden" name="_options_count" id="_options_count" value="{{ $optionsPerQuestion }}">

        <div class="mt-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Añadir pregunta</button>
        </div>
    </form>

    <script>
        (function(){
            const optionsPerQuestion = parseInt(@json($optionsPerQuestion ?? 4));
            const correctAnswers = parseInt(@json($correctAnswers ?? 1));
            const container = document.getElementById('options-container');
            const optionsCountSpan = document.getElementById('options-count');
            const correctCountSpan = document.getElementById('correct-count');
            const hiddenCount = document.getElementById('_options_count');

            optionsCountSpan.textContent = optionsPerQuestion;
            correctCountSpan.textContent = correctAnswers;
            hiddenCount.value = optionsPerQuestion;

            let correctChecked = 0;

            for (let i = 0; i < optionsPerQuestion; i++) {
                const div = document.createElement('div');
                const input = document.createElement('input');
                input.name = `options[${i}][text]`;
                input.placeholder = `Opción ${String.fromCharCode(65 + i)}`;
                input.required = i < 2; // require first two by default
                input.className = 'border px-2 py-1 mr-2';

                const label = document.createElement('label');
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = `options[${i}][is_correct]`;
                checkbox.addEventListener('change', function(){
                    if (this.checked) {
                        correctChecked++;
                    } else {
                        correctChecked--;
                    }
                    if (correctChecked > correctAnswers) {
                        alert('Has excedido el número máximo de respuestas correctas permitidas.');
                        this.checked = false;
                        correctChecked--;
                    }
                });

                label.appendChild(checkbox);
                label.appendChild(document.createTextNode(' correcta'));

                div.appendChild(input);
                div.appendChild(label);
                container.appendChild(div);
            }
        })();
    </script>
</div>
