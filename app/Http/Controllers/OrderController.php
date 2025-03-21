<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Burger;
use App\Models\Payment;
use App\Notifications\OrderReadyNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('burgers', 'user')->latest()->paginate(10);
        return view('gestionnaire.orders.index', compact('orders'));
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
            'status' => 'En attente',
            'total_amount' => 0,
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

        $order->update(['total_amount' => $total]);
        return redirect()->route('orders.index')->with('success', 'Commande créée avec succès.');
    }

    public function show(Order $order)
    {
        $order->load('burgers', 'user');
        return view('gestionnaire.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:En attente,En préparation,Prête,Payée,Annulée'],
        ]);

        $oldStatus = $order->status;
        $order->status = $validated['status'];
        $order->save();

        // Si le statut passe à "Prête", envoyer un email avec la facture en PDF
        if ($validated['status'] === 'Prête' && $oldStatus !== 'Prête') {
            // Générer la facture PDF
            $pdf = PDF::loadView('gestionnaire.orders.invoice', compact('order'));
            $pdfPath = storage_path('app/public/invoices/invoice-' . $order->id . '.pdf');
            $pdf->save($pdfPath);

            // Envoyer la notification
            if ($order->user) {
                $order->user->notify(new OrderReadyNotification($order, $pdfPath));
            }
        }

        // Si le statut passe à "Payée", enregistrer le paiement
        if ($validated['status'] === 'Payée' && $oldStatus !== 'Payée') {
            Payment::create([
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'payment_date' => now(),
                'payment_method' => 'Espèces',
            ]);
        }

        return redirect()->back()->with('success', 'Statut de la commande mis à jour avec succès.');
    }

    public function destroy(Order $order)
    {
        // Au lieu de supprimer, on annule la commande
        $order->status = 'Annulée';
        $order->save();
        return redirect()->route('orders.index')->with('success', 'Commande annulée avec succès.');
    }
}
