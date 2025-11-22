<x-layouts.app :title="__('Mis Exámenes')">
    <div class="px-4 py-6">
        <h1 class="text-2xl font-semibold">Mis exámenes</h1>

        @if(session('success'))
            <div class="mt-4 text-green-600">{{ session('success') }}</div>
        @endif

        @if($examenes->isEmpty())
            <p class="mt-4">No has creado exámenes todavía.</p>
        @else
            <table class="min-w-full mt-4 border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2">Título</th>
                        <th class="px-4 py-2">Preguntas</th>
                        <th class="px-4 py-2">Opciones/preg</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($examenes as $ex)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $ex->titulo }}</td>
                            <td class="px-4 py-2">{{ $ex->preguntas_count }}</td>
                            <td class="px-4 py-2">{{ $ex->options_per_question ?? 4 }} / {{ $ex->correct_answers ?? 1 }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('examenes.show', $ex) }}" class="text-sm text-blue-600 mr-2">Ver</a>
                                <a href="{{ route('examenes.edit', $ex) }}" class="text-sm text-yellow-600 mr-2">Editar</a>
                                <form action="{{ route('examenes.destroy', $ex) }}" method="POST" style="display:inline" onsubmit="return confirm('Eliminar examen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <p class="mt-6"><a href="{{ route('mis.materias') }}">Volver</a></p>
    </div>
</x-layouts.app>
