<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin']);
    }

    public function index()
    {
        return view('admin.dashboard');
    }

    public function data($section, Request $request)
    {
        // Récupérer le rôle "gestionnaire"
        $gestionnaireRole = Role::where('name', 'gestionnaire')->firstOrFail();

        // Initialiser la requête pour les gestionnaires
        $query = User::where('role_id', $gestionnaireRole->id);

        // Appliquer des filtres selon la section
        if ($section === 'actifs') {
            $query->where('is_active', true);
        } elseif ($section === 'desactives') {
            $query->where('is_active', false);
        }

        // Paginer les résultats (10 par page)
        $gestionnaires = $query->paginate(10);

        // Formater les gestionnaires pour s'assurer que tous les champs nécessaires sont présents
        $formattedGestionnaires = $gestionnaires->map(function ($gestionnaire) {
            return [
                'id' => $gestionnaire->id,
                'name' => $gestionnaire->name ?? 'Non défini',
                'username' => $gestionnaire->username ?? 'Non défini',
                'email' => $gestionnaire->email ?? 'Non défini',
                'is_active' => $gestionnaire->is_active,
                'prenom' => $gestionnaire->prenom ?? 'Non défini',
                'profile_image_url' => $gestionnaire->profile_image ? asset('storage/' . $gestionnaire->profile_image) : null,
            ];
        });

        // Si la section est "dashboard", inclure les statistiques
        if ($section === 'dashboard') {
            $totalGestionnaires = User::where('role_id', $gestionnaireRole->id)->count();
            $activeGestionnaires = User::where('role_id', $gestionnaireRole->id)->where('is_active', true)->count();
            $inactiveGestionnaires = User::where('role_id', $gestionnaireRole->id)->where('is_active', false)->count();

            return response()->json([
                'totalGestionnaires' => $totalGestionnaires,
                'activeGestionnaires' => $activeGestionnaires,
                'inactiveGestionnaires' => $inactiveGestionnaires,
                'gestionnaires' => $formattedGestionnaires,
                'pagination' => [
                    'current_page' => $gestionnaires->currentPage(),
                    'last_page' => $gestionnaires->lastPage(),
                    'next_page_url' => $gestionnaires->nextPageUrl(),
                    'prev_page_url' => $gestionnaires->previousPageUrl(),
                    'total' => $gestionnaires->total(),
                    'per_page' => $gestionnaires->perPage(),
                ],
            ]);
        }

        // Pour les autres sections (gestionnaires, actifs, désactivés), retourner uniquement la liste paginée
        return response()->json([
            'gestionnaires' => $formattedGestionnaires,
            'pagination' => [
                'current_page' => $gestionnaires->currentPage(),
                'last_page' => $gestionnaires->lastPage(),
                'next_page_url' => $gestionnaires->nextPageUrl(),
                'prev_page_url' => $gestionnaires->previousPageUrl(),
                'total' => $gestionnaires->total(),
                'per_page' => $gestionnaires->perPage(),
            ],
        ]);
    }
}
