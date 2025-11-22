<x-layouts.app :title="__('Exámenes pendientes')">
    <div class="px-4 py-6">
        <h1 class="text-2xl font-semibold">Exámenes pendientes</h1>

        @if($examenes->isEmpty())
            <p class="mt-4">No tienes exámenes pendientes en este momento.</p>
        @else
            <ul class="mt-4 space-y-3">
                @foreach($examenes as $examen)
                    @php
                        $intento = $examen->intentos->first();
                        $hasAttempt = $intento !== null;
                    @endphp
                    <li class="border rounded p-3 flex justify-between items-center">
                        <div>
                            <a class="font-medium text-lg" href="{{ route('examenes.show', $examen) }}">{{ $examen->titulo }}</a>
                            <div class="text-sm text-gray-600">{{ $examen->descripcion }}</div>
                        </div>
                        <div>
                            @if($hasAttempt)
                                <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded cursor-not-allowed" title="Ya has realizado este examen">
                                    Completado ({{ $intento->puntuacion == floor($intento->puntuacion) ? number_format($intento->puntuacion, 0) : number_format($intento->puntuacion, 1) }}/{{ $intento->total }})
                                </span>
                            @else
                                <a href="{{ route('examenes.show', $examen) }}" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Realizar</a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        <p class="mt-6"><a href="{{ route('mis.materias') }}">Volver</a></p>
    </div>
</x-layouts.app>
