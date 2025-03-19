<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Burger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('burgers')->get();
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $burgers = Burger::where('stock', '>', 0)->get();
        return view('orders.create', compact('burgers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'burgers' => 'required|array',
            'burgers.*.burger_id' => 'required|exists:burgers,id',
            'burgers.*.quantity' => 'required|integer|min:1',
        ]);

        $total = 0;
        $order = Order::create([
            'user_id' => Auth::id() ?? null,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'status' => 'en_attente',
            'total' => 0,
        ]);

        foreach ($validated['burgers'] as $item) {
            $burger = Burger::find($item['burger_id']);
            if ($burger && $burger->stock >= $item['quantity']) {
                $order->burgers()->attach($burger->id, [
                    'quantity' => $item['quantity'],
                    'unit_price' => $burger->price,
                ]);
                $total += $burger->price * $item['quantity'];
                $burger->stock -= $item['quantity'];
                $burger->save();
            }
        }

        $order->update(['total' => $total]);
        return redirect()->route('orders.index')->with('success', 'Commande créée avec succès.');
    }

    public function show(Order $order)
    {
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:en_attente,en_preparation,prete,payee',
        ]);

        $order->update($validated);
        return redirect()->route('orders.index')->with('success', 'Statut mis à jour.');
    }

    public function destroy(Order $order)
    {
        $order->burgers()->detach();
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Commande supprimée.');
    }
}
