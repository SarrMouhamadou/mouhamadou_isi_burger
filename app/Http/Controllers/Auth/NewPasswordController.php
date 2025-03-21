<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        if (Auth::check()) {
            Auth::logout();
        }

        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Attempt to reset the user's password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, redirect based on the user's role
        if ($status == Password::PASSWORD_RESET) {
            // Connecte l'utilisateur après la réinitialisation
            $user = \App\Models\User::where('email', $request->email)->first();
            Auth::login($user);

            // Si c'est un gestionnaire, rediriger vers la page de mise à jour des informations
            if ($user->role->name === 'gestionnaire') {
                return redirect()->route('profile.edit')->with('status', 'Mot de passe défini avec succès. Veuillez mettre à jour vos informations.');
            }

            // Redirection pour les admins
            if ($user->role->name === 'admin') {
                return redirect()->route('admin.dashboard')->with('status', 'Mot de passe réinitialisé avec succès.');
            }

            // Redirection par défaut pour les autres rôles
            return redirect()->route('dashboard')->with('status', __($status));
        }

        // Si la réinitialisation échoue, retourne à la page précédente avec une erreur
        return back()->withInput($request->only('email'))
                     ->withErrors(['email' => __($status)]);
    }
}
