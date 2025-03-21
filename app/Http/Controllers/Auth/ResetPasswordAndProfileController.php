<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetPasswordAndProfileController extends Controller
{
    public function showForm(Request $request, $token)
    {
        Log::info('Page de réinitialisation affichée', [
            'token' => $token,
            'email' => $request->email,
            'user_authenticated' => Auth::check(),
            'user_id' => Auth::id()
        ]);

        // Déconnecter manuellement l'utilisateur s'il est connecté
        if (Auth::check()) {
            Log::info('Déconnexion de l\'utilisateur', ['user_id' => Auth::id()]);
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return view('auth.reset-password-and-profile', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function update(Request $request)
    {
        // Décoder l'email (au cas où)
        $email = urldecode($request->email);
        if (str_contains($email, '%40')) {
            $email = urldecode($email);
        }

        Log::info('Tentative de mise à jour du profil', [
            'email' => $email,
            'token' => $request->token,
            'has_password' => !empty($request->password)
        ]);

        try {
            // Récupérer l'enregistrement de la table password_reset_tokens
            $resetToken = DB::table('password_reset_tokens')
                ->where('email', $email)
                ->first();

            if (!$resetToken) {
                Log::warning('Aucun enregistrement trouvé dans password_reset_tokens', ['email' => $email]);
                return back()->withErrors(['token' => 'Le lien de réinitialisation est invalide ou a expiré.'])
                    ->withInput($request->except('password'));
            }

            // Vérifier si le token correspond
            if (!Hash::check($request->token, $resetToken->token)) {
                Log::warning('Le token ne correspond pas au hachage stocké', [
                    'provided_token' => $request->token,
                    'stored_hash' => $resetToken->token
                ]);
                return back()->withErrors(['token' => 'Le lien de réinitialisation est invalide ou a expiré.'])
                    ->withInput($request->except('password'));
            }

            // Vérifier l'expiration du token
            $expirationMinutes = config('auth.passwords.users.expire', 60);
            if (now()->diffInMinutes($resetToken->created_at) > $expirationMinutes) {
                Log::warning('Token expiré', [
                    'created_at' => $resetToken->created_at,
                    'expiration_minutes' => $expirationMinutes
                ]);
                return back()->withErrors(['token' => 'Le lien de réinitialisation est invalide ou a expiré.'])
                    ->withInput($request->except('password'));
            }

            // Récupérer l'utilisateur par email
            $user = User::where('email', $email)->firstOrFail();
            Log::info('Utilisateur trouvé', ['user_id' => $user->id]);

            // Valider les données
            $validated = $request->validate([
                'token'         => ['required'],
                'email'         => ['required', 'email'],
                'password'      => ['required', 'confirmed', Rules\Password::defaults()],
                'name'          => ['required', 'string', 'max:255'],
                'prenom'        => ['required', 'string', 'max:255'],
                'username'      => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
                'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Validation pour l'image
            ]);

            Log::info('Validation réussie');

            // Vérifier le rôle de l'utilisateur
            if ($user->role->name !== 'gestionnaire') {
                Log::warning('Utilisateur non autorisé', ['role' => $user->role->name]);
                return redirect('/')->with('error', 'Vous n\'êtes pas autorisé à utiliser cette page.');
            }

            // Gérer l'upload de l'image
            $imagePath = $user->profile_image; // Conserver l'image existante par défaut
            if ($request->hasFile('profile_image')) {
                Log::info('Image de profil uploadée', [
                    'file_name' => $request->file('profile_image')->getClientOriginalName(),
                    'file_size' => $request->file('profile_image')->getSize(),
                ]);
                // Supprimer l'ancienne image si elle existe
                if ($user->profile_image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_image);
                    Log::info('Ancienne image supprimée', ['old_image' => $user->profile_image]);
                }
                // Stocker la nouvelle image
                $imagePath = $request->file('profile_image')->store('profile_images', 'public');
                Log::info('Nouvelle image stockée', ['new_image_path' => $imagePath]);
            } else {
                Log::info('Aucune image de profil uploadée');
            }

            // Mettre à jour l'utilisateur
            $user->forceFill([
                'password'       => Hash::make($request->password),
                'remember_token' => Str::random(60),
                'name'           => $request->name,
                'prenom'         => $request->prenom,
                'username'       => $request->username,
                'is_active'      => true,
                'profile_image'  => $imagePath, // Mettre à jour le chemin de l'image
            ])->save();

            Log::info('Utilisateur mis à jour avec succès', ['profile_image' => $imagePath]);

            // Supprimer le token de réinitialisation après utilisation
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->delete();

            Log::info('Token de réinitialisation supprimé');

            // Connecter l'utilisateur et rediriger vers son dashboard
            Auth::login($user);
            return redirect()->route('gestionnaire.dashboard')
                ->with('status', 'Votre mot de passe et profil ont été mis à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour', ['message' => $e->getMessage()]);
            return back()->withErrors(['general' => 'Une erreur est survenue : ' . $e->getMessage()])
                ->withInput($request->except('password'));
        }
    }
}
