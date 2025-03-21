<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

        // Générer un token pour définir le mot de passe
        $token = Password::createToken($user);
        $resetLink = route('password.reset.profile', ['token' => $token, 'email' => $user->email]);

        // Ajouter un log pour vérifier l'URL générée
        \Illuminate\Support\Facades\Log::info('Lien de réinitialisation généré', ['resetLink' => $resetLink]);

        // Envoyer l'email pour définir le mot de passe
        Mail::send('emails.set_password', ['resetLink' => $resetLink], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Définissez votre mot de passe - ISI Burger');
        });

        return response()->json(['success' => true, 'message' => 'Gestionnaire ajouté avec succès. Un email a été envoyé.']);
    }

    public function show(User $gestionnaire)
    {
        $gestionnaire->profile_image_url = $gestionnaire->profile_image
            ? asset('storage/' . $gestionnaire->profile_image)
            : null;

        return response()->json($gestionnaire);
    }

    public function toggleStatus(Request $request, User $gestionnaire)
    {
        // Vérifier que l'utilisateur est un gestionnaire
        $gestionnaireRole = Role::where('name', 'gestionnaire')->firstOrFail();
        if ($gestionnaire->role_id !== $gestionnaireRole->id) {
            return response()->json(['success' => false, 'message' => 'Utilisateur non autorisé.'], 403);
        }

        // Inverser le statut
        $gestionnaire->is_active = !$gestionnaire->is_active;
        $gestionnaire->save();

        return response()->json([
            'success' => true,
            'message' => $gestionnaire->is_active ? 'Gestionnaire activé avec succès.' : 'Gestionnaire désactivé avec succès.',
            'is_active' => $gestionnaire->is_active,
        ]);
    }

    public function destroy(User $gestionnaire)
    {
        // Vérifier que l'utilisateur est un gestionnaire
        $gestionnaireRole = Role::where('name', 'gestionnaire')->firstOrFail();
        if ($gestionnaire->role_id !== $gestionnaireRole->id) {
            return response()->json(['success' => false, 'message' => 'Utilisateur non autorisé.'], 403);
        }

        // Supprimer l'image de profil si elle existe
        if ($gestionnaire->profile_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($gestionnaire->profile_image);
        }

        // Supprimer le gestionnaire
        $gestionnaire->delete();

        return response()->json(['success' => true, 'message' => 'Gestionnaire supprimé avec succès.']);
    }
}
