<?php

namespace App\Http\Controllers;

use App\Models\Burger;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GestionnaireDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Vérifie si les informations doivent être mises à jour
        if ($user->name === 'Nouveau gestionnaire' || empty($user->prenom)) {
            return redirect()->route('profile.edit')->with('status', 'Veuillez mettre à jour vos informations.');
        }

        // Calcul des statistiques pour le tableau de bord

        // Compter le nombre total de clients (utilisateurs avec le rôle "client")
        $clientRole = Role::where('name', 'client')->first();
        $clientCount = $clientRole ? User::where('role_id', $clientRole->id)->count() : 0;

        // Commandes en cours aujourd'hui (statut 'En attente' ou 'En préparation')
        $ongoingOrdersCount = Order::whereIn('status', ['En attente', 'En préparation'])
            ->whereDate('created_at', Carbon::today())
            ->count();

        // Commandes validées aujourd'hui (statut 'Payée' ou 'Prête')
        $validatedOrdersCount = Order::whereIn('status', ['Payée', 'Prête'])
            ->whereDate('created_at', Carbon::today())
            ->count();

        // Total des revenus aujourd'hui (somme des commandes payées)
        $dailyRevenue = Order::where('status', 'Payée')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        // Données pour le graphique : Nombre de commandes par mois (pour l'année en cours)
        $ordersPerMonthData = [];
        for ($month = 1; $month <= 12; $month++) {
            $ordersPerMonthData[$month] = Order::whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', $month)
                ->count();
        }

        // Données pour le graphique : Commandes par catégorie (ce mois)
        $productsPerCategory = [
            'Burgers' => Order::whereHas('burgers')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'Autres' => 0, // À ajuster si tu as d'autres types de produits
        ];

        // Données pour le graphique : Quantité par burger (ce mois)
        $productsPerBurger = Burger::select('burgers.name', DB::raw('SUM(order_burger.quantity) as quantity'))
            ->join('order_burger', 'burgers.id', '=', 'order_burger.burger_id')
            ->join('orders', 'order_burger.order_id', '=', 'orders.id')
            ->whereMonth('orders.created_at', Carbon::now()->month)
            ->whereYear('orders.created_at', Carbon::now()->year)
            ->groupBy('burgers.id', 'burgers.name')
            ->get()
            ->pluck('quantity', 'name')
            ->toArray();

        // Commandes récentes (les 5 dernières commandes)
        $recentOrders = Order::with(['burgers', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Burgers les plus populaires (basé sur les commandes payées)
        $popularBurgers = Burger::select('burgers.*')
            ->join('order_burger', 'burgers.id', '=', 'order_burger.burger_id')
            ->join('orders', 'order_burger.order_id', '=', 'orders.id')
            ->where('orders.status', 'Payée')
            ->groupBy('burgers.id')
            ->selectRaw('burgers.*, SUM(order_burger.quantity) as total_quantity')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        return view('gestionnaire.dashboard', compact(
            'clientCount',
            'ongoingOrdersCount',
            'validatedOrdersCount',
            'dailyRevenue',
            'ordersPerMonthData',
            'productsPerCategory',
            'productsPerBurger',
            'recentOrders',
            'popularBurgers'
        ));
    }
}
