<?php

namespace App\Http\Controllers;

use App\Models\Burger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer les catégories uniques des burgers
        $categories = Burger::select('category')
            ->distinct()
            ->pluck('category')
            ->toArray();

        if (empty($categories)) {
            $categories = ['Burger', 'Pizza', 'Hot dog', 'Chicken', 'Ice', 'Potato', 'Donuts', 'Fuchka'];
        }

        // Construire la requête pour les burgers avec filtres
        $query = Burger::where('stock', '>', 0)
            ->where('archived', false);

        // Filtre par libellé (nom du burger)
        if ($request->has('name') && !empty($request->input('name'))) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // Filtre par prix (plage de prix)
        if ($request->has('price_min') && !empty($request->input('price_min'))) {
            $query->where('price', '>=', $request->input('price_min'));
        }
        if ($request->has('price_max') && !empty($request->input('price_max'))) {
            $query->where('price', '<=', $request->input('price_max'));
        }

        // Récupérer les burgers avec les filtres appliqués
        $burgers = $query->get();

        // Burgers populaires (par exemple, les plus commandés)
        $popularBurgers = Burger::select('burgers.*')
            ->join('order_burger', 'burgers.id', '=', 'order_burger.burger_id')
            ->join('orders', 'order_burger.order_id', '=', 'orders.id')
            ->where('orders.status', 'Payée')
            ->groupBy('burgers.id')
            ->orderByRaw('SUM(order_burger.quantity) DESC')
            ->take(3)
            ->get();

        // Burgers récents (par exemple, les derniers ajoutés)
        $recentBurgers = Burger::where('stock', '>', 0)
            ->where('archived', false)
            ->latest()
            ->take(3)
            ->get();

        // Simuler une liste de souhaits (wishlist)
        $wishlist = Session::get('wishlist', []);

        // Calculer les totaux pour le panier
        $cart = Session::get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $tax = $subtotal * 0.05; // Taxe de 5%
        $total = $subtotal + $tax;

        return view('welcome', compact(
            'categories',
            'burgers',
            'popularBurgers',
            'recentBurgers',
            'wishlist',
            'subtotal',
            'tax',
            'total'
        ));
    }
}
