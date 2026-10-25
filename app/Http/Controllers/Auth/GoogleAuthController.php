<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    /**
     * Verifica si las credenciales OAuth 2.0 de Google están configuradas en el servidor.
     */
    public static function isConfigured(): bool
    {
        return ! empty(config('services.google.client_id'))
            && ! empty(config('services.google.client_secret'));
    }

    /**
     * Redirige al usuario a la pantalla de consentimiento OAuth 2.0 de Google.
     * No simula autenticaciones si las credenciales no están configuradas.
     */
    public function redirect(Request $request): RedirectResponse
    {
        if (! self::isConfigured()) {
            return redirect()->route('login')->with(
                'error',
                'La autenticación con Google no está configurada en este entorno. Configure GOOGLE_CLIENT_ID y GOOGLE_CLIENT_SECRET.'
            );
        }

        if (class_exists(\Laravel\Socialite\Facades\Socialite::class)) {
            return \Laravel\Socialite\Facades\Socialite::driver('google')->redirect();
        }

        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);

        $query = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect'),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account',
        ]);

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
    }

    /**
     * Procesa el callback OAuth 2.0 de Google, valida estado CSRF y autentica usuarios autorizados.
     */
    public function callback(Request $request): RedirectResponse
    {
        if (! self::isConfigured()) {
            return redirect()->route('login')->with(
                'error',
                'La autenticación con Google no está configurada en este entorno.'
            );
        }

        if ($request->filled('error')) {
            return redirect()->route('login')->with(
                'error',
                'El inicio de sesión con Google fue cancelado o denegado.'
            );
        }

        try {
            if (class_exists(\Laravel\Socialite\Facades\Socialite::class)) {
                $socialiteUser = \Laravel\Socialite\Facades\Socialite::driver('google')->user();
                $googleId = (string) $socialiteUser->getId();
                $email = strtolower(trim((string) $socialiteUser->getEmail()));
                $avatar = $socialiteUser->getAvatar();
                $emailVerified = (bool) ($socialiteUser->user['email_verified'] ?? true);
            } else {
                $expectedState = $request->session()->pull('google_oauth_state');
                $receivedState = (string) $request->input('state', '');

                if (empty($expectedState) || ! hash_equals($expectedState, $receivedState)) {
                    return redirect()->route('login')->with(
                        'error',
                        'Estado de seguridad OAuth inválido o expirado. Intenta nuevamente.'
                    );
                }

                $code = (string) $request->input('code', '');
                if ($code === '') {
                    return redirect()->route('login')->with(
                        'error',
                        'No se recibió un código de autorización válido desde Google.'
                    );
                }

                $tokenResponse = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
                    'client_id' => config('services.google.client_id'),
                    'client_secret' => config('services.google.client_secret'),
                    'redirect_uri' => config('services.google.redirect'),
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                ]);

                if (! $tokenResponse->successful() || empty($tokenResponse->json('access_token'))) {
                    return redirect()->route('login')->with(
                        'error',
                        'No fue posible validar el token de acceso con Google.'
                    );
                }

                $profileResponse = Http::withToken($tokenResponse->json('access_token'))
                    ->timeout(10)
                    ->get('https://www.googleapis.com/oauth2/v3/userinfo');

                if (! $profileResponse->successful()) {
                    return redirect()->route('login')->with(
                        'error',
                        'No fue posible obtener la información de perfil desde Google.'
                    );
                }

                $profile = $profileResponse->json();
                $googleId = (string) ($profile['sub'] ?? '');
                $email = strtolower(trim((string) ($profile['email'] ?? '')));
                $avatar = $profile['picture'] ?? null;
                $emailVerified = (bool) ($profile['email_verified'] ?? false);
            }
        } catch (\Throwable $e) {
            return redirect()->route('login')->with(
                'error',
                'Ocurrió un error al comunicarse con el proveedor de Google. Intenta nuevamente.'
            );
        }

        if ($googleId === '' || $email === '' || ! $emailVerified) {
            return redirect()->route('login')->with(
                'error',
                'La cuenta de Google debe contar con una dirección de correo electrónico verificada.'
            );
        }

        // Buscar cuenta existente por google_id o por correo electrónico
        $user = User::where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        if (! $user) {
            return redirect()->route('login')->withErrors([
                'email' => "No existe una cuenta autorizada para {$email} en PuntoÁgil. Solicita el alta al administrador de tu empresa.",
            ]);
        }

        if ($user->status !== 'active') {
            return redirect()->route('login')->withErrors([
                'email' => 'Esta cuenta se encuentra inactiva. Por favor, contacte al administrador.',
            ]);
        }

        // Vincular identificadores OAuth y marcar correo como verificado
        $user->google_id = $googleId;
        if ($avatar) {
            $user->avatar = $avatar;
        }
        if (! $user->email_verified_at) {
            $user->email_verified_at = now();
        }
        $user->last_login_at = now();
        $user->last_login_ip = $request->ip();
        $user->save();

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }
}
