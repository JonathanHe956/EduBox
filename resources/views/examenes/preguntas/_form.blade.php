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
            const opcionesPorPregunta = parseInt(@json($optionsPerQuestion ?? 4));
            const respuestasCorrectas = parseInt(@json($correctAnswers ?? 1));
            const contenedor = document.getElementById('options-container');
            const spanConteoOpciones = document.getElementById('options-count');
            const spanConteoCorrectas = document.getElementById('correct-count');
            const conteoOculto = document.getElementById('_options_count');

            spanConteoOpciones.textContent = opcionesPorPregunta;
            spanConteoCorrectas.textContent = respuestasCorrectas;
            conteoOculto.value = opcionesPorPregunta;

            let correctasMarcadas = 0;

            for (let i = 0; i < opcionesPorPregunta; i++) {
                const contenedorDiv = document.createElement('div');
                const entrada = document.createElement('input');
                entrada.name = `options[${i}][text]`;
                entrada.placeholder = `Opción ${String.fromCharCode(65 + i)}`;
                entrada.required = i < 2; // require first two by default
                entrada.className = 'border px-2 py-1 mr-2';

                const etiqueta = document.createElement('label');
                const casillaVerificacion = document.createElement('input');
                casillaVerificacion.type = 'checkbox';
                casillaVerificacion.name = `options[${i}][is_correct]`;
                casillaVerificacion.addEventListener('change', function(){
                    if (this.checked) {
                        correctasMarcadas++;
                    } else {
                        correctasMarcadas--;
                    }
                    if (correctasMarcadas > respuestasCorrectas) {
                        alert('Has excedido el número máximo de respuestas correctas permitidas.');
                        this.checked = false;
                        correctasMarcadas--;
                    }
                });

                etiqueta.appendChild(casillaVerificacion);
                etiqueta.appendChild(document.createTextNode(' correcta'));

                contenedorDiv.appendChild(entrada);
                contenedorDiv.appendChild(etiqueta);
                contenedor.appendChild(contenedorDiv);
            }
        })();
    </script>
</div>
