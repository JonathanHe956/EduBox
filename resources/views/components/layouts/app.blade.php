@if(auth()->check() && auth()->user()->hasRole('admin'))
    <x-layouts.app.sidebar :title="$title ?? null">
        <flux:main>
            {{ $slot }}
        </flux:main>
    </x-layouts.app.sidebar>
@elseif(auth()->check() && (auth()->user()->hasRole('estudiante') || auth()->user()->hasRole('student')))
    <x-layouts.app.header :title="$title ?? null">
        <flux:main>
            {{ $slot }}
        </flux:main>
    </x-layouts.app.header>
@else
    <x-layouts.app.header :title="$title ?? null">
        <flux:main>
            {{ $slot }}
        </flux:main>
    </x-layouts.app.header>
@endif
