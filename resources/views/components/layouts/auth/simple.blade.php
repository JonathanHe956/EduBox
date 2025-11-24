<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-blue-900 authenticated antialiased">

        {{ $slot }}

        @fluxScripts
    </body>
</html>
