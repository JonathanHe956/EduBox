<x-layouts.auth.simple>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <x-app-logo-icon class="mx-auto h-12 w-auto" />
                <h2 class="mt-6 text-center text-3xl font-extrabold text-blue-900 dark:text-white">
                    Acceso Denegado
                </h2>
                <p class="mt-2 text-center text-sm text-blue-700 dark:text-blue-200">
                    No tienes los permisos necesarios para ingresar.
                </p>
                <div class="mt-8">
                    <a href="{{ route('dashboard') }}" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.auth.simple>
