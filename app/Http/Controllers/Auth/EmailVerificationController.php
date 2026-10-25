<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    /**
     * Muestra la pantalla de estado de verificación de correo.
     */
    public function notice(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        return view('auth.verify-email');
    }

    /**
     * Verifica la dirección de correo electrónico mediante URL firmada criptográficamente.
     */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->intended(route('dashboard'))->with(
            'success',
            'Tu dirección de correo electrónico ha sido verificada exitosamente.'
        );
    }

    /**
     * Reenvía el enlace de verificación al usuario autenticado.
     */
    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            return back()->with(
                'error',
                'No fue posible enviar el correo de verificación. Revise la configuración SMTP del servidor.'
            );
        }

        return back()->with(
            'status',
            'Se ha enviado un nuevo enlace de verificación a tu correo electrónico.'
        );
    }
}
