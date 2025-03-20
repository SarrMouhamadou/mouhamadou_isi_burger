<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;

class AdminGestionnaireController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin']);
    }

    public function store(Request $request)
    {
        // Valider l'email
        $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);

        // Trouver le rôle gestionnaire
        $gestionnaireRole = Role::where('name', 'gestionnaire')->firstOrFail();

        // Générer un mot de passe temporaire
        $temporaryPassword = Str::random(16);

        // Créer l'utilisateur
        $user = User::create([
            'name' => 'Nouveau Gestionnaire',
            'username' => 'gestionnaire_' . Str::random(8),
            'email' => $request->email,
            'password' => Hash::make($temporaryPassword),
            'role_id' => $gestionnaireRole->id,
            'is_active' => false,
        ]);

        // Envoyer un email de vérification
        event(new Registered($user));

        // Générer un token pour définir le mot de passe
        $token = Password::createToken($user);
        $resetLink = url('/reset-password', $token) . '?email=' . urlencode($user->email);

        // Envoyer l'email pour définir le mot de passe
        Mail::send('emails.set_password', ['resetLink' => $resetLink], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Définissez votre mot de passe - ISI Burger');
        });

        return response()->json(['success' => true, 'message' => 'Gestionnaire ajouté avec succès. Un email a été envoyé.']);
    }

    public function show(User $gestionnaire)
    {
        return response()->json($gestionnaire);
    }
}
