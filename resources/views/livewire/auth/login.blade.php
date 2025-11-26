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
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
                <div class="relative">
                    <input
                        wire:model="password"
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="Contraseña"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-zinc-800 dark:text-white @error('password') border-red-500 dark:border-red-500 @enderror"
                    />
                    @if (Route::has('password.request'))
                        <a class="absolute right-3 top-3 text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400" href="{{ route('password.request') }}" wire:navigate>
                            ¿Olvidaste?
                        </a>
                    @endif
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
