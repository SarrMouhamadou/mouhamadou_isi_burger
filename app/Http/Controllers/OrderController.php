<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Burger;
use App\Models\Payment;
use App\Notifications\OrderReadyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use PDF;

class OrderController extends Controller
{
    public function __construct()
    {
        // Applique le middleware 'auth' à toutes les méthodes
        $this->middleware('auth');
        // Applique le middleware 'role:gestionnaire' à toutes les méthodes sauf 'place', 'index', et 'show'
        $this->middleware('role:gestionnaire')->except(['place', 'index', 'show']);
    }

    /**
     * Display a listing of the orders.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role->name === 'gestionnaire') {
            // Les gestionnaires voient toutes les commandes
            $orders = Order::with('burgers', 'user')->latest()->paginate(10);
            return view('gestionnaire.orders.index', compact('orders'));
        } else {
            // Les clients voient uniquement leurs propres commandes
            $orders = $user->orders()->with('burgers')->latest()->paginate(10);
            return view('orders.index', compact('orders'));
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur a le droit de voir cette commande
        if ($user->role->name !== 'gestionnaire' && $order->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $order->load('burgers', 'user', 'payment');

        if ($user->role->name === 'gestionnaire') {
            return view('gestionnaire.orders.show', compact('order'));
        } else {
            return view('orders.show', compact('order'));
        }
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:En attente,En préparation,Prête,Payée,Annulée',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        // Si la commande est marquée comme "Payée", mettre à jour payment_date
        if ($request->status === 'Payée' && $order->payment) {
            $order->payment->update([
                'status' => 'Payée',
                'payment_date' => now(),
            ]);
        }

        // Si la commande est marquée comme "Prête", notifier le client
        if ($request->status === 'Prête' && $order->user) {
            $order->user->notify(new OrderReadyNotification($order));
        }

        return redirect()->route('orders.index')->with('success', 'Statut de la commande mis à jour avec succès.');
    }

    /**
     * Place a new order (for authenticated clients).
     */
    public function place(Request $request)
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'Votre panier est vide.');
        }

        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        $user = Auth::user();

        try {
            $order = DB::transaction(function () use ($user, $cart, $totalAmount) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'total_amount' => $totalAmount,
                    'status' => 'En attente',
                ]);

                foreach ($cart as $id => $item) {
                    $order->burgers()->attach($id, [
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                    ]);
                }

                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $totalAmount,
                    'status' => 'En attente',
                    'payment_date' => null,
                ]);

                return $order;
            });

            Session::forget('cart');

            return redirect()->route('orders.index')->with('success', 'Commande passée avec succès !');
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', 'Une erreur s\'est produite lors de la création de la commande : ' . $e->getMessage());
        }
    }

    /**
     * Update the status of the specified order via AJAX.
     */
    public function updateStatus(Request $request, Order $order)
    {
        try {
            $request->validate([
                'status' => 'required|in:En attente,En préparation,Prête,Payée,Annulée',
            ]);

            $order->update([
                'status' => $request->status,
            ]);

            // Si la commande est marquée comme "Payée", mettre à jour payment_date
            if ($request->status === 'Payée' && $order->payment) {
                $order->payment->update([
                    'status' => 'Payée',
                    'payment_date' => now(),
                ]);
            }

            // Si la commande est marquée comme "Prête", notifier le client
            if ($request->status === 'Prête' && $order->user) {
                $order->user->notify(new OrderReadyNotification($order));
            }

            return response()->json(['success' => true]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour du statut de la commande : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Une erreur s\'est produite lors de la mise à jour du statut.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
