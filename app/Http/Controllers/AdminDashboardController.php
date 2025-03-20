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

    public function dashboardData()
    {
        $gestionnaireRole = Role::where('name', 'gestionnaire')->firstOrFail();
        $totalGestionnaires = User::where('role_id', $gestionnaireRole->id)->count();
        $activeGestionnaires = User::where('role_id', $gestionnaireRole->id)->where('is_active', true)->count();
        $inactiveGestionnaires = User::where('role_id', $gestionnaireRole->id)->where('is_active', false)->count();
        $gestionnaires = User::where('role_id', $gestionnaireRole->id)->paginate(10);

        return response()->json([
            'totalGestionnaires' => $totalGestionnaires,
            'activeGestionnaires' => $activeGestionnaires,
            'inactiveGestionnaires' => $inactiveGestionnaires,
            'gestionnaires' => $gestionnaires->items(),
            'pagination' => (string) $gestionnaires->links(),
        ]);
    }

    public function gestionnairesData()
    {
        $gestionnaireRole = Role::where('name', 'gestionnaire')->firstOrFail();
        $gestionnaires = User::where('role_id', $gestionnaireRole->id)->get();
        return response()->json(['gestionnaires' => $gestionnaires]);
    }

    public function actifsData()
    {
        $gestionnaireRole = Role::where('name', 'gestionnaire')->firstOrFail();
        $gestionnaires = User::where('role_id', $gestionnaireRole->id)->where('is_active', true)->get();
        return response()->json(['gestionnaires' => $gestionnaires]);
    }

    public function desactivesData()
    {
        $gestionnaireRole = Role::where('name', 'gestionnaire')->firstOrFail();
        $gestionnaires = User::where('role_id', $gestionnaireRole->id)->where('is_active', false)->get();
        return response()->json(['gestionnaires' => $gestionnaires]);
    }

    public function ajouterData()
    {
        return response()->json(['form' => true]);
    }
}
