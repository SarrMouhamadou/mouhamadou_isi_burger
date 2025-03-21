<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use Carbon\Carbon;

class GestionnaireDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:gestionnaire']);
    }

    public function index()
    {
        $user = Auth::user();

        // Vérifie si les informations doivent être mises à jour
        if ($user->name === 'Nouveau Gestionnaire' || empty($user->prenom)) {
            return redirect()->route('profile.edit')->with('status', 'Veuillez mettre à jour vos informations avant de continuer.');
        }

        // Calcul des statistiques pour le tableau de bord

        // Commandes en cours aujourd'hui (statut "en_attente" ou "en_preparation")
        $ongoingOrdersCount = Order::whereIn('status', ['en_attente', 'en_preparation'])
            ->whereDate('created_at', Carbon::today())
            ->count();

        // Commandes validées aujourd'hui (statut "payee")
        $validatedOrdersCount = Order::where('status', 'payee')
            ->whereDate('created_at', Carbon::today())
            ->count();

        // Recettes journalières (somme des commandes payées aujourd'hui)
        $dailyRevenue = Order::where('status', 'payee')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        // Données pour le graphique des commandes par mois
        $ordersPerMonthData = Order::selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupByRaw('EXTRACT(MONTH FROM created_at)')
            ->pluck('count', 'month')
            ->toArray();

        // Remplir les mois manquants avec 0
        for ($i = 1; $i <= 12; $i++) {
            if (!isset($ordersPerMonthData[$i])) {
                $ordersPerMonthData[$i] = 0;
            }
        }
        ksort($ordersPerMonthData);

        // Données pour le graphique des produits par catégorie (ce mois)
        $productsPerCategory = [
            'Classique' => Order::whereHas('burgers', function ($query) {
                $query->where('category', 'Classique');
            })->whereMonth('created_at', Carbon::now()->month)->count(),
            'Végétarien' => Order::whereHas('burgers', function ($query) {
                $query->where('category', 'Végétarien');
            })->whereMonth('created_at', Carbon::now()->month)->count(),
        ];

        // Données pour un autre graphique ou section : Quantité totale par burger (ce mois)
        $productsPerBurger = Order::join('order_burger', 'orders.id', '=', 'order_burger.order_id')
            ->join('burgers', 'order_burger.burger_id', '=', 'burgers.id')
            ->selectRaw('burgers.name, SUM(order_burger.quantity) as total_quantity')
            ->whereMonth('orders.created_at', Carbon::now()->month)
            ->groupBy('burgers.name')
            ->pluck('total_quantity', 'name')
            ->toArray();

        // Commandes récentes
        $recentOrders = Order::with('burgers', 'user')
            ->latest()
            ->take(5)
            ->get();

        // Passer les données à la vue
        return view('gestionnaire.dashboard', compact(
            'ongoingOrdersCount',
            'validatedOrdersCount',
            'dailyRevenue',
            'ordersPerMonthData',
            'productsPerCategory',
            'productsPerBurger', // Ajout des données pour la quantité par burger
            'recentOrders'
        ));
    }
}
