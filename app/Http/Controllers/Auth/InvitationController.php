<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BusinessInvitation;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class InvitationController extends Controller
{
    /**
     * Muestra la pantalla para aceptar una invitación a un negocio.
     */
    public function show(string $token): View|RedirectResponse
    {
        $invitation = BusinessInvitation::with('business')
            ->where('token', $token)
            ->first();

        if (! $invitation || ! $invitation->isPending() || ! $invitation->business || ! $invitation->business->isActive()) {
            return redirect()->route('login')->with(
                'error',
                'El enlace de invitación es inválido, fue revocado o ha expirado.'
            );
        }

        $existingUser = User::where('email', $invitation->email)->first();

        return view('auth.accept-invitation', compact('invitation', 'existingUser'));
    }

    /**
     * Procesa la aceptación de la invitación, vincula la membresía y activa la sesión.
     */
    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = BusinessInvitation::with('business')
            ->where('token', $token)
            ->first();

        if (! $invitation || ! $invitation->isPending() || ! $invitation->business || ! $invitation->business->isActive()) {
            return redirect()->route('login')->with(
                'error',
                'El enlace de invitación es inválido, fue revocado o ha expirado.'
            );
        }

        $existingUser = User::where('email', $invitation->email)->first();

        if (! $existingUser) {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
        } else {
            $validated = [];
        }

        $user = DB::transaction(function () use ($invitation, $existingUser, $validated, $request) {
            if (! $existingUser) {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $invitation->email,
                    'password' => Hash::make($validated['password']),
                    'role' => $invitation->role === User::ROLE_OWNER ? User::ROLE_OWNER : ($invitation->role === User::ROLE_ADMIN ? User::ROLE_ADMIN : User::ROLE_CASHIER),
                    'status' => 'active',
                    'current_business_id' => $invitation->business_id,
                    'email_verified_at' => now(),
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip(),
                ]);
            } else {
                $user = $existingUser;
                $user->forceFill([
                    'status' => 'active',
                    'current_business_id' => $invitation->business_id,
                    'email_verified_at' => $user->email_verified_at ?: now(),
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip(),
                ])->save();
            }

            if (! $user->businesses()->where('businesses.id', $invitation->business_id)->exists()) {
                $user->businesses()->attach($invitation->business_id, [
                    'role' => $invitation->role,
                    'is_default' => $user->businesses()->count() === 0,
                ]);
            } else {
                $user->businesses()->updateExistingPivot($invitation->business_id, [
                    'role' => $invitation->role,
                ]);
            }

            $invitation->update([
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        AuditLogger::record(
            'invitation.accepted',
            "El usuario {$user->email} aceptó la invitación para unirse a {$invitation->business->name} como {$invitation->role}.",
            $user,
            [
                'invitation_id' => $invitation->id,
                'role' => $invitation->role,
            ],
            $invitation->business_id
        );

        return redirect()->route('dashboard')->with(
            'success',
            "Te has unido exitosamente a {$invitation->business->name}."
        );
    }
}
