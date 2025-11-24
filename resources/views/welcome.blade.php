<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>EduBox - Plataforma Educativa</title>

        <link rel="icon" href="{{ asset('favicon.ico') }}?v=2" sizes="any">
        <link rel="icon" href="{{ asset('favicon.svg') }}?v=2" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .gradient-text {
                background: linear-gradient(135deg, #d4af37 0%, #cd7f32 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
        </style>
    </head>
    <body class="min-h-screen bg-blue-900 authenticated flex items-center justify-center p-6">
        <div class="w-full max-w-6xl">
            <header class="w-full text-center mb-12">
                @if (Route::has('login'))
                    <nav class="flex items-center justify-end gap-4 mb-8">
                        @auth
                            <a
                                href="{{ url('/dashboard') }}"
                                class="inline-block px-6 py-2.5 bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 text-white rounded-lg text-sm font-medium transition-all duration-300"
                            >
                                Panel de Control
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="inline-block px-6 py-2.5 bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white/20 text-white rounded-lg text-sm font-medium transition-all duration-300"
                            >
                                Iniciar Sesión
                            </a>
                        @endauth
                    </nav>
                @endif
            </header>

            <main class="text-center">
                <!-- Logo and Title -->
                <div class="mb-12">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-gold-400 to-bronze-500 rounded-2xl mb-6 shadow-2xl">
                        <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h1 class="text-6xl font-bold text-white mb-4">
                        Edu<span class="gradient-text">Box</span>
                    </h1>
                    <p class="text-xl text-blue-100 max-w-2xl mx-auto">
                        Plataforma educativa moderna para la gestión integral de recursos académicos
                    </p>
                </div>

                <!-- Features Grid -->
                <div class="grid md:grid-cols-3 gap-6 mb-12">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 hover:bg-white/15 transition-all duration-300 transform hover:scale-105">
                        <div class="w-12 h-12 bg-gradient-to-br from-gold-400 to-gold-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">Gestión de Materias</h3>
                        <p class="text-blue-200 text-sm">Administra asignaturas, contenidos y recursos educativos de forma eficiente</p>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 hover:bg-white/15 transition-all duration-300 transform hover:scale-105">
                        <div class="w-12 h-12 bg-gradient-to-br from-gold-400 to-gold-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">Exámenes Digitales</h3>
                        <p class="text-blue-200 text-sm">Crea, administra y califica exámenes de manera automática y segura</p>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-6 hover:bg-white/15 transition-all duration-300 transform hover:scale-105">
                        <div class="w-12 h-12 bg-gradient-to-br from-gold-400 to-gold-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">Control de Alumnos</h3>
                        <p class="text-blue-200 text-sm">Seguimiento completo del progreso y desempeño de cada estudiante</p>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex gap-4 justify-center items-center">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-block px-8 py-3 bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white font-semibold rounded-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                                Ir al Panel
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-block px-8 py-3 bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white font-semibold rounded-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                                Comenzar Ahora
                            </a>
                        @endauth
                    @endif
                </div>

                <!-- Footer -->
                <div class="mt-16 text-blue-200 text-sm">
                    <p>© {{ date('Y') }} EduBox. Plataforma educativa de última generación.</p>
                </div>
            </main>
        </div>
    </body>
</html>