<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Models\Burger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Applique le middleware 'auth' à toutes les méthodes
        $this->middleware('auth');
        // Applique le middleware 'role:gestionnaire' à toutes les méthodes sauf 'index'
        $this->middleware('role:gestionnaire')->except('index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role->name === 'gestionnaire') {
            // Les gestionnaires voient tous les paiements
            $payments = Payment::with('order')->get();
            return view('gestionnaire.payments.index', compact('payments'));
        } else {
            // Les clients voient uniquement les paiements liés à leurs commandes
            $payments = Payment::whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with('order')->get();
            return view('payments.index', compact('payments'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orders = Order::whereDoesntHave('payment')->get();
        return view('gestionnaire.payments.create', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id|unique:payments,order_id',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:en_attente,réussi,échoué',
        ]);

        $validated['payment_date'] = now();

        Payment::create($validated);

        return redirect()->route('payments.index')->with('success', 'Paiement enregistré avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
