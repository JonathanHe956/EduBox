<?php

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Features;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        $user = $this->validateCredentials();

        if (Features::canManageTwoFactorAuthentication() && $user->hasEnabledTwoFactorAuthentication()) {
            Session::put([
                'login.id' => $user->getKey(),
                'login.remember' => $this->remember,
            ]);

            $this->redirect(route('two-factor.login'), navigate: true);

            return;
        }

        Auth::login($user, $this->remember);

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Validate the user's credentials.
     */
    protected function validateCredentials(): User
    {
        $user = User::where('email', $this->email)->first();

        if (! $user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'No existe una cuenta con este correo electrónico.',
            ]);
        }

        if (! Auth::getProvider()->validateCredentials($user, ['password' => $this->password])) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'password' => 'La contraseña es incorrecta.',
            ]);
        }

        return $user;
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="flex min-h-screen items-center justify-center">
    <div class="w-full max-w-md space-y-8 p-8 glass-card">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-blue-900 dark:text-white">Iniciar Sesión</h2>
            <p class="mt-2 text-sm text-blue-700 dark:text-blue-200">Ingresa tu correo electrónico y contraseña para iniciar sesión</p>
        </div>

        <!-- Estado de sesión -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" wire:submit="login" class="space-y-6" action="#">
            <!-- Correo electrónico -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo electrónico</label>
                <input
                    wire:model="email"
                    id="email"
                    name="email"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="correo@ejemplo.com"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white @error('email') border-red-500 dark:border-red-500 @enderror"
                />
                @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contraseña -->
            <div x-data="{ showPassword: false }">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
                    @if (Route::has('password.request'))
                        <a class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400" href="{{ route('password.request') }}" wire:navigate>
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>
                <div class="mt-1 flex items-center gap-2">
                    <input
                        wire:model="password"
                        id="password"
                        name="password"
                        :type="showPassword ? 'text' : 'password'"
                        required
                        autocomplete="current-password"
                        placeholder="Contraseña"
                        class="block flex-1 rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white @error('password') border-red-500 dark:border-red-500 @enderror"
                    />
                    <button type="button" @click="showPassword = !showPassword" class="p-2 text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200">
                        <!-- Icono Ojo -->
                        <svg x-show="showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Icono Ojo Cerrado -->
                        <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.574-2.59M5.375 5.375A8.49 8.49 0 0112 5c4.478 0 8.268 2.943 9.542 7a8.48 8.48 0 01-2.903 4m-3.715 3.715l-10-10" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Recordarme -->
            <div class="flex items-center">
                <label for="remember" class="flex items-center cursor-pointer">
                    <input
                        wire:model="remember"
                        id="remember"
                        name="remember"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800"
                    />
                    <span class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Recordarme</span>
                </label>
            </div>

            <div>
                <button type="submit" class="w-full rounded-md btn-primary focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" data-test="login-button">
                    Iniciar Sesión
                </button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="text-center text-sm text-blue-700 dark:text-blue-200">
                <span>¿No tienes una cuenta?</span>
                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400" wire:navigate>Regístrate</a>
            </div>
        @endif
    </div>
</div>
